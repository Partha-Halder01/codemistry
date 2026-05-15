import { useState, useEffect } from 'react';
import { MessageSquare, Clock, Users, Database } from 'lucide-react';
import api from '../../api';

const ManageAiChats = () => {
    const [data, setData] = useState({ summary: {}, logs: [] });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/admin/ai-chats')
            .then(res => setData(res.data))
            .catch(console.error)
            .finally(() => setLoading(false));
    }, []);

    const { summary, logs } = data;

    return (
        <div>
            <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1">AI Chat History</h1>
            <p className="text-charcoal-400 text-sm mb-8">View recent interactions with the AI assistant.</p>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="bg-white p-6 rounded-2xl border border-charcoal-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                        <MessageSquare className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-charcoal-500">Total Chats</p>
                        <p className="text-2xl font-bold text-charcoal-950">{summary.total_chats || 0}</p>
                    </div>
                </div>

                <div className="bg-white p-6 rounded-2xl border border-charcoal-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                        <Clock className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-charcoal-500">Chats Today</p>
                        <p className="text-2xl font-bold text-charcoal-950">{summary.today_chats || 0}</p>
                    </div>
                </div>

                <div className="bg-white p-6 rounded-2xl border border-charcoal-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <Users className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-charcoal-500">Unique Sessions</p>
                        <p className="text-2xl font-bold text-charcoal-950">{summary.total_sessions || 0}</p>
                    </div>
                </div>
            </div>

            {loading ? (
                <div className="flex justify-center p-12"><div className="w-8 h-8 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>
            ) : (
                <div className="bg-white border border-charcoal-100 rounded-2xl overflow-hidden shadow-sm">
                    {logs.length > 0 ? (
                        <div className="divide-y divide-charcoal-100">
                            {logs.map((log) => (
                                <div key={log.id} className="p-6 hover:bg-charcoal-50/50 transition-colors">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="flex items-center gap-2 text-xs font-medium text-charcoal-500 bg-charcoal-50 px-3 py-1.5 rounded-lg">
                                            <Database className="w-3.5 h-3.5" />
                                            Session: {log.session_id.substring(0, 12)}...
                                        </div>
                                        <div className="text-xs text-charcoal-400">
                                            {log.formatted_date}
                                        </div>
                                    </div>

                                    <div className="space-y-4">
                                        <div className="flex gap-4">
                                            <div className="w-8 h-8 rounded-full bg-charcoal-100 flex items-center justify-center text-charcoal-600 shrink-0 mt-1">
                                                <Users className="w-4 h-4" />
                                            </div>
                                            <div className="flex-1 bg-charcoal-50 rounded-2xl rounded-tl-none p-4 mr-12 text-sm text-charcoal-700 border border-charcoal-100">
                                                {log.user_message}
                                            </div>
                                        </div>

                                        <div className="flex gap-4 flex-row-reverse">
                                            <div className="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 shrink-0 mt-1">
                                                <MessageSquare className="w-4 h-4" />
                                            </div>
                                            <div className="flex-1 bg-brand-50/50 rounded-2xl rounded-tr-none p-4 ml-12 text-sm text-charcoal-800 border border-brand-100 whitespace-pre-wrap">
                                                {log.ai_response}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="p-12 text-center text-charcoal-400 flex flex-col items-center">
                            <MessageSquare className="w-10 h-10 text-charcoal-200 mb-3" />
                            <p>No chat history available.</p>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default ManageAiChats;
