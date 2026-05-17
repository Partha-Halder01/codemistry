import { Link, useLocation } from 'react-router-dom';
import { Home, Briefcase, Bot, Phone } from 'lucide-react';

const MobileBottomBar = () => {
    const location = useLocation();

    const userRole = (() => {
        try {
            const raw = localStorage.getItem('user');
            return raw ? JSON.parse(raw)?.role : null;
        } catch { return null; }
    })();
    const accountRoute = userRole === 'admin' ? '/admin/dashboard' : '/dashboard';

    const navItems = [
        { to: '/', label: 'Home', icon: Home },
        { to: '/services', label: 'Services', icon: Briefcase },
        { to: '/ai-support', label: 'AI Support', icon: Bot },
        { to: '/contact', label: 'Contact', icon: Phone },
    ];

    return (
        <div className="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-charcoal-100 shadow-[0_-4px_20px_-10px_rgba(0,0,0,0.1)] z-40 pb-safe">
            <div className="flex justify-around items-center h-16 px-2">
                {navItems.map((item, index) => {
                    const isActive = location.pathname === item.to;
                    const Icon = item.icon;

                    return (
                        <Link
                            key={index}
                            to={item.to}
                            className={`flex flex-col items-center justify-center w-full h-full space-y-1 transition-colors ${isActive
                                ? 'text-charcoal-950 font-semibold'
                                : 'text-charcoal-500 hover:text-charcoal-950'
                                }`}
                        >
                            <div className={`${isActive ? 'bg-brand-50 text-brand-600 p-1.5 rounded-xl' : 'p-1.5'}`}>
                                <Icon className={`w-5 h-5 ${isActive ? 'text-brand-600' : ''}`} />
                            </div>
                            <span className={`text-[10px] ${isActive ? 'font-semibold text-charcoal-950' : 'font-medium'}`}>
                                {item.label}
                            </span>
                        </Link>
                    )
                })}
            </div>
        </div>
    );
};

export default MobileBottomBar;
