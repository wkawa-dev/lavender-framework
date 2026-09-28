/**
 * Once-only scroll reveals for [data-reveal].
 *
 * Browsers with animation-timeline: view() animate in CSS; JS only marks
 * nodes already on screen as visible so they never animate at load. Others
 * get IntersectionObserver plus a scroll fallback. Only below-fold nodes are
 * armed (hidden), so above-fold content paints immediately: no LCP delay,
 * and no-JS visitors see everything.
 */
export function revealOnce() {
    const nodes = [...document.querySelectorAll('[data-reveal]')].filter(
        (el) => ! el.hasAttribute('data-visible'),
    );

    if (nodes.length === 0) {
        return;
    }

    const show = (el) => {
        el.setAttribute('data-visible', '');
        el.classList.remove('reveal-armed');
    };

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        nodes.forEach(show);

        return;
    }

    // Layout box, not painted/clipped area — clip-path and scaleX(0) zero out
    // IntersectionObserver's intersectionRatio, so isIntersecting never flips.
    const inRevealZone = (el) => {
        const rect = el.getBoundingClientRect();
        const vh = window.innerHeight || document.documentElement.clientHeight;

        return rect.top < vh * 0.9 && rect.bottom > vh * 0.1;
    };

    // Prefer CSS scroll-driven animations (app.css @supports animation-timeline).
    // Do not arm those nodes — reveal-armed disables the view() path.
    if (typeof CSS !== 'undefined' && CSS.supports('animation-timeline', 'view()')) {
        for (const el of nodes) {
            if (inRevealZone(el)) {
                show(el);
            }
        }

        return;
    }

    if (typeof IntersectionObserver !== 'function') {
        nodes.forEach(show);

        return;
    }

    const pending = new Set();
    let listening = false;
    let io;

    const detach = () => {
        if (! listening) {
            return;
        }

        listening = false;
        window.removeEventListener('scroll', flush, true);
        window.removeEventListener('resize', flush);
        io?.disconnect();
    };

    const flush = () => {
        for (const el of [...pending]) {
            if (! inRevealZone(el)) {
                continue;
            }

            show(el);
            pending.delete(el);
            io?.unobserve(el);
        }

        if (pending.size === 0) {
            detach();
        }
    };

    io = new IntersectionObserver(flush, {
        rootMargin: '0px 0px -10% 0px',
        threshold: [0, 0.01, 0.12, 1],
    });

    for (const el of nodes) {
        if (inRevealZone(el)) {
            show(el);
            continue;
        }

        el.classList.add('reveal-armed');
        pending.add(el);
        io.observe(el);
    }

    if (pending.size === 0) {
        return;
    }

    // Scroll/resize fallback: IO may never notify when clip-path keeps ratio at 0.
    listening = true;
    window.addEventListener('scroll', flush, { passive: true, capture: true });
    window.addEventListener('resize', flush, { passive: true });
    flush();
}
