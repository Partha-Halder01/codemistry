import { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../api';
import { LogOut, Package, CreditCard, User as UserIcon, Bot, ArrowRight } from 'lucide-react';

const Dashboard = () => {
    const [user, setUser] = useState(null);
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate();

    useEffect(() => {
        const fetchData = async () => {
            try {
                const userData = localStorage.getItem('user');
                if (!userData) { navigate('/login'); return; }
                setUser(JSON.parse(userData));
                const response = await api.get('/orders');
                setOrders(response.data);
            } catch (error) {
                if (error.response?.status === 401) { localStorage.removeItem('auth_token'); localStorage.removeItem('user'); navigate('/login'); }
            } finally { setLoading(false); }
        };
        fetchData();
    }, [navigate]);

    const handleLogout = async () => {
        try { await api.post('/logout'); } catch (e) { /* ignore */ }
        localStorage.removeItem('auth_token'); localStorage.removeItem('user'); navigate('/login');
    };

    if (loading) return <div className="min-h-screen bg-white flex justify-center items-center"><div className="w-10 h-10 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>;

    return (
        <div className="min-h-screen bg-charcoal-50 pt-24 pb-12 px-4">
            <div className="max-w-7xl mx-auto">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1">My Dashboard</h1>
                        <p className="text-charcoal-500 text-sm">Welcome back, {user?.name}</p>
                    </div>
                    <button onClick={handleLogout} className="flex items-center gap-2 px-4 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium">
                        <LogOut className="w-4 h-4" /> Logout
                    </button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Column (Profile & AI) */}
                    <div className="flex flex-col gap-6">
                        {/* Profile */}
                        <div className="bg-white border border-charcoal-100 rounded-2xl p-6">
                            <div className="flex items-center gap-4 mb-5 pb-5 border-b border-charcoal-100">
                                <div className="w-14 h-14 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                                    <UserIcon className="w-7 h-7" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-charcoal-950 flex items-center flex-wrap gap-2">
                                        {user?.name}
                                        {user?.role === 'admin' && <span className="px-2 py-0.5 rounded text-xs font-medium bg-brand-50 text-brand-700">Admin</span>}
                                    </h3>
                                    <p className="text-sm text-charcoal-500 mt-1">{user?.phone || user?.username || user?.email}</p>
                                </div>
                            </div>
                            <a href="#orders" className="flex items-center gap-3 px-4 py-3 rounded-xl bg-charcoal-50 text-charcoal-700 hover:bg-charcoal-100 transition-colors font-medium text-sm">
                                <Package className="w-5 h-5" /> My Orders
                            </a>
                        </div>

                        {/* AI Support Card */}
                        <Link
                            to="/ai-support"
                            className="bg-gradient-to-br from-brand-50 to-white hover:from-brand-100 hover:to-brand-50 border border-brand-100 hover:border-brand-200 transition-all rounded-2xl p-6 relative overflow-hidden group text-left w-full shadow-sm hover:shadow-md block"
                        >
                            <div className="absolute top-0 right-0 p-4 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500">
                                <Bot className="w-24 h-24 text-brand-600" />
                            </div>
                            <div className="relative z-10">
                                <h3 className="text-lg font-display font-bold text-charcoal-950 mb-2 flex items-center gap-2">
                                    <Bot className="w-5 h-5 text-brand-600" /> AI Support Assitant
                                </h3>
                                <p className="text-sm text-charcoal-600 mb-5 leading-relaxed pr-6">
                                    Have a question? Chat directly with our intelligent AI support agent for instant, real-time help.
                                </p>
                                <span className="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-semibold group-hover:bg-brand-700 transition-colors shadow-sm">
                                    Start Chat <ArrowRight className="w-4 h-4 ml-0.5 group-hover:translate-x-1 transition-transform" />
                                </span>
                            </div>
                        </Link>
                    </div>

                    {/* Orders */}
                    <div id="orders" className="lg:col-span-2 bg-white border border-charcoal-100 rounded-2xl p-6">
                        <h2 className="text-lg font-display font-bold text-charcoal-950 mb-5 flex items-center gap-2">
                            <CreditCard className="w-5 h-5 text-brand-600" /> Recent Orders
                        </h2>
                        {orders.length > 0 ? (
                            <div className="space-y-3">
                                {orders.map((order) => (
                                    <div key={order.id} className="p-4 rounded-xl border border-charcoal-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 hover:bg-charcoal-50/50 transition-colors">
                                        <div>
                                            <div className="flex items-center gap-2 mb-1">
                                                <h4 className="text-charcoal-950 font-medium text-sm">{order.service?.name || 'Service Package'}</h4>
                                                <span className="px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">{order.status}</span>
                                            </div>
                                            <p className="text-xs text-charcoal-400">Order ID: {order.order_uid}</p>
                                        </div>
                                        <div className="text-left sm:text-right">
                                            <div className="text-charcoal-950 font-medium text-sm">₹{(order.amount_paid / 100).toLocaleString('en-IN')}</div>
                                            <div className="text-xs text-charcoal-400">{new Date(order.created_at).toLocaleDateString()}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12 border border-dashed border-charcoal-200 rounded-xl">
                                <Package className="w-10 h-10 text-charcoal-300 mx-auto mb-3" />
                                <h3 className="font-medium text-charcoal-950 mb-1">No orders yet</h3>
                                <p className="text-charcoal-400 text-sm mb-4">You haven't purchased any services yet.</p>
                                <a href="/services" className="btn-primary !text-sm !py-2 !px-5">Browse Services</a>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
