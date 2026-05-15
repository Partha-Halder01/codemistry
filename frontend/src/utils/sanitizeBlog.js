import DOMPurify from 'dompurify';

/**
 * Sanitize admin-supplied blog HTML.
 * Allows style/class so authors can style content, but blocks scripts and event handlers.
 */
export const sanitizeBlogHtml = (html) =>
    DOMPurify.sanitize(html || '', {
        ADD_ATTR: ['target', 'rel', 'class', 'style'],
        FORBID_TAGS: ['script', 'iframe', 'object', 'embed', 'form'],
        FORBID_ATTR: ['onerror', 'onclick', 'onload', 'onmouseover', 'onfocus', 'onsubmit'],
    });

/**
 * Scope admin-written CSS to a single blog post container.
 *
 * Every selector in every rule gets prefixed with `.<scopeClass>` so the styles
 * cannot leak out and affect the rest of the site. Selectors that already
 * start with the scope class are left alone (idempotent).
 *
 * @-rules (@media, @supports, @keyframes) are passed through; selectors *inside*
 * @media / @supports also get scoped because the same regex runs over the inner
 * block. @keyframes step names ("from", "to", "50%") are scoped too, but the
 * keyframes definition itself still works because the scope-class prefix on a
 * step name makes it an invalid step — so as a safety net we skip @keyframes
 * blocks entirely.
 */
export const scopeBlogCss = (css, scopeClass) => {
    if (!css || !scopeClass) return css || '';
    const prefix = '.' + scopeClass;

    // Strip @keyframes blocks first (preserve, don't scope) — re-attach at end.
    const keyframes = [];
    const stripped = css.replace(/@(?:-webkit-|-moz-)?keyframes[^{]+\{(?:[^{}]*\{[^{}]*\}\s*)*\}/g, (m) => {
        keyframes.push(m);
        return `\n/*__KF_${keyframes.length - 1}__*/\n`;
    });

    // Now scope every remaining rule.
    const scoped = stripped.replace(
        /([^{}@/][^{}]*?)\{([^{}]*)\}/g,
        (match, selectorList, body) => {
            const scopedSelectors = selectorList
                .split(',')
                .map((s) => {
                    const sel = s.trim();
                    if (!sel) return '';
                    if (sel.startsWith(prefix)) return sel;
                    // host selectors targeting body/html — anchor to the scope itself
                    if (/^(body|html)\b/i.test(sel)) return prefix;
                    return `${prefix} ${sel}`;
                })
                .filter(Boolean)
                .join(', ');
            return `${scopedSelectors} { ${body.trim()} }`;
        }
    );

    // Re-attach keyframes
    return scoped.replace(/\/\*__KF_(\d+)__\*\//g, (_, i) => keyframes[Number(i)] || '');
};
