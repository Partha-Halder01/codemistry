import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import api from '../api';

const Login = () => {
    const [formData, setFormData] = useState({ phone: '', password: '' });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const navigate = useNavigate();

    const handleChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value });

    const handleLoginSuccess = (data) => {
        localStorage.setItem('auth_token', data.access_token);
        localStorage.setItem('user', JSON.stringify(data.user));

        if (data.user.role === 'admin') {
            navigate('/admin/dashboard');
        } else {
            navigate('/dashboard');
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        try {
            const response = await api.post('/login', formData);
            handleLoginSuccess(response.data);
        } catch (err) {
            setError(err.response?.data?.message || 'Invalid phone number or password');
        } finally {
            setLoading(false);
        }
    };

    const inputClass = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg px-4 py-3 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all placeholder:text-charcoal-300";

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
                <div className="text-center mb-8">
                    <div className="mx-auto w-12 h-12 rounded-xl bg-charcoal-950 flex items-center justify-center mb-5">
                        <span className="text-white font-display font-bold text-2xl">C</span>
                    </div>
                    <h2 className="text-2xl font-display font-bold text-charcoal-950">Admin Login</h2>
                    <p className="mt-2 text-sm text-charcoal-500">
                        Use your admin username and password to access the dashboard.
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-5">
                    {error && <div className="p-3 rounded-lg bg-red-50 text-red-700 border border-red-100 text-sm">{error}</div>}

                    <div>
                        <label htmlFor="phone" className="block text-xs font-medium text-charcoal-600 mb-1">Phone Number or Admin Username</label>
                        <input id="phone" name="phone" type="text" required value={formData.phone} onChange={handleChange} className={inputClass} placeholder="Enter phone or admin username" />
                    </div>
                    <div>
                        <label htmlFor="password" className="block text-xs font-medium text-charcoal-600 mb-1">Password</label>
                        <input id="password" name="password" type="password" required value={formData.password} onChange={handleChange} className={inputClass} placeholder="Enter your password" />
                    </div>

                    <div className="flex items-center justify-between text-sm">
                        <label className="flex items-center gap-2 text-charcoal-500">
                            <input type="checkbox" className="rounded border-charcoal-300" /> Remember me
                        </label>
                        <a href="#" className="text-brand-600 hover:text-brand-700 font-medium">Forgot password?</a>
                    </div>

                    <button type="submit" disabled={loading}
                        className={`w-full btn-primary ${loading ? 'opacity-70 cursor-not-allowed' : ''}`}>
                        {loading ? 'Signing in...' : 'Sign in'}
                    </button>
                </form>
            </div>
        </div>
    );
};

export default Login;
