import { useState, useRef, useEffect } from 'react';
import { Send, Bot, User, Loader2, Sparkles, CheckCircle2, ListChecks } from 'lucide-react';
import ReactMarkdown from 'react-markdown';
import api from '../api';

const AiSupport = () => {
    const [messages, setMessages] = useState([
        { role: 'model', content: "Hi there! I'm the Codemistry AI assistant. How can I help you today?" }
    ]);
    const [input, setInput] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const messagesEndRef = useRef(null);
    const textareaRef = useRef(null);

    useEffect(() => {
        window.scrollTo(0, 0);
    }, []);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    useEffect(() => {
        if (!textareaRef.current) return;
        textareaRef.current.style.height = 'auto';
        const nextHeight = Math.min(textareaRef.current.scrollHeight, 140);
        textareaRef.current.style.height = `${nextHeight}px`;
    }, [input]);

    const sendMessage = async (textToSubmit) => {
        const userMsg = textToSubmit.trim();
        if (!userMsg) return;

        setInput('');

        const newMessages = [...messages, { role: 'user', content: userMsg }];
        setMessages(newMessages);
        setIsLoading(true);

        try {
            const history = newMessages.slice(0, -1);
            const response = await api.post('/chat', {
                message: userMsg,
                history,
            });

            let aiText = "I'm sorry, I couldn't understand that.";
            if (response.data?.reply) {
                aiText = response.data.reply;
            }

            setMessages((prev) => [...prev, { role: 'model', content: aiText }]);
        } catch (error) {
            console.error('Chat error details:', error.response?.data || error.message);
            setMessages((prev) => [
                ...prev,
                {
                    role: 'model',
                    content: 'Sorry, I am having trouble connecting to my knowledge base right now. Please try again later.'
                }
            ]);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        sendMessage(input);
    };

    const handleInputKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (input.trim() && !isLoading) {
                sendMessage(input);
            }
        }
    };

    const markdownComponents = {
        h1: ({ children }) => (
            <h1 className="text-base md:text-lg font-bold text-charcoal-950 mb-2 flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>{children}</span>
            </h1>
        ),
        h2: ({ children }) => (
            <h2 className="text-[15px] md:text-base font-bold text-charcoal-950 mb-2 mt-3 flex items-center gap-2">
                <ListChecks className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>{children}</span>
            </h2>
        ),
        h3: ({ children }) => (
            <h3 className="text-sm font-bold text-charcoal-900 mb-1.5 mt-3">{children}</h3>
        ),
        p: ({ children }) => <p className="text-[14px] leading-6 text-charcoal-800 mb-2">{children}</p>,
        strong: ({ children }) => <strong className="font-bold text-charcoal-950">{children}</strong>,
        ul: ({ children }) => <ul className="space-y-1.5 mb-2">{children}</ul>,
        ol: ({ children }) => <ol className="list-decimal pl-5 space-y-1.5 mb-2">{children}</ol>,
        li: ({ children }) => (
            <li className="text-[14px] leading-6 text-charcoal-800 flex items-start gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-600 mt-1 shrink-0" />
                <span>{children}</span>
            </li>
        ),
        code: ({ children }) => (
            <code className="bg-charcoal-100 text-charcoal-900 px-1.5 py-0.5 rounded text-[12px]">{children}</code>
        )
    };

    return (
        <div className="min-h-[calc(100vh-theme(spacing.16))] bg-charcoal-50 pt-20 md:pt-24 pb-24 md:pb-12 px-3 sm:px-4 flex flex-col items-center">
            <div className="max-w-4xl w-full bg-white rounded-2xl shadow-sm border border-charcoal-100 flex flex-col overflow-hidden h-[calc(100vh-9rem)] md:h-[75vh]">
                <div className="bg-emerald-600 p-3.5 md:p-4 flex justify-between items-center text-white rounded-t-2xl shrink-0">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <Bot className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 className="font-display font-semibold text-base md:text-lg leading-tight">AI Support Agent</h3>
                            <p className="text-emerald-100 text-[11px] md:text-xs flex items-center gap-1.5 mt-0.5">
                                <span className="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                Online | Structured replies
                            </p>
                        </div>
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto p-3 md:p-6 space-y-4 md:space-y-6 bg-charcoal-50/50">
                    {messages.map((msg, index) => (
                        <div key={index} className={`flex items-start gap-2.5 md:gap-4 ${msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'}`}>
                            <div className={`w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center shrink-0 ${msg.role === 'user' ? 'bg-emerald-100 text-emerald-700' : 'bg-charcoal-900 text-white'}`}>
                                {msg.role === 'user' ? <User className="w-4 h-4 md:w-5 md:h-5" /> : <Bot className="w-4 h-4 md:w-5 md:h-5" />}
                            </div>

                            <div className={`max-w-[85%] md:max-w-[78%] px-4 md:px-5 py-3 rounded-2xl ${msg.role === 'user'
                                    ? 'bg-emerald-600 text-white rounded-tr-sm shadow-md shadow-emerald-600/10'
                                    : 'bg-white border border-charcoal-100 text-charcoal-900 rounded-tl-sm shadow-sm'
                                }`}>
                                {msg.role === 'user' ? (
                                    <p className="text-[14px] md:text-[15px] leading-relaxed whitespace-pre-wrap font-medium">{msg.content}</p>
                                ) : (
                                    <div className="max-w-none">
                                        <ReactMarkdown components={markdownComponents}>{msg.content}</ReactMarkdown>
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}

                    {isLoading && (
                        <div className="flex items-start gap-3">
                            <div className="w-8 h-8 rounded-full bg-charcoal-900 flex items-center justify-center shrink-0">
                                <Bot className="w-4 h-4 text-white" />
                            </div>
                            <div className="bg-white border border-charcoal-100 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm flex items-center gap-1.5">
                                <span className="w-1.5 h-1.5 rounded-full bg-charcoal-300 animate-bounce" style={{ animationDelay: '0ms' }}></span>
                                <span className="w-1.5 h-1.5 rounded-full bg-charcoal-300 animate-bounce" style={{ animationDelay: '150ms' }}></span>
                                <span className="w-1.5 h-1.5 rounded-full bg-charcoal-300 animate-bounce" style={{ animationDelay: '300ms' }}></span>
                            </div>
                        </div>
                    )}
                    <div ref={messagesEndRef} />
                </div>

                <div className="p-3 md:p-4 bg-white border-t border-charcoal-100 shrink-0">
                    <form onSubmit={handleSubmit} className="relative flex items-end gap-2 max-w-4xl mx-auto">
                        <div className="flex-1">
                            <textarea
                                ref={textareaRef}
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                onKeyDown={handleInputKeyDown}
                                placeholder="Ask me anything about Codemistry..."
                                className="w-full bg-charcoal-50 border border-charcoal-200 rounded-xl px-4 py-3 text-charcoal-900 text-[14px] md:text-[15px] focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all placeholder:text-charcoal-400 resize-none overflow-y-auto min-h-[48px] max-h-[140px]"
                                rows={1}
                                disabled={isLoading}
                                spellCheck={false}
                                autoCorrect="off"
                                autoCapitalize="sentences"
                                inputMode="text"
                                enterKeyHint="send"
                            />
                        </div>

                        <button
                            type="submit"
                            disabled={!input.trim() || isLoading}
                            className="bg-emerald-600 text-white rounded-xl p-3 hover:bg-emerald-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex shadow-sm shadow-emerald-600/20 shrink-0 h-[48px] w-[48px] md:h-[52px] md:w-[52px] items-center justify-center"
                        >
                            {isLoading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Send className="w-5 h-5 ml-0.5" />}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default AiSupport;
