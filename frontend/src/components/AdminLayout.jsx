import { Outlet, Link, useNavigate, useLocation } from 'react-router-dom';
import { LayoutDashboard, Settings, Mail, LogOut, Brain, LineChart, MessageSquare, BookOpen } from 'lucide-react';
import api from '../api';

const AdminLayout = () => {
    const navigate = useNavigate();
    const location = useLocation();

    const handleLogout = async () => {
        try { await api.post('/logout'); } catch (e) { /* ignore */ }
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        navigate('/login');
    };

    const navItems = [
        { path: '/admin/dashboard', icon: LayoutDashboard, label: 'Overview' },
        { path: '/admin/analytics', icon: LineChart, label: 'Analytics' },
        { path: '/admin/services', icon: Settings, label: 'Services' },
        { path: '/admin/blog-posts', icon: BookOpen, label: 'Blog Posts' },
        { path: '/admin/messages', icon: Mail, label: 'Messages' },
        { path: '/admin/knowledge-base', icon: Brain, label: 'AI Brain' },
        { path: '/admin/ai-chats', icon: MessageSquare, label: 'AI Chats' },
    ];

    return (
        <div className="min-h-screen bg-charcoal-50 flex">
            <aside className="w-64 bg-white border-r border-charcoal-100 flex-col hidden md:flex">
                <div className="p-6 border-b border-charcoal-100 flex flex-col items-center">
                    <div className="w-10 h-10 rounded-lg bg-charcoal-950 flex items-center justify-center mb-3">
                        <span className="text-white font-display font-bold text-lg">C</span>
                    </div>
                    <span className="text-lg font-display font-bold text-charcoal-950">Codemistry</span>
                    <span className="text-xs font-medium text-brand-600 tracking-wider uppercase mt-1">Admin Panel</span>
                </div>
                <nav className="flex-1 p-4 space-y-1">
                    {navItems.map((item) => {
                        const Icon = item.icon;
                        const isActive = location.pathname.startsWith(item.path);
                        return (
                            <Link key={item.path} to={item.path}
                                className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-sm font-medium ${isActive ? 'bg-charcoal-950 text-white' : 'text-charcoal-500 hover:bg-charcoal-50 hover:text-charcoal-950'}`}>
                                <Icon className="w-5 h-5" />
                                {item.label}
                            </Link>
                        );
                    })}
                </nav>
                <div className="p-4 border-t border-charcoal-100">
                    <button onClick={handleLogout} className="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-colors text-sm font-medium">
                        <LogOut className="w-5 h-5" /> Logout
                    </button>
                </div>
            </aside>

            <div className="md:hidden fixed bottom-4 left-4 right-4 bg-white/90 backdrop-blur-lg border border-charcoal-100/50 shadow-2xl shadow-charcoal-900/10 z-50 rounded-2xl p-2 flex justify-between items-center px-2">
                {navItems.map(item => {
                    const Icon = item.icon;
                    const isActive = location.pathname.startsWith(item.path);
                    return (
                        <Link
                            key={item.path}
                            to={item.path}
                            className={`flex flex-col items-center gap-1 p-2 min-w-[64px] rounded-xl transition-all duration-300 ${isActive ? 'text-brand-600' : 'text-charcoal-400 hover:text-charcoal-600'}`}
                        >
                            <div className={`p-2 rounded-xl transition-all duration-300 ${isActive ? 'bg-brand-50 shadow-sm scale-110' : 'bg-transparent'}`}>
                                <Icon className="w-5 h-5" />
                            </div>
                            <span className={`text-[10px] font-medium transition-all duration-300 ${isActive ? 'opacity-100 max-h-4 mt-0.5' : 'opacity-0 max-h-0 overflow-hidden'}`}>
                                {item.label}
                            </span>
                        </Link>
                    );
                })}
            </div>

            <main className="flex-1 overflow-y-auto">
                <div className="min-h-full p-4 md:p-8 lg:p-12 pb-24 md:pb-12">
                    <Outlet />
                </div>
            </main>
        </div>
    );
};

export default AdminLayout;
