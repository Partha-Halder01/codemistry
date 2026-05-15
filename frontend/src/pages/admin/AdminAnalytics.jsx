import React, { useState, useEffect, useCallback, useMemo } from 'react';
import {
    Activity, Users, Clock, Globe, MapPin, MousePointer2,
    Calendar, Monitor, Smartphone, Tablet, Compass, TrendingUp, Layers, Percent
} from 'lucide-react';
import api from '../../api';

const deviceIcon = (type) => {
    if (type === 'Mobile') return Smartphone;
    if (type === 'Tablet') return Tablet;
    return Monitor;
};

const TrendChart = ({ trend }) => {
    const { width, height, pad } = { width: 760, height: 220, pad: 30 };

    const series = useMemo(() => {
        if (!trend?.length) return null;
        const maxViews = Math.max(1, ...trend.map(t => t.views));
        const maxVisitors = Math.max(1, ...trend.map(t => t.visitors));
        const max = Math.max(maxViews, maxVisitors);
        const stepX = trend.length > 1 ? (width - pad * 2) / (trend.length - 1) : 0;
        const toY = v => height - pad - (v / max) * (height - pad * 2);
        const points = (key) => trend.map((t, i) => `${pad + i * stepX},${toY(t[key])}`).join(' ');
        const area = (key) => {
            const head = `${pad},${height - pad}`;
            const tail = `${pad + (trend.length - 1) * stepX},${height - pad}`;
            return `${head} ${points(key)} ${tail}`;
        };
        return { max, points, area, stepX, toY };
    }, [trend]);

    if (!series) {
        return <div className="p-8 text-center text-charcoal-500 text-sm">No traffic in this range yet.</div>;
    }

    const ticks = 4;
    const tickValues = Array.from({ length: ticks + 1 }, (_, i) => Math.round((series.max / ticks) * i));

    const labelStride = Math.max(1, Math.ceil(trend.length / 6));

    return (
        <div className="w-full overflow-x-auto">
            <svg viewBox={`0 0 ${width} ${height}`} className="w-full min-w-[640px]">
                <defs>
                    <linearGradient id="viewsGrad" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stopColor="#10b981" stopOpacity="0.3" />
                        <stop offset="100%" stopColor="#10b981" stopOpacity="0" />
                    </linearGradient>
                </defs>

                {tickValues.map((v, i) => {
                    const y = height - pad - (v / series.max) * (height - pad * 2);
                    return (
                        <g key={i}>
                            <line x1={pad} x2={width - pad} y1={y} y2={y} stroke="#e2e8f0" strokeDasharray="3 3" />
                            <text x={pad - 6} y={y + 4} textAnchor="end" fontSize="10" fill="#94a3b8">{v}</text>
                        </g>
                    );
                })}

                <polygon points={series.area('views')} fill="url(#viewsGrad)" />
                <polyline points={series.points('views')} fill="none" stroke="#10b981" strokeWidth="2" />
                <polyline points={series.points('visitors')} fill="none" stroke="#3b82f6" strokeWidth="2" strokeDasharray="4 3" />

                {trend.map((t, i) => (
                    i % labelStride === 0 && (
                        <text
                            key={t.date}
                            x={pad + i * series.stepX}
                            y={height - pad + 14}
                            fontSize="10"
                            fill="#64748b"
                            textAnchor="middle"
                        >
                            {new Date(t.date).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })}
                        </text>
                    )
                ))}
            </svg>

            <div className="flex gap-5 text-xs text-charcoal-600 mt-3 pl-8">
                <span className="flex items-center gap-2"><span className="w-3 h-0.5 bg-emerald-500 inline-block"></span> Page views</span>
                <span className="flex items-center gap-2"><span className="w-3 h-0.5 bg-blue-500 inline-block" style={{ borderTop: '2px dashed #3b82f6' }}></span> Visitors</span>
            </div>
        </div>
    );
};

const BreakdownBar = ({ items, valueKey, labelKey, total }) => {
    if (!items?.length) {
        return <div className="p-8 text-center text-charcoal-500 text-sm">No data in this range.</div>;
    }
    const sum = (total ?? items.reduce((acc, it) => acc + Number(it[valueKey] || 0), 0)) || 1;
    return (
        <ul className="divide-y divide-charcoal-100">
            {items.map((it, idx) => {
                const value = Number(it[valueKey] || 0);
                const pct = Math.round((value / sum) * 100);
                return (
                    <li key={idx} className="p-4">
                        <div className="flex items-center justify-between mb-2">
                            <span className="font-medium text-charcoal-800 text-sm truncate">{it[labelKey]}</span>
                            <span className="text-charcoal-600 text-sm font-semibold">{value.toLocaleString()} <span className="text-charcoal-400 font-normal">({pct}%)</span></span>
                        </div>
                        <div className="h-1.5 bg-charcoal-100 rounded-full overflow-hidden">
                            <div className="h-full bg-brand-500" style={{ width: `${pct}%` }}></div>
                        </div>
                    </li>
                );
            })}
        </ul>
    );
};

const AdminAnalytics = () => {
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState(null);
    const [dateRange, setDateRange] = useState('30d');
    const [customStartDate, setCustomStartDate] = useState('');
    const [customEndDate, setCustomEndDate] = useState('');
    const [isRefreshing, setIsRefreshing] = useState(false);
    const [error, setError] = useState(null);

    const fetchAnalytics = useCallback(async (isRefresh = false) => {
        if (!isRefresh) setLoading(true);
        else setIsRefreshing(true);

        try {
            let url = '/admin/analytics';
            const params = new URLSearchParams();

            if (dateRange === 'custom' && customStartDate && customEndDate) {
                params.append('start_date', customStartDate);
                params.append('end_date', customEndDate);
            } else if (dateRange === '7d' || dateRange === '30d') {
                const days = dateRange === '7d' ? 7 : 30;
                const start = new Date();
                start.setDate(start.getDate() - days);
                params.append('start_date', start.toISOString().split('T')[0]);
            }
            // 'all' sends no params — backend uses earliest record

            if (params.toString()) url += `?${params.toString()}`;

            const response = await api.get(url);
            setData(response.data);
            setError(null);
        } catch (e) {
            console.error('Error fetching analytics:', e);
            setError('Failed to load analytics. Please try again.');
        } finally {
            setLoading(false);
            setIsRefreshing(false);
        }
    }, [dateRange, customStartDate, customEndDate]);

    useEffect(() => {
        fetchAnalytics();
        const interval = setInterval(() => fetchAnalytics(true), 15000);
        return () => clearInterval(interval);
    }, [fetchAnalytics]);

    if (loading && !data) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="w-12 h-12 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div>
            </div>
        );
    }

    if (error && !data) {
        return (
            <div className="p-8 text-center">
                <p className="text-red-600 mb-3">{error}</p>
                <button onClick={() => fetchAnalytics()} className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium">Retry</button>
            </div>
        );
    }

    if (!data) return <div className="p-8 text-center text-charcoal-500">Failed to load analytics data.</div>;

    const {
        summary, trend, top_pages, top_services, top_countries, top_cities,
        device_breakdown, browser_breakdown, top_referrers, range
    } = data;

    return (
        <div className="max-w-7xl mx-auto space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-display font-bold text-charcoal-900 flex items-center gap-3">
                        Analytics Overview
                        {isRefreshing && <span className="flex w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>}
                    </h1>
                    <p className="text-charcoal-500 text-sm mt-1">
                        Track traffic, visitor behavior, and engagement.
                        {range && <span className="ml-2 text-charcoal-400">({range.start} → {range.end})</span>}
                    </p>
                </div>

                <div className="flex items-center gap-3 bg-white p-2 border border-charcoal-100 rounded-xl shadow-sm w-full sm:w-auto overflow-x-auto">
                    <Calendar className="w-4 h-4 text-charcoal-400 ml-2" />
                    <select
                        value={dateRange}
                        onChange={e => setDateRange(e.target.value)}
                        className="bg-transparent border-none text-sm font-medium text-charcoal-700 outline-none cursor-pointer pr-4"
                    >
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                        <option value="all">All Time</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    {dateRange === 'custom' && (
                        <div className="flex items-center gap-2 pl-2 border-l border-charcoal-100">
                            <input
                                type="date"
                                value={customStartDate}
                                onChange={e => setCustomStartDate(e.target.value)}
                                className="text-sm border border-charcoal-200 rounded px-2 py-1"
                            />
                            <span className="text-charcoal-400">-</span>
                            <input
                                type="date"
                                value={customEndDate}
                                onChange={e => setCustomEndDate(e.target.value)}
                                className="text-sm border border-charcoal-200 rounded px-2 py-1"
                            />
                        </div>
                    )}
                </div>
            </div>

            {/* Top Stat Cards */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <StatCard icon={Activity} label="Live Users" value={summary.live_users} color="emerald" sub="Active right now" pulse />
                <StatCard icon={Users} label="Total Visitors" value={summary.total_visitors.toLocaleString()} color="brand" />
                <StatCard icon={MousePointer2} label="Page Views" value={summary.total_views.toLocaleString()} color="blue" />
                <StatCard icon={Clock} label="Avg. Time Spent" value={summary.average_time} color="purple" />
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <StatCard icon={Layers} label="Pages / Session" value={summary.pages_per_session} color="amber" />
                <StatCard icon={Percent} label="Bounce Rate" value={`${summary.bounce_rate}%`} color="rose" sub="Single-page sessions" />
                <StatCard icon={Globe} label="Countries" value={top_countries.length} color="cyan" />
                <StatCard icon={Compass} label="Devices" value={device_breakdown.length} color="indigo" />
            </div>

            {/* Daily Trend */}
            <div className="bg-white rounded-2xl border border-charcoal-100 shadow-sm overflow-hidden">
                <div className="p-5 border-b border-charcoal-100 bg-charcoal-50/50 flex justify-between items-center">
                    <h3 className="font-semibold text-charcoal-900 flex items-center gap-2">
                        <TrendingUp className="w-4 h-4 text-brand-600" />
                        Traffic Trend
                    </h3>
                </div>
                <div className="p-5">
                    <TrendChart trend={trend || []} />
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Top Pages */}
                <Section title="Most Visited Pages" icon={Monitor}>
                    {top_pages.length > 0 ? (
                        <ul className="divide-y divide-charcoal-100">
                            {top_pages.map((page, idx) => (
                                <li key={idx} className="p-4 hover:bg-charcoal-50 transition-colors flex items-center justify-between">
                                    <div className="flex flex-col min-w-0">
                                        <span className="font-medium text-charcoal-800 break-all truncate">{page.path}</span>
                                        <span className="text-xs text-charcoal-400 mt-1">
                                            Avg Time: {Math.floor(page.avg_time / 60)}m {page.avg_time % 60}s
                                        </span>
                                    </div>
                                    <div className="bg-brand-50 text-brand-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap ml-4">
                                        {page.views.toLocaleString()} views
                                    </div>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="p-8 text-center text-charcoal-500 text-sm">No page data available.</div>
                    )}
                </Section>

                {/* Top Services */}
                <Section title="Top Services Viewed" icon={Activity}>
                    {top_services.length > 0 ? (
                        <ul className="divide-y divide-charcoal-100">
                            {top_services.map((service, idx) => (
                                <li key={idx} className="p-4 flex items-center justify-between">
                                    <span className="font-medium text-charcoal-800">{service.name}</span>
                                    <div className="text-charcoal-600 font-medium text-sm">{service.views.toLocaleString()} views</div>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="p-8 text-center text-charcoal-500 text-sm">No service views recorded yet.</div>
                    )}
                </Section>
            </div>

            {/* Devices / Browsers */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <Section title="Devices" icon={Smartphone}>
                    {device_breakdown.length > 0 ? (
                        <ul className="divide-y divide-charcoal-100">
                            {device_breakdown.map((d, idx) => {
                                const Icon = deviceIcon(d.device_type);
                                const sum = device_breakdown.reduce((a, b) => a + Number(b.visitors), 0) || 1;
                                const pct = Math.round((d.visitors / sum) * 100);
                                return (
                                    <li key={idx} className="p-4">
                                        <div className="flex items-center justify-between mb-2">
                                            <span className="flex items-center gap-2 font-medium text-charcoal-800 text-sm">
                                                <Icon className="w-4 h-4 text-charcoal-500" />
                                                {d.device_type}
                                            </span>
                                            <span className="text-charcoal-600 text-sm font-semibold">
                                                {Number(d.visitors).toLocaleString()} <span className="text-charcoal-400 font-normal">({pct}%)</span>
                                            </span>
                                        </div>
                                        <div className="h-1.5 bg-charcoal-100 rounded-full overflow-hidden">
                                            <div className="h-full bg-indigo-500" style={{ width: `${pct}%` }}></div>
                                        </div>
                                    </li>
                                );
                            })}
                        </ul>
                    ) : (
                        <div className="p-8 text-center text-charcoal-500 text-sm">No device data yet — newer visits will populate this.</div>
                    )}
                </Section>

                <Section title="Browsers" icon={Compass}>
                    <BreakdownBar items={browser_breakdown} valueKey="visitors" labelKey="browser" />
                </Section>
            </div>

            {/* Referrers + Geography */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <Section title="Top Referrers" icon={Globe}>
                    {top_referrers.length > 0 ? (
                        <BreakdownBar items={top_referrers} valueKey="visitors" labelKey="referrer" />
                    ) : (
                        <div className="p-8 text-center text-charcoal-500 text-sm">No external referrers — most visits are direct.</div>
                    )}
                </Section>

                <Section title="Visitor Geography (Countries)" icon={Globe}>
                    {top_countries.length > 0 ? (
                        <ul className="divide-y divide-charcoal-100">
                            {top_countries.map((loc, idx) => (
                                <li key={idx} className="p-4 flex items-center justify-between">
                                    <span className="font-medium text-charcoal-800">{loc.country === 'Localhost' ? 'Local Network' : loc.country}</span>
                                    <div className="text-charcoal-600 font-medium text-sm">{loc.visitors.toLocaleString()} visitors</div>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="p-8 text-center text-charcoal-500 text-sm">No country data available.</div>
                    )}
                </Section>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
                <Section title="Visitor Geography (Cities)" icon={MapPin}>
                    {top_cities.length > 0 ? (
                        <ul className="divide-y divide-charcoal-100">
                            {top_cities.map((loc, idx) => (
                                <li key={idx} className="p-4 flex items-center justify-between">
                                    <span className="font-medium text-charcoal-800">
                                        {loc.city === 'Local' ? 'Local Development' : loc.city}
                                        <span className="text-charcoal-400 font-normal text-sm ml-1">({loc.country})</span>
                                    </span>
                                    <div className="text-charcoal-600 font-medium text-sm">{loc.visitors.toLocaleString()} visitors</div>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="p-8 text-center text-charcoal-500 text-sm">No city data available.</div>
                    )}
                </Section>

                <Section title="Engagement Snapshot" icon={Activity}>
                    <div className="p-5 grid grid-cols-2 gap-4 text-sm">
                        <div className="p-4 bg-charcoal-50/60 rounded-xl">
                            <div className="text-charcoal-500 text-xs uppercase tracking-wide">Total Sessions</div>
                            <div className="text-2xl font-display font-bold text-charcoal-900 mt-1">{summary.total_visitors.toLocaleString()}</div>
                        </div>
                        <div className="p-4 bg-charcoal-50/60 rounded-xl">
                            <div className="text-charcoal-500 text-xs uppercase tracking-wide">Total Views</div>
                            <div className="text-2xl font-display font-bold text-charcoal-900 mt-1">{summary.total_views.toLocaleString()}</div>
                        </div>
                        <div className="p-4 bg-charcoal-50/60 rounded-xl">
                            <div className="text-charcoal-500 text-xs uppercase tracking-wide">Pages / Session</div>
                            <div className="text-2xl font-display font-bold text-charcoal-900 mt-1">{summary.pages_per_session}</div>
                        </div>
                        <div className="p-4 bg-charcoal-50/60 rounded-xl">
                            <div className="text-charcoal-500 text-xs uppercase tracking-wide">Bounce Rate</div>
                            <div className="text-2xl font-display font-bold text-charcoal-900 mt-1">{summary.bounce_rate}%</div>
                        </div>
                    </div>
                </Section>
            </div>
        </div>
    );
};

const Section = ({ title, icon: Icon, children }) => (
    <div className="bg-white rounded-2xl border border-charcoal-100 shadow-sm overflow-hidden">
        <div className="p-5 border-b border-charcoal-100 bg-charcoal-50/50 flex justify-between items-center">
            <h3 className="font-semibold text-charcoal-900 flex items-center gap-2">
                <Icon className="w-4 h-4 text-brand-600" />
                {title}
            </h3>
        </div>
        <div className="p-0">{children}</div>
    </div>
);

const colorMap = {
    emerald: { bg: 'bg-emerald-50', text: 'text-emerald-600' },
    brand:   { bg: 'bg-brand-50',   text: 'text-brand-600' },
    blue:    { bg: 'bg-blue-50',    text: 'text-blue-600' },
    purple:  { bg: 'bg-purple-50',  text: 'text-purple-600' },
    amber:   { bg: 'bg-amber-50',   text: 'text-amber-600' },
    rose:    { bg: 'bg-rose-50',    text: 'text-rose-600' },
    cyan:    { bg: 'bg-cyan-50',    text: 'text-cyan-600' },
    indigo:  { bg: 'bg-indigo-50',  text: 'text-indigo-600' },
};

const StatCard = ({ icon: Icon, label, value, color = 'brand', sub, pulse }) => {
    const c = colorMap[color] || colorMap.brand;
    return (
        <div className="bg-white rounded-2xl border border-charcoal-100 p-6 shadow-sm relative overflow-hidden">
            <div className="absolute top-0 right-0 p-4 opacity-10">
                <Icon className={`w-16 h-16 ${c.text}`} />
            </div>
            <div className="flex items-center gap-3 mb-4">
                <div className={`w-10 h-10 rounded-xl ${c.bg} flex items-center justify-center ${c.text} relative`}>
                    <Icon className="w-5 h-5" />
                    {pulse && <span className="absolute top-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white animate-ping"></span>}
                </div>
                <h3 className="text-charcoal-500 font-medium text-sm">{label}</h3>
            </div>
            <div className="relative z-10">
                <span className="text-3xl font-display font-bold text-charcoal-900">{value}</span>
                {sub && <p className={`text-xs ${c.text} mt-1 font-medium`}>{sub}</p>}
            </div>
        </div>
    );
};

export default AdminAnalytics;
