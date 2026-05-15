import React, { useEffect, useState, useCallback } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { Calendar, User, Search, Tag, ArrowRight, Loader2 } from 'lucide-react';
import api from '../api';
import Seo from '../components/Seo';
import { breadcrumbLd, organizationLd, SITE_INFO } from '../seo/structuredData';

const formatDate = (d) => {
    if (!d) return '';
    try {
        return new Date(d).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
    } catch {
        return '';
    }
};

const Blog = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const [posts, setPosts] = useState([]);
    const [meta, setMeta] = useState(null);
    const [loading, setLoading] = useState(true);
    const [searchInput, setSearchInput] = useState(searchParams.get('search') || '');

    const fetchPosts = useCallback(async () => {
        setLoading(true);
        try {
            const params = {};
            const search = searchParams.get('search');
            const tag = searchParams.get('tag');
            const page = searchParams.get('page');
            if (search) params.search = search;
            if (tag) params.tag = tag;
            if (page) params.page = page;

            const r = await api.get('/blog-posts', { params });
            setPosts(r.data.data || []);
            setMeta({
                current_page: r.data.current_page,
                last_page: r.data.last_page,
                total: r.data.total,
            });
        } catch (e) {
            console.error('Failed to load posts', e);
            setPosts([]);
        } finally {
            setLoading(false);
        }
    }, [searchParams]);

    useEffect(() => { fetchPosts(); }, [fetchPosts]);

    const onSearchSubmit = (e) => {
        e.preventDefault();
        const next = new URLSearchParams(searchParams);
        if (searchInput.trim()) next.set('search', searchInput.trim());
        else next.delete('search');
        next.delete('page');
        setSearchParams(next);
    };

    const setTag = (tag) => {
        const next = new URLSearchParams(searchParams);
        if (tag) next.set('tag', tag);
        else next.delete('tag');
        next.delete('page');
        setSearchParams(next);
    };

    const goToPage = (page) => {
        const next = new URLSearchParams(searchParams);
        next.set('page', String(page));
        setSearchParams(next);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const activeTag = searchParams.get('tag') || '';
    const activeSearch = searchParams.get('search') || '';

    const canonical = `${SITE_INFO.url}/blog`;

    return (
        <>
            <Seo
                title="Blog — Web, App & AI Insights for Indian Businesses"
                description="Practical guides on web development cost in India, mobile app pricing, e-commerce setup with Razorpay & UPI, GST-compliant invoicing, and AI integration for Indian startups."
                canonical={canonical}
                keywords="web development India blog, app development India, AI integration India, ecommerce India guide"
                jsonLd={[
                    organizationLd(),
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'Blog', url: canonical },
                    ]),
                ]}
            />

            <section className="bg-gradient-to-b from-brand-50/40 to-white border-b border-charcoal-100">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 py-16">
                    <div className="text-center max-w-3xl mx-auto">
                        <span className="inline-block px-3 py-1 bg-brand-100 text-brand-700 rounded-full text-xs font-bold tracking-wider uppercase">
                            Codemistry Blog
                        </span>
                        <h1 className="mt-4 text-3xl sm:text-5xl font-display font-bold text-charcoal-900">
                            Insights for Indian Businesses Building Online
                        </h1>
                        <p className="mt-4 text-charcoal-600 text-base sm:text-lg">
                            Honest, India-specific guides on web development, mobile apps, e-commerce, GST compliance, AI integration and pricing in INR.
                        </p>

                        <form onSubmit={onSearchSubmit} className="mt-8 flex items-center gap-2 max-w-xl mx-auto">
                            <div className="flex-1 flex items-center gap-2 bg-white border border-charcoal-200 rounded-xl px-4 py-3 shadow-sm">
                                <Search className="w-4 h-4 text-charcoal-400" />
                                <input
                                    type="text"
                                    value={searchInput}
                                    onChange={e => setSearchInput(e.target.value)}
                                    placeholder="Search articles..."
                                    className="flex-1 outline-none bg-transparent text-sm"
                                />
                            </div>
                            <button type="submit" className="px-5 py-3 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-semibold shadow-sm">
                                Search
                            </button>
                        </form>

                        {(activeTag || activeSearch) && (
                            <div className="mt-4 text-sm text-charcoal-600">
                                {activeSearch && <span>Showing results for <strong>"{activeSearch}"</strong>. </span>}
                                {activeTag && <span>Filtered by tag <strong>#{activeTag}</strong>. </span>}
                                <button
                                    onClick={() => setSearchParams({})}
                                    className="text-brand-600 hover:underline ml-1"
                                >Clear filters</button>
                            </div>
                        )}
                    </div>
                </div>
            </section>

            <section className="max-w-6xl mx-auto px-4 sm:px-6 py-12">
                {loading ? (
                    <div className="flex items-center justify-center py-20">
                        <Loader2 className="w-8 h-8 text-brand-500 animate-spin" />
                    </div>
                ) : posts.length === 0 ? (
                    <div className="text-center py-20 text-charcoal-500">
                        No posts found. <button onClick={() => setSearchParams({})} className="text-brand-600 hover:underline">Reset filters</button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {posts.map((post) => (
                            <BlogCard key={post.id} post={post} onTagClick={setTag} />
                        ))}
                    </div>
                )}

                {meta && meta.last_page > 1 && (
                    <div className="flex justify-center items-center gap-2 mt-10">
                        {Array.from({ length: meta.last_page }, (_, i) => i + 1).map((p) => (
                            <button
                                key={p}
                                onClick={() => goToPage(p)}
                                className={`w-9 h-9 rounded-lg text-sm font-semibold border ${
                                    p === meta.current_page
                                        ? 'bg-brand-500 text-white border-brand-500'
                                        : 'bg-white text-charcoal-700 border-charcoal-200 hover:bg-charcoal-50'
                                }`}
                            >
                                {p}
                            </button>
                        ))}
                    </div>
                )}
            </section>
        </>
    );
};

const BlogCard = ({ post, onTagClick }) => {
    const cover = post.cover_image_url;
    return (
        <article className="bg-white rounded-2xl border border-charcoal-100 shadow-sm overflow-hidden hover:shadow-lg transition-all flex flex-col">
            <Link to={`/blog/${post.slug}`} className="block">
                <div className="aspect-[16/9] bg-gradient-to-br from-brand-100 to-brand-50 flex items-center justify-center overflow-hidden">
                    {cover ? (
                        <img src={cover} alt={post.title} loading="lazy" className="w-full h-full object-cover" />
                    ) : (
                        <span className="text-brand-700 text-4xl font-display font-bold opacity-40">CM</span>
                    )}
                </div>
            </Link>
            <div className="p-5 flex flex-col flex-1">
                <div className="flex items-center gap-3 text-xs text-charcoal-500 mb-2">
                    <span className="flex items-center gap-1"><Calendar className="w-3 h-3" /> {formatDate(post.published_at)}</span>
                    <span className="flex items-center gap-1"><User className="w-3 h-3" /> {post.author_name}</span>
                </div>
                <h3 className="font-display font-bold text-charcoal-900 text-lg leading-tight mb-2">
                    <Link to={`/blog/${post.slug}`} className="hover:text-brand-600 transition-colors">{post.title}</Link>
                </h3>
                <p className="text-charcoal-600 text-sm line-clamp-3 flex-1">{post.excerpt}</p>

                {Array.isArray(post.tags) && post.tags.length > 0 && (
                    <div className="flex flex-wrap gap-1 mt-3">
                        {post.tags.slice(0, 3).map((t) => (
                            <button
                                key={t}
                                onClick={() => onTagClick(t)}
                                className="text-[11px] px-2 py-0.5 bg-brand-50 text-brand-700 rounded-full hover:bg-brand-100"
                            >
                                <Tag className="w-2.5 h-2.5 inline -mt-0.5 mr-1" />{t}
                            </button>
                        ))}
                    </div>
                )}

                <Link to={`/blog/${post.slug}`} className="inline-flex items-center gap-1 mt-4 text-brand-600 text-sm font-semibold hover:gap-2 transition-all">
                    Read article <ArrowRight className="w-4 h-4" />
                </Link>
            </div>
        </article>
    );
};

export default Blog;
