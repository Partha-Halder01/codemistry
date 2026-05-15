import { useState, useEffect } from 'react';
import { Mail, Grid, MessageSquare, Users, Brain } from 'lucide-react';
import { Link } from 'react-router-dom';
import api from '../../api';

const AdminDashboard = () => {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/admin/stats').then(r => setStats(r.data)).catch(console.error).finally(() => setLoading(false));
    }, []);

    if (loading) return <div className="flex justify-center items-center h-64"><div className="w-8 h-8 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>;

    const cards = [
        { label: "Today's Chats", value: stats?.today_chats || 0, icon: MessageSquare, bg: 'bg-green-50 text-green-600' },
        { label: "Today's Visitors", value: stats?.today_visitors || 0, icon: Users, bg: 'bg-brand-50 text-brand-600' },
        { label: 'Services', value: stats?.services || 0, icon: Grid, bg: 'bg-amber-50 text-amber-600' },
        { label: 'Messages', value: stats?.tickets || 0, icon: Mail, bg: 'bg-orange-50 text-orange-600' },
    ];

    return (
        <div>
            <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1">Overview</h1>
            <p className="text-charcoal-400 text-sm mb-8">Welcome to your admin panel.</p>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-12">
                {cards.map((c, i) => {
                    const Icon = c.icon;
                    return (
                        <div key={i} className="bg-white border border-charcoal-100 rounded-2xl p-5 hover:shadow-md transition-shadow">
                            <div className={`w-10 h-10 rounded-xl flex items-center justify-center mb-4 ${c.bg}`}><Icon className="w-5 h-5" /></div>
                            <div className="text-2xl font-display font-bold text-charcoal-950">{c.value}</div>
                            <div className="text-charcoal-400 text-xs font-medium mt-1">{c.label}</div>
                        </div>
                    );
                })}
            </div>

            <h2 className="text-lg font-display font-bold text-charcoal-950 mb-4">Quick Actions</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                <Link to="/admin/services" className="group bg-white border border-charcoal-100 rounded-2xl p-5 hover:shadow-md transition-shadow flex items-start gap-4">
                    <div className="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0"><Grid className="w-5 h-5" /></div>
                    <div>
                        <h3 className="font-bold text-charcoal-950 mb-1">Manage Services</h3>
                        <p className="text-charcoal-400 text-sm">Add, edit, or remove services from your website.</p>
                    </div>
                </Link>
                <Link to="/admin/messages" className="group bg-white border border-charcoal-100 rounded-2xl p-5 hover:shadow-md transition-shadow flex items-start gap-4">
                    <div className="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0"><Mail className="w-5 h-5" /></div>
                    <div>
                        <h3 className="font-bold text-charcoal-950 mb-1">Read Messages</h3>
                        <p className="text-charcoal-400 text-sm">View inquiries from the contact form.</p>
                    </div>
                </Link>
                <Link to="/admin/knowledge-base" className="group bg-white border border-charcoal-100 rounded-2xl p-5 hover:shadow-md transition-shadow flex items-start gap-4">
                    <div className="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0"><Brain className="w-5 h-5" /></div>
                    <div>
                        <h3 className="font-bold text-charcoal-950 mb-1">Manage AI Knowledge Base</h3>
                        <p className="text-charcoal-400 text-sm">Add, edit, remove, and activate the AI data entries.</p>
                    </div>
                </Link>
            </div>
        </div>
    );
};

export default AdminDashboard;
