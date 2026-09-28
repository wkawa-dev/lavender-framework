let scrollStacksQueued = false;

/**
 * Lazy-load the scroll-stack animation (GSAP + ScrollTrigger, ~116 KB raw)
 * only when a `.scroll-stack` section approaches the viewport. The cards are
 * server-rendered and styled as a static stack without JS, so the landing
 * hero/proof FCP/LCP never pays for this chunk.
 */
export function loadScrollStacks() {
    if (scrollStacksQueued) {
        return;
    }

    const target = document.querySelector('.scroll-stack');

    if (! target) {
        return;
    }

    if (! window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
        return;
    }

    scrollStacksQueued = true;

    const start = () => {
        // A failed chunk leaves the static stack, which already holds all content.
        import('./scroll-stack.js').then((m) => m.initScrollStacks()).catch(() => {});
    };

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                io.disconnect();
                start();
            }
        }, { rootMargin: '800px 0px' });

        io.observe(target);

        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(start, { timeout: 4000 });
        } else {
            window.setTimeout(start, 4000);
        }

        return;
    }

    start();
}
