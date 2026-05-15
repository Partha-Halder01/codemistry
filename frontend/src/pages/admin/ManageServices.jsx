import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, X, Check, Upload, PlusCircle, MinusCircle, Star } from 'lucide-react';
import api from '../../api';

const ManageServices = () => {
    const [services, setServices] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isEditing, setIsEditing] = useState(false);
    const [currentService, setCurrentService] = useState(null);

    const initialFormState = {
        name: '',
        slug: '',
        description: '',
        full_price: '',
        deposit_price: '',
        features: '',
        cover_image_path: '',
        cta_image_path: '',
        is_featured: false,
        faq: [], // Array of { q: '', a: '' }
        pricings: [], // Array of { id?: null, plan_name: '', price: '', features: [], is_popular: false }
        process_steps: [] // Array of { title: '', description: '', icon: '' }
    };
    const [formData, setFormData] = useState(initialFormState);

    const fetchServices = async () => {
        setLoading(true);
        try {
            const { data } = await api.get('/services');
            setServices(data);
        } catch (e) {
            console.error(e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchServices();
    }, []);

    const handleInputChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value });

    // FAQ Handlers
    const addFaq = () => setFormData({ ...formData, faq: [...formData.faq, { q: '', a: '' }] });
    const removeFaq = (index) => {
        const newFaqs = [...formData.faq];
        newFaqs.splice(index, 1);
        setFormData({ ...formData, faq: newFaqs });
    };
    const updateFaq = (index, field, value) => {
        const newFaqs = [...formData.faq];
        newFaqs[index][field] = value;
        setFormData({ ...formData, faq: newFaqs });
    };

    // Pricing Handlers
    const addPricing = () => setFormData({ ...formData, pricings: [...formData.pricings, { plan_name: '', price: '', features: [], is_popular: false }] });
    const removePricing = (index) => {
        const newPricings = [...formData.pricings];
        newPricings.splice(index, 1);
        setFormData({ ...formData, pricings: newPricings });
    };
    const updatePricing = (index, field, value) => {
        const newPricings = [...formData.pricings];
        if ((field === 'price' || field === 'end_price') && value === '') {
            newPricings[index][field] = null;
        } else {
            newPricings[index][field] = value;
        }
        setFormData({ ...formData, pricings: newPricings });
    };
    const updatePricingFeatures = (index, text) => {
        const newPricings = [...formData.pricings];
        newPricings[index].features = text.split('\n').filter(f => f.trim());
        setFormData({ ...formData, pricings: newPricings });
    };

    // Process Steps Handlers
    const addProcessStep = () => setFormData({ ...formData, process_steps: [...formData.process_steps, { title: '', description: '', icon: 'CheckCircle2' }] });
    const removeProcessStep = (index) => {
        const newSteps = [...formData.process_steps];
        newSteps.splice(index, 1);
        setFormData({ ...formData, process_steps: newSteps });
    };
    const updateProcessStep = (index, field, value) => {
        const newSteps = [...formData.process_steps];
        newSteps[index][field] = value;
        setFormData({ ...formData, process_steps: newSteps });
    };

    const handleEdit = (s) => {
        setCurrentService(s.id);
        setFormData({
            ...s,
            features: s.features || '',
            cover_image_path: s.cover_image_path || '',
            faq: s.faq || [],
            pricings: s.pricings || [],
            // Convert numbers back to string for inputs (divide by 100 if paise, but we store as standard int? Let's assume standard integer pricing here based on migration)
            full_price: s.full_price || '',
            deposit_price: s.deposit_price || '',
            is_featured: s.is_featured || false,
        });
        setIsEditing(true);
    };

    const handleAddNew = () => {
        setCurrentService(null);
        setFormData(initialFormState);
        setIsEditing(true);
    };

    const handleCancel = () => {
        setIsEditing(false);
        setCurrentService(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const formDataToSend = new FormData();

            // Append all fields to FormData
            Object.keys(formData).forEach(key => {
                if (key === 'faq' || key === 'pricings' || key === 'process_steps') {
                    formDataToSend.append(key, JSON.stringify(formData[key]));
                } else if (key === 'cover_image' || key === 'cta_image') {
                    if (formData[key]) formDataToSend.append(key, formData[key]);
                } else if (key === 'is_featured') {
                    formDataToSend.append(key, formData[key] ? 1 : 0);
                } else {
                    formDataToSend.append(key, formData[key] === null ? '' : formData[key]);
                }
            });

            const config = {
                headers: { 'Content-Type': 'multipart/form-data' }
            };

            if (currentService) {
                // Laravel Spoofing for PUT with multipart/form-data
                formDataToSend.append('_method', 'PUT');
                await api.post(`/admin/services/${currentService}`, formDataToSend, config);
            } else {
                await api.post('/admin/services', formDataToSend, config);
            }
            fetchServices();
            setIsEditing(false);
        } catch (err) {
            console.error(err.response?.data);
            const errorMsg = err.response?.data?.message || err.message;
            const errors = err.response?.data?.errors ? JSON.stringify(err.response.data.errors, null, 2) : '';
            alert(`Failed to save service: ${errorMsg}\n\n${errors}`);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm("Are you sure you want to delete this service? All related pricings will be destroyed.")) return;
        try {
            await api.delete(`/admin/services/${id}`);
            fetchServices();
        } catch (e) {
            console.error(e);
        }
    };

    const handleToggleFeatured = async (id) => {
        try {
            await api.patch(`/admin/services/${id}/toggle-featured`);
            fetchServices();
        } catch (e) {
            console.error('Failed to toggle featured status', e);
        }
    };

    const inputClass = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg px-4 py-2.5 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500";
    const labelClass = "block text-xs font-semibold text-charcoal-600 mb-2 tracking-wide uppercase";

    return (
        <div>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 className="text-2xl font-display font-bold text-charcoal-950 mb-1">Manage Services</h1>
                    <p className="text-charcoal-400 text-sm">Create and edit dynamic services with pricing tiers and FAQs.</p>
                </div>
                {!isEditing && (
                    <button onClick={handleAddNew} className="btn-primary flex items-center gap-2 !text-sm !py-2 !px-4 hover:-translate-y-1 transition-transform">
                        <Plus className="w-4 h-4" /> Add Service
                    </button>
                )}
            </div>

            {isEditing ? (
                <div className="bg-white border border-charcoal-100 rounded-2xl p-6 shadow-sm overflow-hidden">
                    <div className="flex justify-between items-center mb-8 pb-4 border-b border-charcoal-100">
                        <h2 className="text-xl font-display font-bold text-charcoal-950">{currentService ? 'Edit Service' : 'Create New Service'}</h2>
                        <button onClick={handleCancel} className="p-2 text-charcoal-400 hover:text-charcoal-900 hover:bg-charcoal-50 rounded-lg transition-colors"><X className="w-5 h-5" /></button>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-10">
                        {/* 1. Basic Information */}
                        <section className="space-y-5">
                            <h3 className="text-lg font-bold text-charcoal-900 flex items-center gap-2"><span className="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm">1</span> Basic Info</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5 pl-8">
                                <div className="md:col-span-1">
                                    <label className={labelClass}>Service Name *</label>
                                    <input type="text" name="name" required value={formData.name} onChange={handleInputChange} className={inputClass} placeholder="e.g. Web Development" />
                                </div>
                                <div className="md:col-span-1">
                                    <label className={labelClass}>URL Slug (Optional)</label>
                                    <input type="text" name="slug" value={formData.slug} onChange={handleInputChange} className={inputClass} placeholder="e.g. web-development" />
                                    <p className="text-[10px] text-charcoal-400 mt-1 italic">Leave blank to auto-generate from name.</p>
                                </div>
                                <div className="md:col-span-2">
                                    <label className={labelClass}>Short Description *</label>
                                    <textarea name="description" required rows="3" value={formData.description} onChange={handleInputChange} className={`${inputClass} resize-none`} placeholder="Briefly describe the service..."></textarea>
                                </div>
                                <div className="md:col-span-1">
                                    <label className={labelClass}>Cover Image</label>
                                    <div className="flex flex-col gap-2">
                                        <input type="file" onChange={(e) => setFormData({ ...formData, cover_image: e.target.files[0] })} className={inputClass} accept="image/*" />
                                        {formData.cover_image_path && !formData.cover_image && (
                                            <div className="rounded-lg overflow-hidden border border-charcoal-200">
                                                <img src={`${api.defaults.baseURL.replace('/api', '')}/storage/${formData.cover_image_path}`} alt="Current" className="w-full h-20 object-cover" />
                                            </div>
                                        )}
                                    </div>
                                </div>
                                <div className="md:col-span-1">
                                    <label className={labelClass}>CTA Background (Parallax)</label>
                                    <div className="flex flex-col gap-2">
                                        <input type="file" onChange={(e) => setFormData({ ...formData, cta_image: e.target.files[0] })} className={inputClass} accept="image/*" />
                                        {formData.cta_image_path && !formData.cta_image && (
                                            <div className="rounded-lg overflow-hidden border border-charcoal-200">
                                                <img src={`${api.defaults.baseURL.replace('/api', '')}/storage/${formData.cta_image_path}`} alt="Current CTA" className="w-full h-20 object-cover" />
                                            </div>
                                        )}
                                    </div>
                                </div>
                                <div className="md:col-span-2 flex items-center gap-3 bg-brand-50/50 p-4 rounded-xl border border-brand-100">
                                    <input
                                        type="checkbox"
                                        id="is_featured"
                                        checked={formData.is_featured}
                                        onChange={(e) => setFormData({ ...formData, is_featured: e.target.checked })}
                                        className="w-5 h-5 rounded border-charcoal-300 text-brand-500 focus:ring-brand-500/40"
                                    />
                                    <label htmlFor="is_featured" className="text-sm font-semibold text-charcoal-800 cursor-pointer select-none">Feature this service on the Home Page</label>
                                </div>
                            </div>
                        </section>

                        <div className="h-px bg-charcoal-100"></div>

                        {/* 2. Process Steps */}
                        <section className="space-y-5">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-bold text-charcoal-900 flex items-center gap-2"><span className="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm">2</span> Journey Steps</h3>
                                <button type="button" onClick={addProcessStep} className="text-sm font-medium text-brand-600 hover:text-brand-700 flex items-center gap-1"><PlusCircle className="w-4 h-4" /> Add Step</button>
                            </div>

                            <div className="space-y-4 pl-8">
                                {formData.process_steps?.map((step, index) => (
                                    <div key={index} className="p-4 border border-charcoal-100 rounded-xl bg-charcoal-50/30 relative">
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div className="md:col-span-1">
                                                <label className={labelClass}>Title</label>
                                                <input type="text" value={step.title} onChange={(e) => updateProcessStep(index, 'title', e.target.value)} className={inputClass} placeholder="Step Title" required />
                                            </div>
                                            <div className="md:col-span-2">
                                                <label className={labelClass}>Description</label>
                                                <div className="flex gap-2">
                                                    <input type="text" value={step.description} onChange={(e) => updateProcessStep(index, 'description', e.target.value)} className={inputClass} placeholder="Step description..." required />
                                                    <button type="button" onClick={() => removeProcessStep(index)} className="p-2.5 text-charcoal-400 hover:text-red-500 rounded-lg border border-charcoal-200 bg-white"><Trash2 className="w-4 h-4" /></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                                {(!formData.process_steps || formData.process_steps.length === 0) && <p className="text-sm text-charcoal-400 italic">No custom steps added. Default steps will be used.</p>}
                            </div>
                        </section>

                        <div className="h-px bg-charcoal-100"></div>

                        {/* 3. Pricing Tiers */}
                        <section className="space-y-5">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-bold text-charcoal-900 flex items-center gap-2"><span className="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm">3</span> Pricing Tiers</h3>
                                <button type="button" onClick={addPricing} className="text-sm font-medium text-brand-600 hover:text-brand-700 flex items-center gap-1"><PlusCircle className="w-4 h-4" /> Add Plan</button>
                            </div>

                            <div className="space-y-4 pl-8">
                                {formData.pricings.map((plan, index) => (
                                    <div key={index} className="p-5 border border-charcoal-100 rounded-xl bg-charcoal-50/50 relative group">
                                        <button type="button" onClick={() => removePricing(index)} className="absolute top-4 right-4 text-charcoal-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"><Trash2 className="w-4 h-4" /></button>

                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label className={labelClass}>Plan Name</label>
                                                <input type="text" value={plan.plan_name} onChange={(e) => updatePricing(index, 'plan_name', e.target.value)} className={inputClass} placeholder="e.g. Basic, Pro" required />
                                            </div>
                                            <div>
                                                <label className={labelClass}>Starting Price (₹)</label>
                                                <input type="number" value={plan.price} onChange={(e) => updatePricing(index, 'price', e.target.value)} className={inputClass} placeholder="e.g. 50000" required />
                                            </div>
                                            <div>
                                                <label className={labelClass}>Ending Price (Optional)</label>
                                                <input type="number" value={plan.end_price || ''} onChange={(e) => updatePricing(index, 'end_price', e.target.value)} className={inputClass} placeholder="Optional Max" />
                                            </div>
                                            <div className="md:col-span-3">
                                                <label className={labelClass}>Features (One per line)</label>
                                                <textarea rows="4" value={plan.features ? plan.features.join('\n') : ''} onChange={(e) => updatePricingFeatures(index, e.target.value)} className={`${inputClass} resize-none`} placeholder="5 Pages&#10;Basic SEO&#10;1 month support" />
                                            </div>
                                            <div className="md:col-span-3 flex items-center gap-2">
                                                <input type="checkbox" id={`popular-${index}`} checked={plan.is_popular} onChange={(e) => updatePricing(index, 'is_popular', e.target.checked)} className="rounded border-charcoal-300 text-brand-500 focus:ring-brand-500/40" />
                                                <label htmlFor={`popular-${index}`} className="text-sm font-medium text-charcoal-700">Mark as Popular / Recommended Plan</label>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                                {formData.pricings.length === 0 && <p className="text-sm text-charcoal-400 italic">No pricing plans added. This service will display as custom pricing.</p>}
                            </div>
                        </section>

                        <div className="h-px bg-charcoal-100"></div>

                        {/* 4. FAQs */}
                        <section className="space-y-5">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-bold text-charcoal-900 flex items-center gap-2"><span className="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm">4</span> FAQs</h3>
                                <button type="button" onClick={addFaq} className="text-sm font-medium text-brand-600 hover:text-brand-700 flex items-center gap-1"><PlusCircle className="w-4 h-4" /> Add FAQ</button>
                            </div>

                            <div className="space-y-4 pl-8">
                                {formData.faq.map((item, index) => (
                                    <div key={index} className="flex gap-4 items-start relative border-l-2 border-charcoal-100 pl-4 ml-2">
                                        <div className="flex-1 space-y-3">
                                            <input type="text" value={item.q} onChange={(e) => updateFaq(index, 'q', e.target.value)} className={inputClass} placeholder="Question..." required />
                                            <textarea value={item.a} onChange={(e) => updateFaq(index, 'a', e.target.value)} className={`${inputClass} resize-none`} rows="2" placeholder="Answer..." required />
                                        </div>
                                        <button type="button" onClick={() => removeFaq(index)} className="p-2 text-charcoal-400 hover:text-red-500 bg-white border border-charcoal-200 rounded-lg"><MinusCircle className="w-4 h-4" /></button>
                                    </div>
                                ))}
                                {formData.faq.length === 0 && <p className="text-sm text-charcoal-400 italic">No FAQs added.</p>}
                            </div>
                        </section>

                        {/* Actions */}
                        <div className="flex justify-end gap-3 pt-6 border-t border-charcoal-100 sticky bottom-0 bg-white py-4 z-10 -mx-6 px-6 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
                            <button type="button" onClick={handleCancel} className="px-6 py-2.5 rounded-lg text-charcoal-600 text-sm font-medium hover:bg-charcoal-50 transition-colors">Cancel</button>
                            <button type="submit" className="btn-primary flex items-center gap-2 !text-sm !py-2.5 !px-8 hover:-translate-y-1 transition-transform shadow-lg shadow-brand-500/20"><Check className="w-4 h-4" /> Save Service</button>
                        </div>
                    </form>
                </div >
            ) : loading ? (
                <div className="flex justify-center p-20"><div className="w-8 h-8 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>
            ) : (
                <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    {services.map(s => (
                        <div key={s.id} className="bg-white border border-charcoal-100 rounded-2xl overflow-hidden hover:shadow-lg hover:shadow-charcoal-900/5 transition-all group flex flex-col h-full">
                            {/* Card Image */}
                            <div className="h-40 bg-charcoal-100 relative overflow-hidden">
                                {s.cover_image_path ? (
                                    <img src={s.cover_image_path} alt={s.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                ) : (
                                    <div className="absolute inset-0 flex items-center justify-center text-charcoal-300">No Image</div>
                                )}
                                <div className="absolute inset-0 bg-gradient-to-t from-charcoal-950/60 to-transparent"></div>
                                <div className="absolute bottom-4 left-4 right-4 flex justify-between items-end gap-2">
                                    <h3 className="text-lg font-bold text-white drop-shadow-sm line-clamp-1">{s.name}</h3>

                                    <button
                                        onClick={() => handleToggleFeatured(s.id)}
                                        title={s.is_featured ? "Remove from Home Page" : "Feature on Home Page"}
                                        className={`p-1.5 rounded-full backdrop-blur-sm transition-all ${s.is_featured
                                            ? 'bg-amber-400 text-white shadow-[0_0_15px_rgba(251,191,36,0.5)]'
                                            : 'bg-black/30 text-white/70 hover:bg-black/50 hover:text-white'
                                            }`}
                                    >
                                        <Star className={`w-4 h-4 ${s.is_featured ? 'fill-current' : ''}`} />
                                    </button>
                                </div>
                            </div>

                            {/* Card Content */}
                            <div className="p-5 flex-1 flex flex-col">
                                <p className="text-charcoal-500 text-sm mb-4 line-clamp-2 flex-1">{s.description}</p>

                                <div className="flex items-center gap-4 text-xs font-semibold text-charcoal-400 mb-5 uppercase tracking-wider">
                                    <span>{s.pricings?.length || 0} Pricing Plans</span>
                                    <span>•</span>
                                    <span>{s.faq?.length || 0} FAQs</span>
                                </div>

                                <div className="flex gap-2">
                                    <button onClick={() => handleEdit(s)} className="flex-1 justify-center btn-secondary !text-xs !py-2 flex items-center gap-2 group-hover:bg-charcoal-100"><Edit2 className="w-3.5 h-3.5" /> Edit</button>
                                    <button onClick={() => handleDelete(s.id)} className="flex justify-center items-center p-2 rounded-lg bg-white border border-charcoal-200 text-charcoal-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors"><Trash2 className="w-4 h-4" /></button>
                                </div>
                            </div>
                        </div>
                    ))}
                    {services.length === 0 && (
                        <div className="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-charcoal-200">
                            <div className="w-16 h-16 rounded-full bg-charcoal-50 flex items-center justify-center mb-4"><Plus className="w-8 h-8 text-charcoal-300" /></div>
                            <h3 className="text-lg font-bold text-charcoal-900 mb-1">No services created</h3>
                            <p className="text-charcoal-500 text-sm mb-6 text-center max-w-sm">Get started by adding your first service with dynamic pricing and FAQs.</p>
                            <button onClick={handleAddNew} className="btn-primary !text-sm">Create Service</button>
                        </div>
                    )}
                </div>
            )}
        </div >
    );
};

export default ManageServices;
