import { useEffect, useRef } from 'react';
import { useLocation } from 'react-router-dom';
import api from '../api';

const generateSessionId = () => {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
};

export const useAnalytics = () => {
    const location = useLocation();
    const sessionId = useRef(sessionStorage.getItem('analytics_session_id'));
    const timeSpent = useRef(0);
    const trackingInterval = useRef(null);

    useEffect(() => {
        if (!sessionId.current) {
            sessionId.current = generateSessionId();
            sessionStorage.setItem('analytics_session_id', sessionId.current);
        }

        // Reset time spent on route change
        timeSpent.current = 0;

        const trackPing = async () => {
            timeSpent.current += 5; // Ping every 5 seconds

            // Only track public facing routes, not admin pages
            if (location.pathname.startsWith('/admin')) return;

            try {
                // Determine if we should use the authenticated api instance or a raw fetch
                // We'll use the api instance, but fire and forget to avoid blocking UI
                api.post('/analytics/track', {
                    session_id: sessionId.current,
                    path: location.pathname,
                    time_spent: timeSpent.current,
                    referrer: document.referrer || ''
                }).catch(() => { /* mute tracking errors */ });
            } catch (e) {
                // Silently ignore tracking errors
            }
        };

        // Initial ping
        trackPing();

        // Setup interval for continuous pinging
        if (trackingInterval.current) clearInterval(trackingInterval.current);
        trackingInterval.current = setInterval(trackPing, 5000);

        return () => {
            if (trackingInterval.current) clearInterval(trackingInterval.current);
        };
    }, [location.pathname]);
};
