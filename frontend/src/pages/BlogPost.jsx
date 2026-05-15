import React, { useEffect, useState, useMemo } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { Calendar, User, Tag, ArrowLeft, Loader2, Share2 } from 'lucide-react';
import api from '../api';
import Seo from '../components/Seo';
import { sanitizeBlogHtml, scopeBlogCss } from '../utils/sanitizeBlog';
import { articleLd, breadcrumbLd, organizationLd, SITE_INFO } from '../seo/structuredData';

const formatDate = (d) => {
    if (!d) return '';
    try {
        return new Date(d).toLocaleDateString('en-IN', { year: 'numeric', month: 'long', day: 'numeric' });
    } catch { return ''; }
};

const BlogPostPage = () => {
    const { slug } = useParams();
    const navigate = useNavigate();
    const [post, setPost] = useState(null);
    const [related, setRelated] = useState([]);
    const [loading, setLoading] = useState(true);
    const [notFound, setNotFound] = useState(false);

    useEffect(() => {
        let cancelled = false;
        setLoading(true);
        setNotFound(false);
        api.get(`/blog-posts/${slug}`)
            .then(r => {
                if (cancelled) return;
                setPost(r.data.post);
                setRelated(r.data.related || []);
            })
            .catch(e => {
                if (cancelled) return;
                if (e.response?.status === 404) setNotFound(true);
                else console.error(e);
            })
            .finally(() => { if (!cancelled) setLoading(false); });
        return () => { cancelled = true; };
    }, [slug]);

    const scopeClass = useMemo(() => post ? `cm-blog-${post.id}` : '', [post]);
    const cleanHtml = useMemo(() => post ? sanitizeBlogHtml(post.content_html) : '', [post]);
    const scopedCss = useMemo(() => post ? scopeBlogCss(post.content_css || '', scopeClass) : '', [post, scopeClass]);

    const onShare = async () => {
        const url = window.location.href;
        if (navigator.share) {
            try { await navigator.share({ title: post.title, url }); } catch {}
        } else {
            try {
                await navigator.clipboard.writeText(url);
                alert('Link copied to clipboard');
            } catch {}
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center py-32">
                <Loader2 className="w-8 h-8 text-brand-500 animate-spin" />
            </div>
        );
    }

    if (notFound || !post) {
        return (
            <div className="max-w-3xl mx-auto px-4 py-32 text-center">
                <h1 className="text-3xl font-display font-bold text-charcoal-900 mb-3">Post not found</h1>
                <p className="text-charcoal-500 mb-6">The article you're looking for doesn't exist or has been moved.</p>
                <button onClick={() => navigate('/blog')} className="px-5 py-3 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-semibold">
                    Back to Blog
                </button>
            </div>
        );
    }

    const canonical = `${SITE_INFO.url}/blog/${post.slug}`;

    return (
        <>
            <Seo
                title={post.meta_title || post.title}
                description={post.meta_description || post.excerpt || ''}
                canonical={canonical}
                ogImage={post.cover_image_url || undefined}
                keywords={post.meta_keywords || (Array.isArray(post.tags) ? post.tags.join(', ') : '')}
                type="article"
                jsonLd={[
                    organizationLd(),
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'Blog', url: SITE_INFO.url + '/blog' },
                        { name: post.title, url: canonical },
                    ]),
                    articleLd(post, canonical),
                ]}
            />

            {scopedCss && <style dangerouslySetInnerHTML={{ __html: scopedCss }} />}

            <article className="max-w-3xl mx-auto px-4 sm:px-6 py-12">
                <Link to="/blog" className="inline-flex items-center gap-1 text-charcoal-500 hover:text-brand-600 text-sm mb-6">
                    <ArrowLeft className="w-4 h-4" /> Back to Blog
                </Link>

                <header className="mb-8">
                    <h1 className="text-3xl sm:text-4xl lg:text-5xl font-display font-bold text-charcoal-900 leading-tight">
                        {post.title}
                    </h1>

                    {post.excerpt && (
                        <p className="mt-4 text-lg text-charcoal-600 leading-relaxed">{post.excerpt}</p>
                    )}

                    <div className="mt-6 flex flex-wrap items-center gap-4 text-sm text-charcoal-500">
                        <span className="flex items-center gap-1.5"><Calendar className="w-4 h-4" /> {formatDate(post.published_at)}</span>
                        <span className="flex items-center gap-1.5"><User className="w-4 h-4" /> {post.author_name}</span>
                        {Array.isArray(post.tags) && post.tags.map((t) => (
                            <Link key={t} to={`/blog?tag=${encodeURIComponent(t)}`} className="text-[12px] px-2 py-0.5 bg-brand-50 text-brand-700 rounded-full hover:bg-brand-100">
                                <Tag className="w-2.5 h-2.5 inline -mt-0.5 mr-1" />{t}
                            </Link>
                        ))}
                        <button onClick={onShare} className="ml-auto flex items-center gap-1.5 text-charcoal-600 hover:text-brand-600">
                            <Share2 className="w-4 h-4" /> Share
                        </button>
                    </div>
                </header>

                {post.cover_image_url && (
                    <div className="rounded-2xl overflow-hidden mb-10 border border-charcoal-100">
                        <img src={post.cover_image_url} alt={post.title} className="w-full h-auto object-cover" />
                    </div>
                )}

                <div
                    className={`blog-content ${scopeClass}`}
                    dangerouslySetInnerHTML={{ __html: cleanHtml }}
                />

                <div className="mt-14 pt-8 border-t border-charcoal-100">
                    <div className="bg-brand-50/60 border border-brand-100 rounded-2xl p-6 text-center">
                        <h3 className="font-display font-bold text-xl text-charcoal-900">Need help with your project?</h3>
                        <p className="text-charcoal-600 mt-2 text-sm">Codemistry builds web, app and AI products for businesses across India.</p>
                        <Link to="/contact" className="inline-flex items-center gap-2 mt-4 px-5 py-3 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-semibold">
                            Get a free quote
                        </Link>
                    </div>
                </div>
            </article>

            {related.length > 0 && (
                <section className="max-w-6xl mx-auto px-4 sm:px-6 pb-16">
                    <h2 className="text-2xl font-display font-bold text-charcoal-900 mb-6">Related articles</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        {related.map((r) => (
                            <Link key={r.id} to={`/blog/${r.slug}`} className="bg-white rounded-2xl border border-charcoal-100 shadow-sm overflow-hidden hover:shadow-md transition-all">
                                <div className="aspect-[16/9] bg-gradient-to-br from-brand-100 to-brand-50 overflow-hidden">
                                    {r.cover_image_url ? (
                                        <img src={r.cover_image_url} alt={r.title} loading="lazy" className="w-full h-full object-cover" />
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-brand-700 text-2xl font-display font-bold opacity-40">CM</div>
                                    )}
                                </div>
                                <div className="p-4">
                                    <h3 className="font-display font-semibold text-charcoal-900 text-base leading-snug line-clamp-2">{r.title}</h3>
                                    {r.excerpt && <p className="text-charcoal-600 text-xs mt-1 line-clamp-2">{r.excerpt}</p>}
                                    <span className="text-charcoal-400 text-[11px] mt-2 block">{formatDate(r.published_at)}</span>
                                </div>
                            </Link>
                        ))}
                    </div>
                </section>
            )}
        </>
    );
};

export default BlogPostPage;
