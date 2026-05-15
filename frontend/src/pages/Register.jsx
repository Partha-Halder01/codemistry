import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ArrowLeft, Phone } from 'lucide-react';
import { GoogleLogin } from '@react-oauth/google';
import api from '../api';

const Register = () => {
    const [formData, setFormData] = useState({ name: '', phone: '', email: '', password: '', password_confirmation: '' });
    const [errors, setErrors] = useState({});
    const [generalError, setGeneralError] = useState('');
    const [loading, setLoading] = useState(false);

    // State for completing Google Registration
    const [requiresPhone, setRequiresPhone] = useState(false);
    const [googleTempToken, setGoogleTempToken] = useState('');
    const [googlePhone, setGooglePhone] = useState('');

    const navigate = useNavigate();

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
        if (errors[e.target.name]) setErrors({ ...errors, [e.target.name]: null });
    };

    const handleLoginSuccess = (data) => {
        localStorage.setItem('auth_token', data.access_token);
        localStorage.setItem('user', JSON.stringify(data.user));

        if (data.user?.role === 'admin') {
            navigate('/admin/dashboard');
        } else {
            navigate('/dashboard');
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true); setErrors({}); setGeneralError('');
        try {
            const response = await api.post('/register', formData);
            handleLoginSuccess(response.data);
        } catch (err) {
            if (err.response?.status === 422) setErrors(err.response.data.errors);
            else setGeneralError('Registration failed. Please try again later.');
        } finally { setLoading(false); }
    };

    const handleGoogleSuccess = async (credentialResponse) => {
        setLoading(true);
        setGeneralError('');
        try {
            const response = await api.post('/auth/google', {
                credential: credentialResponse.credential,
            });

            if (response.data.requires_phone) {
                // Must collect phone number before full login
                setGoogleTempToken(response.data.temp_token);
                setRequiresPhone(true);
            } else {
                handleLoginSuccess(response.data);
            }
        } catch (err) {
            console.error("Google login error:", err);
            setGeneralError(err.response?.data?.message || 'Google Sign-In failed');
        } finally {
            setLoading(false);
        }
    };

    const handleCompleteGoogleRegistration = async (e) => {
        e.preventDefault();
        setLoading(true);
        setGeneralError('');
        setErrors({});
        try {
            // Use the temporary token for this specific request
            const response = await api.post('/auth/google/complete', { phone: googlePhone }, {
                headers: {
                    Authorization: `Bearer ${googleTempToken}`
                }
            });

            // Now fully logged in
            handleLoginSuccess(response.data);
            setRequiresPhone(false);
        } catch (err) {
            if (err.response?.status === 422) setErrors(err.response.data.errors);
            else setGeneralError(err.response?.data?.message || 'Failed to complete registration');
        } finally {
            setLoading(false);
        }
    };

    const inputClass = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg px-4 py-3 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all placeholder:text-charcoal-300";

    const fields = [
        { id: 'name', label: 'Full Name', type: 'text', placeholder: 'John Doe', required: true },
        { id: 'phone', label: 'Phone Number', type: 'tel', placeholder: '+91 9876543210', required: true },
        { id: 'email', label: 'Email Address (Optional)', type: 'email', placeholder: 'john@example.com' },
        { id: 'password', label: 'Password', type: 'password', placeholder: '••••••••', required: true },
        { id: 'password_confirmation', label: 'Confirm Password', type: 'password', placeholder: '••••••••', required: true },
    ];

    return (
        <div className="min-h-screen bg-white flex flex-col items-center justify-center p-6 relative">
            {/* Back Button */}
            <Link to="/" className="absolute top-8 left-8 flex items-center gap-2 text-charcoal-500 hover:text-charcoal-950 transition-colors font-medium text-sm group">
                <div className="w-8 h-8 rounded-full bg-charcoal-50 flex items-center justify-center group-hover:bg-charcoal-100 transition-colors">
                    <ArrowLeft className="w-4 h-4" />
                </div>
                Back to Home
            </Link>

            <div className="max-w-md w-full bg-white border border-charcoal-100 rounded-2xl p-10 shadow-sm relative overflow-hidden">
                {/* Complete Profile Overlay built inside card */}
                {requiresPhone && (
                    <div className="absolute inset-0 bg-white z-20 flex flex-col justify-center p-10 animate-fade-in">
                        <div className="text-center mb-6">
                            <div className="mx-auto w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mb-4 text-brand-500">
                                <Phone className="w-6 h-6" />
                            </div>
                            <h2 className="text-2xl font-display font-bold text-charcoal-950">Almost there!</h2>
                            <p className="mt-2 text-sm text-charcoal-500">
                                Codemistry requires a valid phone number to secure your account.
                            </p>
                        </div>

                        <form onSubmit={handleCompleteGoogleRegistration} className="space-y-4">
                            {generalError && <div className="p-3 rounded-lg bg-red-50 text-red-700 border border-red-100 text-sm">{generalError}</div>}

                            <div>
                                <label className="block text-xs font-medium text-charcoal-600 mb-1">Phone Number</label>
                                <input
                                    type="text"
                                    required
                                    value={googlePhone}
                                    onChange={(e) => setGooglePhone(e.target.value)}
                                    className={inputClass}
                                    placeholder="Enter your phone number"
                                />
                                {errors.phone && <p className="mt-1 text-xs text-red-500">{errors.phone[0]}</p>}
                            </div>

                            <button type="submit" disabled={loading}
                                className={`w-full btn-primary ${loading ? 'opacity-70 cursor-not-allowed' : ''}`}>
                                {loading ? 'Saving...' : 'Complete Account'}
                            </button>

                            <button
                                type="button"
                                onClick={() => setRequiresPhone(false)}
                                className="w-full mt-2 py-2 text-sm font-medium text-charcoal-500 hover:text-charcoal-950 transition-colors"
                            >
                                Cancel
                            </button>
                        </form>
                    </div>
                )}


                <div className="text-center mb-8">
                    <h2 className="text-2xl font-display font-bold text-charcoal-950">Create Account</h2>
                    <p className="mt-2 text-sm text-charcoal-500">
                        Already have an account?{' '}
                        <Link to="/login" className="font-medium text-brand-600 hover:text-brand-700">Sign in</Link>
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {generalError && !requiresPhone && <div className="p-3 rounded-lg bg-red-50 text-red-700 border border-red-100 text-sm">{generalError}</div>}

                    {fields.map((f) => (
                        <div key={f.id}>
                            <label htmlFor={f.id} className="block text-xs font-medium text-charcoal-600 mb-1">{f.label}</label>
                            <input id={f.id} name={f.id} type={f.type} required={f.required} value={formData[f.id]} onChange={handleChange} className={inputClass} placeholder={f.placeholder} />
                            {errors[f.id] && <p className="mt-1 text-xs text-red-500">{errors[f.id][0]}</p>}
                        </div>
                    ))}

                    <button type="submit" disabled={loading}
                        className={`w-full btn-primary mt-2 ${loading ? 'opacity-70 cursor-not-allowed' : ''}`}>
                        {loading ? 'Creating Account...' : 'Create Account'}
                    </button>
                </form>

                <div className="mt-6 flex items-center">
                    <div className="flex-1 shrink-0 bg-charcoal-100 h-px"></div>
                    <span className="px-4 text-xs font-medium text-charcoal-400 uppercase tracking-widest">Or continue with</span>
                    <div className="flex-1 shrink-0 bg-charcoal-100 h-px"></div>
                </div>

                <div className="mt-6 flex justify-center">
                    <GoogleLogin
                        onSuccess={handleGoogleSuccess}
                        onError={() => {
                            setGeneralError('Google Sign-In failed');
                        }}
                        useOneTap={false}
                        theme="outline"
                        size="large"
                        shape="pill"
                        text="signup_with"
                        width="100%"
                    />
                </div>
            </div>
        </div>
    );
};

export default Register;
