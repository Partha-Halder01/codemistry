import { useState, useEffect, useMemo } from 'react';
import { Plus, Edit2, Trash2, X, Eye, EyeOff, Tag, Calendar, Search } from 'lucide-react';
import api from '../../api';
import { sanitizeBlogHtml, scopeBlogCss } from '../../utils/sanitizeBlog';

const initialFormState = {
    title: '',
    slug: '',
    excerpt: '',
    cover_image: null,
    cover_image_path: '',
    content_html: '',
    content_css: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    tags: '', // comma-separated string in form, sent as JSON array
    author_name: 'Codemistry Team',
    status: 'draft',
    published_at: '',
};

const formatDate = (d) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' }); }
    catch { return '—'; }
};

const slugify = (s) =>
    (s || '')
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

const ManageBlogPosts = () => {
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isEditing, setIsEditing] = useState(false);
    const [currentId, setCurrentId] = useState(null);
    const [formData, setFormData] = useState(initialFormState);
    const [showPreview, setShowPreview] = useState(false);
    const [search, setSearch] = useState('');
    const [submitError, setSubmitError] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    const fetchPosts = async () => {
        setLoading(true);
        try {
            const { data } = await api.get('/admin/blog-posts');
            setPosts(data);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    useEffect(() => { fetchPosts(); }, []);

    const handleAddNew = () => {
        setCurrentId(null);
        setFormData(initialFormState);
        setSubmitError(null);
        setIsEditing(true);
    };

    const handleEdit = (post) => {
        setCurrentId(post.id);
        setFormData({
            ...initialFormState,
            title: post.title || '',
            slug: post.slug || '',
            excerpt: post.excerpt || '',
            cover_image_path: post.cover_image_path || '',
            cover_image: null,
            content_html: post.content_html || '',
            content_css: post.content_css || '',
            meta_title: post.meta_title || '',
            meta_description: post.meta_description || '',
            meta_keywords: post.meta_keywords || '',
            tags: Array.isArray(post.tags) ? post.tags.join(', ') : '',
            author_name: post.author_name || 'Codemistry Team',
            status: post.status || 'draft',
            published_at: post.published_at ? new Date(post.published_at).toISOString().slice(0, 16) : '',
        });
        setSubmitError(null);
        setIsEditing(true);
    };

    const handleCancel = () => {
        setIsEditing(false);
        setCurrentId(null);
        setSubmitError(null);
    };

    const handleField = (field, value) => {
        setFormData((f) => {
            const next = { ...f, [field]: value };
            if (field === 'title' && (!f.slug || f.slug === slugify(f.title))) {
                next.slug = slugify(value);
            }
            return next;
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setSubmitError(null);
        try {
            const fd = new FormData();
            fd.append('title', formData.title);
            if (formData.slug) fd.append('slug', formData.slug);
            if (formData.excerpt) fd.append('excerpt', formData.excerpt);
            fd.append('content_html', formData.content_html);
            if (formData.content_css) fd.append('content_css', formData.content_css);
            if (formData.meta_title) fd.append('meta_title', formData.meta_title);
            if (formData.meta_description) fd.append('meta_description', formData.meta_description);
            if (formData.meta_keywords) fd.append('meta_keywords', formData.meta_keywords);
            if (formData.author_name) fd.append('author_name', formData.author_name);
            fd.append('status', formData.status);
            if (formData.published_at) fd.append('published_at', formData.published_at);

            const tagArr = (formData.tags || '')
                .split(',').map(t => t.trim()).filter(Boolean);
            fd.append('tags', JSON.stringify(tagArr));

            if (formData.cover_image instanceof File) {
                fd.append('cover_image', formData.cover_image);
            }

            const config = { headers: { 'Content-Type': 'multipart/form-data' } };
            if (currentId) {
                fd.append('_method', 'PUT');
                await api.post(`/admin/blog-posts/${currentId}`, fd, config);
            } else {
                await api.post('/admin/blog-posts', fd, config);
            }

            await fetchPosts();
            setIsEditing(false);
        } catch (err) {
            console.error(err.response?.data || err);
            const msg = err.response?.data?.message || err.message;
            const errors = err.response?.data?.errors;
            setSubmitError(errors ? `${msg}\n${JSON.stringify(errors, null, 2)}` : msg);
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('Delete this blog post? This cannot be undone.')) return;
        try {
            await api.delete(`/admin/blog-posts/${id}`);
            fetchPosts();
        } catch (e) { console.error(e); }
    };

    const handleTogglePublish = async (id) => {
        try {
            await api.patch(`/admin/blog-posts/${id}/toggle-publish`);
            fetchPosts();
        } catch (e) { console.error(e); }
    };

    const previewHtml = useMemo(() => sanitizeBlogHtml(formData.content_html), [formData.content_html]);
    const previewScope = `cm-blog-preview`;
    const previewCss = useMemo(() => scopeBlogCss(formData.content_css || '', previewScope), [formData.content_css]);

    const filtered = useMemo(() => {
        const q = search.trim().toLowerCase();
        if (!q) return posts;
        return posts.filter(p =>
            (p.title || '').toLowerCase().includes(q) ||
            (p.slug || '').toLowerCase().includes(q)
        );
    }, [posts, search]);

    const inputClass = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg px-4 py-2.5 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500";
    const labelClass = "block text-xs font-semibold text-charcoal-600 mb-2 tracking-wide uppercase";
    const monoTextarea = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg p-4 text-charcoal-900 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500";

    const apiBase = api.defaults.baseURL.replace('/api', '');

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1">Manage Blog Posts</h1>
                    <p className="text-charcoal-400 text-sm">Write SEO-friendly posts in raw HTML &amp; CSS. Each post's CSS is scoped — it won't break other pages.</p>
                </div>
                {!isEditing && (
                    <button onClick={handleAddNew} className="btn-primary flex items-center gap-2 !text-sm !py-2 !px-4 hover:-translate-y-1 transition-transform">
                        <Plus className="w-4 h-4" /> New Post
                    </button>
                )}
            </div>

            {isEditing ? (
                <form onSubmit={handleSubmit} className="bg-white border border-charcoal-100 rounded-2xl p-6 shadow-sm">
                    <div className="flex justify-between items-center mb-6 pb-4 border-b border-charcoal-100">
                        <h2 className="text-xl font-display font-bold text-charcoal-950">{currentId ? 'Edit Post' : 'New Post'}</h2>
                        <div className="flex items-center gap-2">
                            <button type="button" onClick={() => setShowPreview(p => !p)} className="text-sm font-semibold text-brand-600 hover:text-brand-700 px-3 py-2 rounded-lg hover:bg-brand-50 transition">
                                {showPreview ? 'Hide preview' : 'Show preview'}
                            </button>
                            <button type="button" onClick={handleCancel} className="p-2 text-charcoal-400 hover:text-charcoal-900 hover:bg-charcoal-50 rounded-lg"><X className="w-5 h-5" /></button>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {/* Left column — meta + cover */}
                        <div className="space-y-5">
                            <div>
                                <label className={labelClass}>Title *</label>
                                <input className={inputClass} value={formData.title} onChange={e => handleField('title', e.target.value)} required />
                            </div>
                            <div>
                                <label className={labelClass}>Slug</label>
                                <input className={inputClass} value={formData.slug} onChange={e => handleField('slug', slugify(e.target.value))} placeholder="auto from title" />
                            </div>
                            <div>
                                <label className={labelClass}>Excerpt (1-2 sentences)</label>
                                <textarea className={inputClass} rows="3" value={formData.excerpt} onChange={e => handleField('excerpt', e.target.value)} />
                            </div>
                            <div>
                                <label className={labelClass}>Cover image (max 5MB)</label>
                                <input type="file" accept="image/*" className="text-sm" onChange={e => handleField('cover_image', e.target.files?.[0] || null)} />
                                {formData.cover_image_path && !formData.cover_image && (
                                    <img src={`${apiBase}/storage/${formData.cover_image_path}`} alt="cover" className="mt-3 h-32 rounded-lg border border-charcoal-100 object-cover" />
                                )}
                                {formData.cover_image instanceof File && (
                                    <p className="text-xs text-charcoal-500 mt-2">Selected: {formData.cover_image.name}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className={labelClass}>Author</label>
                                    <input className={inputClass} value={formData.author_name} onChange={e => handleField('author_name', e.target.value)} />
                                </div>
                                <div>
                                    <label className={labelClass}>Status</label>
                                    <select className={inputClass} value={formData.status} onChange={e => handleField('status', e.target.value)}>
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Publish date (optional)</label>
                                <input type="datetime-local" className={inputClass} value={formData.published_at} onChange={e => handleField('published_at', e.target.value)} />
                            </div>
                            <div>
                                <label className={labelClass}>Tags (comma separated)</label>
                                <input className={inputClass} value={formData.tags} onChange={e => handleField('tags', e.target.value)} placeholder="Web Development, India, Pricing" />
                            </div>
                        </div>

                        {/* Right column — SEO meta */}
                        <div className="space-y-5">
                            <div>
                                <label className={labelClass}>Meta title <span className="text-charcoal-400 normal-case">({(formData.meta_title || '').length}/60)</span></label>
                                <input className={inputClass} maxLength={70} value={formData.meta_title} onChange={e => handleField('meta_title', e.target.value)} placeholder="Falls back to post title" />
                            </div>
                            <div>
                                <label className={labelClass}>Meta description <span className="text-charcoal-400 normal-case">({(formData.meta_description || '').length}/160)</span></label>
                                <textarea className={inputClass} rows="3" maxLength={200} value={formData.meta_description} onChange={e => handleField('meta_description', e.target.value)} />
                            </div>
                            <div>
                                <label className={labelClass}>Meta keywords</label>
                                <input className={inputClass} value={formData.meta_keywords} onChange={e => handleField('meta_keywords', e.target.value)} placeholder="comma, separated, keywords" />
                            </div>

                            <div className="bg-brand-50/40 border border-brand-100 rounded-xl p-4 text-xs text-charcoal-600 leading-relaxed">
                                <strong className="text-charcoal-800">SEO tips for the Indian market:</strong>
                                <ul className="list-disc list-inside mt-1 space-y-0.5">
                                    <li>Include "India" or a city name in the meta title.</li>
                                    <li>Mention INR pricing, GST, UPI or Razorpay if relevant.</li>
                                    <li>Keep meta description under 160 characters.</li>
                                    <li>Use 3–6 specific tags — they help internal linking.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {/* Content editors — full width */}
                    <div className="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label className={labelClass}>Content HTML *</label>
                            <textarea
                                className={monoTextarea}
                                rows="20"
                                value={formData.content_html}
                                onChange={e => handleField('content_html', e.target.value)}
                                placeholder={'<p class="lede">Lede paragraph...</p>\n<h2>Heading</h2>\n<p>Body text...</p>'}
                                required
                            />
                        </div>
                        <div>
                            <label className={labelClass}>Custom CSS (auto-scoped to this post)</label>
                            <textarea
                                className={monoTextarea}
                                rows="20"
                                value={formData.content_css}
                                onChange={e => handleField('content_css', e.target.value)}
                                placeholder={'.lede { font-size: 1.05rem; color: #374151; }\nh2 { margin-top: 1.5rem; }'}
                            />
                        </div>
                    </div>

                    {showPreview && (
                        <div className="mt-6">
                            <div className="text-xs font-semibold text-charcoal-600 mb-2 tracking-wide uppercase">Live preview</div>
                            <div className="border border-charcoal-200 rounded-2xl p-6 bg-white">
                                {previewCss && <style dangerouslySetInnerHTML={{ __html: previewCss }} />}
                                <h2 className="text-2xl font-display font-bold mb-4">{formData.title || 'Untitled'}</h2>
                                <div className={`blog-content ${previewScope}`} dangerouslySetInnerHTML={{ __html: previewHtml }} />
                            </div>
                        </div>
                    )}

                    {submitError && (
                        <pre className="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-xs whitespace-pre-wrap">{submitError}</pre>
                    )}

                    <div className="flex justify-end gap-3 mt-8 pt-6 border-t border-charcoal-100">
                        <button type="button" onClick={handleCancel} className="px-5 py-2.5 text-sm font-semibold text-charcoal-700 bg-white border border-charcoal-200 rounded-lg hover:bg-charcoal-50">Cancel</button>
                        <button type="submit" disabled={submitting} className="px-5 py-2.5 text-sm font-semibold text-white bg-brand-500 rounded-lg hover:bg-brand-600 disabled:opacity-60">
                            {submitting ? 'Saving…' : (currentId ? 'Save changes' : 'Create post')}
                        </button>
                    </div>
                </form>
            ) : (
                <>
                    <div className="bg-white border border-charcoal-100 rounded-xl p-3 mb-4 flex items-center gap-2">
                        <Search className="w-4 h-4 text-charcoal-400 ml-1" />
                        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Search posts..." className="flex-1 bg-transparent outline-none text-sm" />
                    </div>

                    <div className="bg-white border border-charcoal-100 rounded-2xl shadow-sm overflow-hidden">
                        {loading ? (
                            <div className="p-10 text-center text-charcoal-500 text-sm">Loading...</div>
                        ) : filtered.length === 0 ? (
                            <div className="p-10 text-center text-charcoal-500 text-sm">No posts yet. Click "New Post" to write your first article.</div>
                        ) : (
                            <table className="w-full text-sm">
                                <thead className="bg-charcoal-50/60 text-charcoal-600 text-xs uppercase tracking-wide">
                                    <tr>
                                        <th className="text-left p-4">Title</th>
                                        <th className="text-left p-4 w-28">Status</th>
                                        <th className="text-left p-4 w-32">Published</th>
                                        <th className="text-left p-4 w-20">Views</th>
                                        <th className="text-right p-4 w-44">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filtered.map((p) => (
                                        <tr key={p.id} className="border-t border-charcoal-100 hover:bg-charcoal-50/40">
                                            <td className="p-4">
                                                <div className="font-semibold text-charcoal-900">{p.title}</div>
                                                <div className="text-xs text-charcoal-400 flex items-center gap-2 mt-0.5">
                                                    <span>/{p.slug}</span>
                                                    {Array.isArray(p.tags) && p.tags.slice(0, 2).map((t) => (
                                                        <span key={t} className="px-1.5 py-0.5 bg-brand-50 text-brand-700 rounded-full text-[10px]"><Tag className="inline w-2.5 h-2.5 -mt-0.5 mr-0.5" />{t}</span>
                                                    ))}
                                                </div>
                                            </td>
                                            <td className="p-4">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-semibold ${p.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-charcoal-100 text-charcoal-600'}`}>
                                                    {p.status}
                                                </span>
                                            </td>
                                            <td className="p-4 text-charcoal-600 text-xs">
                                                <span className="flex items-center gap-1"><Calendar className="w-3 h-3" /> {formatDate(p.published_at)}</span>
                                            </td>
                                            <td className="p-4 text-charcoal-600 text-xs">{p.view_count ?? 0}</td>
                                            <td className="p-4 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    <button onClick={() => handleTogglePublish(p.id)} className="p-2 text-charcoal-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg" title={p.status === 'published' ? 'Unpublish' : 'Publish'}>
                                                        {p.status === 'published' ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                                                    </button>
                                                    <button onClick={() => handleEdit(p)} className="p-2 text-charcoal-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit"><Edit2 className="w-4 h-4" /></button>
                                                    <button onClick={() => handleDelete(p.id)} className="p-2 text-charcoal-500 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete"><Trash2 className="w-4 h-4" /></button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </>
            )}
        </div>
    );
};

export default ManageBlogPosts;
