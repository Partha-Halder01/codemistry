<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Google\Client;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => clone collect($user)->put('role', 'user')
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->phone;

        // Try Admin login first
        $admin = AdminUser::where('username', $identifier)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            $token = $admin->createToken('admin_token')->plainTextToken;
            
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $admin->id,
                    'name' => 'Administrator',
                    'username' => $admin->username,
                    'role' => 'admin'
                ]
            ]);
        }

        // Try regular User login
        $user = User::where('phone', $identifier)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => 'user'
                ]
            ]);
        }
        
        throw ValidationException::withMessages([
            'phone' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'credential' => 'nullable|string',
            'client_id' => 'nullable|string',
        ]);

        $idToken = $request->credential;
        $clientId = env('GOOGLE_CLIENT_ID', '662928908213-shsupdsrkpibvge022000rst2qc9dii0.apps.googleusercontent.com'); // Fallback to provided ID

        $client = new Client(['client_id' => $clientId]);
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            return response()->json(['message' => 'Invalid Google token'], 401);
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];
        // $picture = $payload['picture'];

        // Check if user already exists based on email or google_id
        $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();

        if ($user) {
            // Update google_id if it was missing 
            if (!$user->google_id) {
                $user->update(['google_id' => $googleId]);
            }

            // Check if they have a phone number. If not, they must complete registration.
            if (empty($user->phone)) {
                 $tempToken = $user->createToken('temp_auth_token', ['google-registration'])->plainTextToken;
                 return response()->json([
                     'requires_phone' => true,
                     'temp_token' => $tempToken,
                     'message' => 'Please provide a phone number to complete registration.',
                     'user' => [
                         'name' => $user->name,
                         'email' => $user->email,
                     ]
                 ]);
            }

            // Fully logged in
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => 'user'
                ]
            ]);
        }

        // User does not exist, create a partial profile and ask for phone number
        $newUser = User::create([
            'name' => $name,
            'email' => $email,
            'google_id' => $googleId,
            'password' => null, // No password for Google signups
            'phone' => null, // Needs to be filled
        ]);

        $tempToken = $newUser->createToken('temp_auth_token', ['google-registration'])->plainTextToken;

        return response()->json([
            'requires_phone' => true,
            'temp_token' => $tempToken,
            'message' => 'Account created! Please provide a phone number to complete registration.',
            'user' => [
                'name' => $newUser->name,
                'email' => $newUser->email,
            ]
        ], 201);
    }

    public function completeGoogleRegistration(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20|unique:users',
        ]);

        $user = $request->user();

        // Ensure user actually needs to complete registration
        if (!$user->tokenCan('google-registration') && !empty($user->phone)) {
            return response()->json(['message' => 'User already has a phone number.'], 400);
        }

        $user->update([
            'phone' => $request->phone
        ]);

        // Revoke the temporary token
        $user->currentAccessToken()->delete();

        // Issue a full auth token
        $fullToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $fullToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => 'user'
            ]
        ]);
    }
}
