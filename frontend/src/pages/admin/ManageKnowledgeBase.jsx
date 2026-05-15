import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, X, Check, Brain, Search, Eye, EyeOff, RefreshCcw } from 'lucide-react';
import api from '../../api';

const ManageKnowledgeBase = () => {
    const [entries, setEntries] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [currentEntry, setCurrentEntry] = useState(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [pageError, setPageError] = useState('');
    const [status, setStatus] = useState({ type: '', text: '' });
    const [showWebsiteContext, setShowWebsiteContext] = useState(false);
    const [websiteContext, setWebsiteContext] = useState('');
    const [websiteContextLoading, setWebsiteContextLoading] = useState(false);

    const initialFormState = {
        question: '',
        answer: '',
        content: '',
        is_active: true
    };
    const [formData, setFormData] = useState(initialFormState);

    const setSuccess = (text) => setStatus({ type: 'success', text });
    const setError = (text) => setStatus({ type: 'error', text });

    const fetchEntries = async () => {
        setLoading(true);
        setPageError('');
        try {
            const { data } = await api.get('/admin/knowledge-bases');
            setEntries(data);
        } catch (e) {
            console.error(e);
            setPageError('Unable to load knowledge base entries.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchEntries();
    }, []);

    const handleInputChange = (e) => {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value;
        setFormData({ ...formData, [e.target.name]: value });
    };

    const fetchWebsiteContext = async () => {
        setWebsiteContextLoading(true);
        try {
            const { data } = await api.get('/admin/knowledge-bases/context');
            setWebsiteContext(data.context || '');
        } catch (e) {
            console.error(e);
            setError('Could not load website context.');
        } finally {
            setWebsiteContextLoading(false);
        }
    };

    const handleEdit = (entry) => {
        setCurrentEntry(entry.id);
        setFormData({
            question: entry.question,
            answer: entry.answer,
            content: entry.content || '',
            is_active: !!entry.is_active,
        });
        setIsEditing(true);
    };

    const handleAddNew = () => {
        setCurrentEntry(null);
        setFormData(initialFormState);
        setIsEditing(true);
    };

    const handleCancel = () => {
        setIsEditing(false);
        setCurrentEntry(null);
        setFormData(initialFormState);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            if (currentEntry) {
                await api.put(`/admin/knowledge-bases/${currentEntry}`, formData);
                setSuccess('Knowledge entry updated.');
            } else {
                await api.post('/admin/knowledge-bases', formData);
                setSuccess('Knowledge entry added.');
            }
            await fetchEntries();
            setIsEditing(false);
            setCurrentEntry(null);
            setFormData(initialFormState);
        } catch (err) {
            console.error(err);
            const message = err.response?.data?.message || 'Failed to save knowledge base entry.';
            setError(message);
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm("Are you sure you want to delete this entry? The chatbot will no longer know this information.")) return;
        try {
            await api.delete(`/admin/knowledge-bases/${id}`);
            await fetchEntries();
            setSuccess('Knowledge entry deleted.');
        } catch (e) {
            console.error(e);
            setError('Failed to delete knowledge entry.');
        }
    };

    const handleToggleActive = async (entry) => {
        try {
            await api.put(`/admin/knowledge-bases/${entry.id}`, { is_active: !entry.is_active });
            setEntries((prev) =>
                prev.map((item) =>
                    item.id === entry.id ? { ...item, is_active: !item.is_active } : item
                )
            );
        } catch (e) {
            console.error(e);
            setError('Failed to update active status.');
        }
    };

    const toggleWebsiteContext = async () => {
        const nextValue = !showWebsiteContext;
        setShowWebsiteContext(nextValue);
        if (nextValue && !websiteContext) {
            await fetchWebsiteContext();
        }
    };

    const filteredEntries = entries.filter((entry) => {
        const text = `${entry.question} ${entry.answer} ${entry.content || ''}`.toLowerCase();
        return text.includes(searchTerm.toLowerCase());
    });

    const inputClass = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg px-4 py-2.5 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500";
    const labelClass = "block text-xs font-semibold text-charcoal-600 mb-2 tracking-wide uppercase";

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1 flex items-center gap-2">
                        <Brain className="w-6 h-6 text-brand-600" /> AI Knowledge Base
                    </h1>
                    <p className="text-charcoal-400 text-sm">Manage the data used by your AI assistant: add, edit, remove, or disable entries.</p>
                </div>
                <div className="flex items-center gap-2">
                    <button
                        onClick={toggleWebsiteContext}
                        className="btn-secondary flex items-center gap-2 !text-sm !py-2 !px-4"
                    >
                        {showWebsiteContext ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                        {showWebsiteContext ? 'Hide Website Context' : 'Show Website Context'}
                    </button>
                    {!isEditing && (
                        <button onClick={handleAddNew} className="btn-primary flex items-center gap-2 !text-sm !py-2 !px-4 hover:-translate-y-1 transition-transform">
                            <Plus className="w-4 h-4" /> Add Memory
                        </button>
                    )}
                </div>
            </div>

            {status.text && (
                <div className={`mb-6 p-3 rounded-lg border text-sm ${status.type === 'error' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200'}`}>
                    {status.text}
                </div>
            )}

            {pageError && (
                <div className="mb-6 p-3 rounded-lg border bg-red-50 text-red-700 border-red-200 text-sm">
                    {pageError}
                </div>
            )}

            {showWebsiteContext && (
                <div className="mb-8 bg-white border border-charcoal-100 rounded-2xl p-5">
                    <div className="flex items-center justify-between mb-3">
                        <h2 className="text-lg font-display font-bold text-charcoal-950">Website Context Used by AI</h2>
                        <button
                            onClick={fetchWebsiteContext}
                            className="btn-secondary !text-xs !py-1.5 !px-3 flex items-center gap-2"
                        >
                            <RefreshCcw className="w-3.5 h-3.5" /> Refresh
                        </button>
                    </div>
                    <p className="text-xs text-charcoal-500 mb-3">
                        This context is generated from your services and pricing data.
                    </p>
                    {websiteContextLoading ? (
                        <div className="flex justify-center py-8">
                            <div className="w-6 h-6 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div>
                        </div>
                    ) : (
                        <pre className="bg-charcoal-50 border border-charcoal-100 rounded-xl p-4 text-xs leading-relaxed whitespace-pre-wrap max-h-72 overflow-auto">
                            {websiteContext || 'No website context available.'}
                        </pre>
                    )}
                </div>
            )}

            {isEditing ? (
                <div className="bg-white border border-charcoal-100 rounded-2xl p-6 shadow-sm overflow-hidden">
                    <div className="flex justify-between items-center mb-8 pb-4 border-b border-charcoal-100">
                        <h2 className="text-xl font-display font-bold text-charcoal-950">{currentEntry ? 'Edit Knowledge' : 'Add New Knowledge'}</h2>
                        <button onClick={handleCancel} className="p-2 text-charcoal-400 hover:text-charcoal-900 hover:bg-charcoal-50 rounded-lg transition-colors"><X className="w-5 h-5" /></button>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label className={labelClass}>Expected Question / Topic *</label>
                            <input type="text" name="question" required value={formData.question} onChange={handleInputChange} className={inputClass} placeholder="e.g. What is your refund policy?" />
                        </div>

                        <div>
                            <label className={labelClass}>AI's Answer / Summary *</label>
                            <textarea
                                name="answer"
                                required
                                rows="4"
                                value={formData.answer}
                                onChange={handleInputChange}
                                className={`${inputClass} resize-none`}
                                placeholder="Short, direct answer the AI should give when this question is asked."
                            ></textarea>
                        </div>

                        <div>
                            <label className={labelClass}>Content Writing Box (Full Context)</label>
                            <textarea
                                name="content"
                                rows="8"
                                value={formData.content}
                                onChange={handleInputChange}
                                className={`${inputClass} resize-none`}
                                placeholder="Write or paste detailed content here (paragraphs, bullet points, notes). The AI will read this to answer related questions."
                            ></textarea>
                            <p className="text-xs text-charcoal-400 mt-2">
                                This larger content box is ideal for long explanations, policy text, service details, or any reference material you want the AI to use.
                            </p>
                        </div>

                        <div className="flex items-center gap-2 mt-4 p-4 bg-charcoal-50 rounded-xl border border-charcoal-100">
                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                checked={formData.is_active}
                                onChange={handleInputChange}
                                className="rounded border-charcoal-300 text-brand-500 focus:ring-brand-500/40 w-4 h-4"
                            />
                            <label htmlFor="is_active" className="text-sm font-medium text-charcoal-700 cursor-pointer">Active in AI Brain</label>
                            <span className="text-xs text-charcoal-400 ml-2">(Uncheck to hide this from the AI without deleting it)</span>
                        </div>

                        {/* Actions */}
                        <div className="flex justify-end gap-3 pt-6 border-t border-charcoal-100">
                            <button type="button" onClick={handleCancel} className="px-6 py-2.5 rounded-lg text-charcoal-600 text-sm font-medium hover:bg-charcoal-50 transition-colors">Cancel</button>
                            <button type="submit" disabled={submitting} className="btn-primary flex items-center gap-2 !text-sm !py-2.5 !px-8 hover:-translate-y-1 transition-transform shadow-lg shadow-brand-500/20 disabled:opacity-70 disabled:cursor-not-allowed">
                                <Check className="w-4 h-4" /> {submitting ? 'Saving...' : 'Save Knowledge'}
                            </button>
                        </div>
                    </form>
                </div>
            ) : loading ? (
                <div className="flex justify-center p-20"><div className="w-8 h-8 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>
            ) : (
                <>
                    <div className="mb-5">
                        <div className="relative max-w-md">
                            <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-charcoal-400" />
                            <input
                                type="text"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                placeholder="Search question, answer, or content..."
                                className="w-full bg-white border border-charcoal-200 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500"
                            />
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {filteredEntries.map(entry => (
                        <div key={entry.id} className="bg-white border border-charcoal-100 rounded-2xl p-5 hover:shadow-lg hover:shadow-charcoal-900/5 transition-all flex flex-col h-full relative group">
                            <div className="flex justify-between items-start mb-3">
                                <h3 className="text-sm font-bold text-charcoal-900 line-clamp-2 pr-4">{entry.question}</h3>
                                <button
                                    type="button"
                                    onClick={() => handleToggleActive(entry)}
                                    className={`px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide ${entry.is_active ? 'bg-green-50 text-green-700' : 'bg-charcoal-100 text-charcoal-600'}`}
                                    title={entry.is_active ? 'Click to deactivate' : 'Click to activate'}
                                >
                                    {entry.is_active ? 'Active' : 'Inactive'}
                                </button>
                            </div>

                            <p className="text-charcoal-500 text-sm mb-3 line-clamp-3 whitespace-pre-wrap leading-relaxed">
                                {entry.answer}
                            </p>
                            {entry.content && (
                                <p className="text-charcoal-400 text-xs mb-6 line-clamp-3 whitespace-pre-wrap leading-relaxed bg-charcoal-50 p-3 rounded-xl border border-charcoal-100/50">
                                    {entry.content}
                                </p>
                            )}

                            <div className="flex gap-2 pt-4 border-t border-charcoal-50">
                                <button onClick={() => handleEdit(entry)} className="flex-1 justify-center btn-secondary !text-xs !py-2 flex items-center gap-2 group-hover:bg-charcoal-100"><Edit2 className="w-3.5 h-3.5" /> Edit</button>
                                <button onClick={() => handleDelete(entry.id)} className="flex justify-center items-center p-2 rounded-lg bg-white border border-charcoal-200 text-charcoal-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors"><Trash2 className="w-4 h-4" /></button>
                            </div>
                        </div>
                    ))}
                    {filteredEntries.length === 0 && (
                        <div className="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-charcoal-200">
                            <div className="w-16 h-16 rounded-full bg-charcoal-50 flex items-center justify-center mb-4"><Brain className="w-8 h-8 text-charcoal-300" /></div>
                            <h3 className="text-lg font-bold text-charcoal-900 mb-1">{entries.length === 0 ? 'AI Head is Empty' : 'No Matching Entries'}</h3>
                            <p className="text-charcoal-500 text-sm mb-6 text-center max-w-sm">
                                {entries.length === 0
                                    ? 'The chatbot has no knowledge entries yet. Add your first one now.'
                                    : 'No entry matched your search. Try another keyword.'}
                            </p>
                            <button onClick={handleAddNew} className="btn-primary !text-sm">Add First Memory</button>
                        </div>
                    )}
                    </div>
                </>
            )}
        </div>
    );
};

export default ManageKnowledgeBase;
