import { useState, useRef, useEffect } from 'react';
import { X, Send, Bot, User, Cpu, Sparkles } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import ReactMarkdown from 'react-markdown';
import api from '../api';

const Chatbot = () => {
    const navigate = useNavigate();
    const [isOpen, setIsOpen] = useState(false);
    const [messages, setMessages] = useState([
        { role: 'model', content: "Hi there! I'm the Codemistry AI assistant. How can I help you today?" }
    ]);
    const [input, setInput] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const messagesEndRef = useRef(null);

    useEffect(() => {
        const isMobile = window.matchMedia('(max-width: 767px)').matches;
        const handleOpenChatbot = () => setIsOpen(true);
        const handleMobileAwareOpen = () => {
            if (isMobile) {
                navigate('/ai-support');
                return;
            }
            handleOpenChatbot();
        };
        window.addEventListener('open-chatbot', handleMobileAwareOpen);
        return () => window.removeEventListener('open-chatbot', handleMobileAwareOpen);
    }, [navigate]);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        if (isOpen) {
            scrollToBottom();
        }
    }, [messages, isOpen]);

    const sendMessage = async (textToSubmit) => {
        const userMsg = textToSubmit.trim();
        if (!userMsg) return;

        setInput('');
        setIsLoading(true);
        const newMessages = [...messages, { role: 'user', content: userMsg }];
        setMessages(newMessages);

        try {
            const history = newMessages.slice(0, -1);
            const response = await api.post('/chat', {
                message: userMsg,
                history
            });

            const replyText = response.data.reply;
            setMessages([...newMessages, { role: 'model', content: replyText }]);
        } catch (error) {
            console.error('Chat error:', error);
            const errReply = "I'm sorry, I'm having trouble connecting to my brain right now. Please try again later.";
            setMessages([...newMessages, {
                role: 'model',
                content: errReply,
                isError: true
            }]);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSend = (e) => {
        e.preventDefault();
        if (!isLoading) {
            sendMessage(input);
        }
    };

    const markdownComponents = {
        h1: ({ children }) => <h1 className="text-[15px] font-bold text-charcoal-950 mb-2">{children}</h1>,
        h2: ({ children }) => <h2 className="text-sm font-bold text-charcoal-900 mb-2 mt-2">{children}</h2>,
        h3: ({ children }) => <h3 className="text-sm font-bold text-charcoal-900 mb-1">{children}</h3>,
        p: ({ children }) => <p className="text-sm leading-6 text-charcoal-800 mb-2">{children}</p>,
        strong: ({ children }) => <strong className="font-bold text-charcoal-950">{children}</strong>,
        ul: ({ children }) => <ul className="space-y-1.5 mb-2">{children}</ul>,
        li: ({ children }) => <li className="text-sm leading-6 text-charcoal-800 list-disc ml-4">{children}</li>,
        code: ({ children }) => <code className="bg-charcoal-100 px-1.5 py-0.5 rounded text-xs">{children}</code>,
    };

    return (
        <div className="fixed bottom-6 left-6 z-[60] flex flex-col items-start gap-4">
            <div className={`
                absolute bottom-20 left-0 w-[350px] sm:w-[420px] h-[600px] max-h-[85vh] 
                bg-white/90 backdrop-blur-2xl border border-white/20 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] 
                flex flex-col overflow-hidden transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) origin-bottom-left
                hidden md:flex
                ${isOpen ? 'scale-100 opacity-100 pointer-events-auto translate-y-0' : 'scale-95 opacity-0 pointer-events-none translate-y-10'}
            `}>
                <div className="bg-charcoal-950/95 backdrop-blur-md p-5 flex justify-between items-center shrink-0 border-b border-white/10">
                    <div className="flex items-center gap-4">
                        <div className="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center relative shadow-lg shadow-purple-500/20">
                            <Cpu className="w-5 h-5 text-white" />
                            <div className="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-charcoal-950 rounded-full"></div>
                        </div>
                        <div>
                            <h3 className="text-white font-bold text-sm tracking-tight">Codemistry AI</h3>
                            <div className="flex items-center gap-1.5 mt-0.5">
                                <span className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                <span className="text-white/60 text-[10px] font-bold uppercase tracking-widest">Active Assistant</span>
                            </div>
                        </div>
                    </div>
                    <button
                        onClick={() => setIsOpen(false)}
                        className="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200"
                    >
                        <X className="w-4 h-4" />
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-5 space-y-5 bg-gradient-to-b from-charcoal-50/50 to-white/50 scroll-smooth custom-scrollbar">
                    {messages.map((msg, idx) => (
                        <div key={idx} className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'} animate-in fade-in slide-in-from-bottom-2 duration-300`}>
                            <div className={`flex max-w-[85%] gap-3 ${msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'}`}>
                                <div className={`w-8 h-8 rounded-xl flex shrink-0 items-center justify-center mt-1 shadow-sm ${msg.role === 'user' ? 'bg-purple-600 text-white' : 'bg-white border border-charcoal-100 text-purple-600'}`}>
                                    {msg.role === 'user' ? <User className="w-4 h-4" /> : <Bot className="w-4 h-4" />}
                                </div>

                                <div className={`
                                    p-3.5 text-sm leading-relaxed rounded-2xl break-words
                                    ${msg.role === 'user'
                                        ? 'bg-purple-600 text-white rounded-tr-none shadow-lg shadow-purple-600/10'
                                        : msg.isError
                                            ? 'bg-red-50 text-red-700 border border-red-100 rounded-tl-none'
                                            : 'bg-white border border-charcoal-100 text-charcoal-800 rounded-tl-none shadow-sm'
                                    }
                                `}>
                                    {msg.role === 'user' ? (
                                        <p className="text-sm leading-relaxed whitespace-pre-wrap">{msg.content}</p>
                                    ) : (
                                        <ReactMarkdown components={markdownComponents}>{msg.content}</ReactMarkdown>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}

                    {isLoading && (
                        <div className="flex justify-start animate-in fade-in duration-300">
                            <div className="flex max-w-[85%] gap-3 flex-row">
                                <div className="w-8 h-8 rounded-xl bg-white border border-charcoal-100 text-purple-600 shadow-sm flex shrink-0 items-center justify-center mt-1">
                                    <Bot className="w-4 h-4" />
                                </div>
                                <div className="p-4 bg-white border border-charcoal-100 rounded-2xl rounded-tl-none flex items-center gap-3 shadow-sm">
                                    <div className="flex gap-1">
                                        <span className="w-1.5 h-1.5 rounded-full bg-purple-600 animate-bounce [animation-delay:-0.3s]"></span>
                                        <span className="w-1.5 h-1.5 rounded-full bg-purple-600 animate-bounce [animation-delay:-0.15s]"></span>
                                        <span className="w-1.5 h-1.5 rounded-full bg-purple-600 animate-bounce"></span>
                                    </div>
                                    <span className="text-[11px] text-charcoal-400 font-bold uppercase tracking-wider">Assistant is thinking...</span>
                                </div>
                            </div>
                        </div>
                    )}
                    <div ref={messagesEndRef} />
                </div>

                <div className="p-4 bg-white/50 backdrop-blur-md border-t border-charcoal-100 shrink-0">
                    <form onSubmit={handleSend} className="flex items-center gap-2 relative">
                        <input
                            type="text"
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                            placeholder="How can I help you?"
                            className="flex-1 bg-white border border-charcoal-200 rounded-2xl px-4 pr-14 py-4 text-[13px] font-medium focus:outline-none focus:ring-4 focus:ring-purple-600/10 focus:border-purple-600 transition-all placeholder:text-charcoal-400 shadow-sm"
                            disabled={isLoading}
                        />

                        <button
                            type="submit"
                            disabled={!input.trim() || isLoading}
                            className="absolute right-2 bg-purple-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-purple-600/20 shrink-0 group-form"
                        >
                            <Send className="w-4 h-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </button>
                    </form>
                    <div className="mt-3 flex justify-center items-center gap-2 opacity-50">
                        <div className="h-[1px] w-8 bg-charcoal-200"></div>
                        <span className="text-[10px] text-charcoal-400 font-bold uppercase tracking-widest">Codemistry Intelligence</span>
                        <div className="h-[1px] w-8 bg-charcoal-200"></div>
                    </div>
                </div>
            </div>

            <button
                onClick={() => setIsOpen(!isOpen)}
                className={`
                    group hidden md:flex items-center gap-3 px-6 py-4 rounded-full shadow-2xl transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) relative overflow-hidden
                    ${isOpen
                        ? 'bg-charcoal-950 text-white scale-90 translate-y-2'
                        : 'bg-white text-charcoal-900 hover:scale-105 hover:-translate-y-1 active:scale-95 border border-purple-100 animate-ai-glow'
                    }
                `}
            >
                {!isOpen && <div className="absolute inset-0 bg-gradient-to-r from-purple-50 via-white to-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>}

                <div className="relative flex items-center gap-3 z-10">
                    <div className={`w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-500 ${isOpen ? 'bg-white/10 rotate-180' : 'bg-purple-600 shadow-lg shadow-purple-600/20'}`}>
                        {isOpen ? <X className="w-4 h-4 text-white" /> : <Cpu className="w-4 h-4 text-white group-hover:rotate-12 transition-transform" />}
                    </div>

                    {!isOpen && (
                        <div className="flex flex-col items-start leading-none">
                            <span className="text-[13px] font-extrabold tracking-tight text-charcoal-950">AI Support</span>
                            <div className="flex items-center gap-1.5 mt-1">
                                <span className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                <span className="text-[9px] font-bold text-charcoal-400 uppercase tracking-widest">Online Now</span>
                            </div>
                        </div>
                    )}

                    {isOpen && <span className="text-sm font-bold tracking-tight">Close Assistant</span>}
                </div>

                {!isOpen && (
                    <div className="absolute top-0 right-0 p-1">
                        <Sparkles className="w-3 h-3 text-purple-400/50 animate-pulse" />
                    </div>
                )}
            </button>
        </div>
    );
};

export default Chatbot;
