<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\AnalyticsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

Route::post('/analytics/track', [AnalyticsController::class, 'track']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store']);
Route::post('/chat', [\App\Http\Controllers\ChatbotController::class, 'chat']);

// Public Blog
Route::get('/blog-posts', [\App\Http\Controllers\BlogPostController::class, 'index']);
Route::get('/blog-posts/latest', [\App\Http\Controllers\BlogPostController::class, 'latest']);
Route::get('/blog-posts/{slug}', [\App\Http\Controllers\BlogPostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/google/complete', [AuthController::class, 'completeGoogleRegistration']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/stats', [\App\Http\Controllers\AdminController::class, 'getStats']);
    Route::get('/analytics', [\App\Http\Controllers\AdminAnalyticsController::class, 'getDashboardData']);
    Route::get('/ai-chats', [\App\Http\Controllers\AdminChatHistoryController::class, 'index']);

    Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index']);

    // Admin Service Management
    Route::post('/services', [\App\Http\Controllers\AdminServiceController::class, 'store']);
    Route::put('/services/{id}', [\App\Http\Controllers\AdminServiceController::class, 'update']);
    Route::patch('/services/{id}/toggle-featured', [\App\Http\Controllers\ServiceController::class, 'toggleFeatured']);
    Route::delete('/services/{id}', [\App\Http\Controllers\AdminServiceController::class, 'destroy']);
    
    // Admin Orders Management
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'adminIndex']); 

    // Admin Knowledge Base Management
    Route::get('/knowledge-bases/context', [\App\Http\Controllers\KnowledgeBaseController::class, 'getWebsiteContext']);
    Route::get('/knowledge-bases', [\App\Http\Controllers\KnowledgeBaseController::class, 'index']);
    Route::post('/knowledge-bases', [\App\Http\Controllers\KnowledgeBaseController::class, 'store']);
    Route::get('/knowledge-bases/{id}', [\App\Http\Controllers\KnowledgeBaseController::class, 'show']);
    Route::put('/knowledge-bases/{id}', [\App\Http\Controllers\KnowledgeBaseController::class, 'update']);
    Route::delete('/knowledge-bases/{id}', [\App\Http\Controllers\KnowledgeBaseController::class, 'destroy']);

    // Admin Blog Management
    Route::get('/blog-posts', [\App\Http\Controllers\AdminBlogPostController::class, 'index']);
    Route::post('/blog-posts', [\App\Http\Controllers\AdminBlogPostController::class, 'store']);
    Route::get('/blog-posts/{id}', [\App\Http\Controllers\AdminBlogPostController::class, 'show']);
    Route::put('/blog-posts/{id}', [\App\Http\Controllers\AdminBlogPostController::class, 'update']);
    Route::post('/blog-posts/{id}', [\App\Http\Controllers\AdminBlogPostController::class, 'update']); // multipart fallback w/ _method=PUT
    Route::patch('/blog-posts/{id}/toggle-publish', [\App\Http\Controllers\AdminBlogPostController::class, 'togglePublish']);
    Route::delete('/blog-posts/{id}', [\App\Http\Controllers\AdminBlogPostController::class, 'destroy']);
});
