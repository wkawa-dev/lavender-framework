/**
 * Count-up for [data-proof-num]. The server renders the final value, so
 * no-JS and reduced motion show the real number; the animation only runs
 * when the parent [data-proof-item] enters the viewport.
 */
export function proofCountUp() {
    const nodes = [...document.querySelectorAll('[data-proof-num]')];

    if (nodes.length === 0) {
        return;
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const parseDisplay = (raw) => {
        const match = String(raw).trim().match(/^(\d+(?:\.\d+)?)(.*)$/);

        if (! match) {
            return null;
        }

        return {
            target: Number(match[1]),
            suffix: match[2] ?? '',
            decimals: (match[1].split('.')[1] ?? '').length,
        };
    };

    const formatStep = (value, decimals, suffix) => {
        const fixed = decimals > 0 ? value.toFixed(decimals) : String(Math.round(value));

        return fixed + suffix;
    };

    const run = (el) => {
        if (el.dataset.proofCounted === '1') {
            return;
        }

        el.dataset.proofCounted = '1';

        const display = el.dataset.proofDisplay ?? el.textContent ?? '';
        const parsed = parseDisplay(display);

        if (! parsed || reduced) {
            el.textContent = display;

            return;
        }

        const duration = 900;
        const start = performance.now();

        el.textContent = formatStep(0, parsed.decimals, parsed.suffix);

        const tick = (now) => {
            const t = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - t, 3);
            const current = parsed.target * eased;

            if (t < 1) {
                el.textContent = formatStep(current, parsed.decimals, parsed.suffix);
                requestAnimationFrame(tick);

                return;
            }

            el.textContent = display;
        };

        requestAnimationFrame(tick);
    };

    const io = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            if (! entry.isIntersecting) {
                continue;
            }

            const num = entry.target.querySelector('[data-proof-num]');

            if (num) {
                run(num);
            }

            io.unobserve(entry.target);
        }
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.12 });

    for (const el of nodes) {
        const item = el.closest('[data-proof-item]') ?? el;
        const rect = item.getBoundingClientRect();
        const inView = rect.top < window.innerHeight * 0.9 && rect.bottom > 0;

        if (reduced || inView) {
            run(el);
            continue;
        }

        io.observe(item);
    }
}
