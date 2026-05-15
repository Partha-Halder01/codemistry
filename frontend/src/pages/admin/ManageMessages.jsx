import { useState, useEffect } from 'react';
import { Mail, Clock, Calendar } from 'lucide-react';
import api from '../../api';

const ManageMessages = () => {
    const [messages, setMessages] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/admin/tickets').then(r => setMessages(r.data)).catch(console.error).finally(() => setLoading(false));
    }, []);

    return (
        <div>
            <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1">Messages</h1>
            <p className="text-charcoal-400 text-sm mb-8">Inquiries from the Contact form.</p>

            {loading ? (
                <div className="flex justify-center p-12"><div className="w-8 h-8 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>
            ) : (
                <div className="bg-white border border-charcoal-100 rounded-2xl overflow-hidden">
                    {messages.length > 0 ? (
                        <div className="divide-y divide-charcoal-100">
                            {messages.map((msg) => (
                                <div key={msg.id} className="p-5 hover:bg-charcoal-50/50 transition-colors">
                                    <div className="flex flex-col sm:flex-row justify-between items-start gap-3 mb-3">
                                        <div className="flex items-center gap-3">
                                            <div className="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 shrink-0"><Mail className="w-5 h-5" /></div>
                                            <div>
                                                <h3 className="font-bold text-charcoal-950">{msg.name}</h3>
                                                <div className="flex flex-wrap items-center gap-x-3 text-xs text-charcoal-400">
                                                    <span>{msg.email || 'No email'}</span>
                                                    <span>•</span>
                                                    <span>{msg.phone}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-1.5 text-xs text-charcoal-400 bg-charcoal-50 px-2.5 py-1 rounded-lg shrink-0">
                                            <Calendar className="w-3.5 h-3.5" />
                                            {new Date(msg.created_at).toLocaleDateString()}
                                        </div>
                                    </div>
                                    <div className="bg-charcoal-50 rounded-xl p-4 text-charcoal-600 text-sm whitespace-pre-wrap border-l-2 border-l-brand-400">{msg.message}</div>
                                    <div className="mt-2.5 flex gap-2">
                                        <span className="px-2 py-0.5 text-xs font-medium rounded bg-charcoal-100 text-charcoal-500">{msg.ticket_uid}</span>
                                        <span className={`px-2 py-0.5 text-xs font-medium rounded ${msg.status === 'open' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'}`}>{msg.status}</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="p-12 text-center text-charcoal-400 flex flex-col items-center">
                            <Mail className="w-10 h-10 text-charcoal-200 mb-3" />
                            <p>No messages yet.</p>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default ManageMessages;
