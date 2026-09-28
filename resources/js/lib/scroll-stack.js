import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * Scroll-stack cards (cream theme, x-cream::scroll-stack).
 *
 * All card content is server-rendered in Blade (SEO + no-JS safe). This
 * module only owns the scroll handoff between consecutive cards: the
 * incoming card settles from scale 1.1 to 1 while the outgoing card fades.
 * Mirrors the reference scroll-stack component, minus React.
 *
 * Progressive enhancement: tall rows + sticky positioning apply only when
 * `documentElement.scroll-stack-anim` is set, which happens solely under
 * prefers-reduced-motion: no-preference with GSAP loaded. Otherwise the
 * cards render as a plain static stack.
 */
export function initScrollStacks() {
    const sections = [...document.querySelectorAll('.scroll-stack')];

    if (sections.length === 0) {
        return;
    }

    if (! window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
        return;
    }

    document.documentElement.classList.add('scroll-stack-anim');

    const media = gsap.matchMedia();

    media.add('(prefers-reduced-motion: no-preference)', () => {
        const triggers = [];
        const animated = [];

        for (const section of sections) {
            const rows = [...section.querySelectorAll('.scroll-stack__row')];
            const cards = rows
                .map((row) => row.querySelector('.scroll-stack__card'))
                .filter(Boolean);

            if (cards.length < 2) {
                continue;
            }

            animated.push(...cards);
            cards.forEach((card, index) => {
                gsap.set(card, {
                    autoAlpha: 1,
                    scale: index === 0 ? 1 : 1.1,
                    transformOrigin: 'center center',
                });
            });

            cards.slice(0, -1).forEach((card, index) => {
                const nextRow = rows[index + 1];
                const nextCard = cards[index + 1];

                if (! nextRow || ! nextCard) {
                    return;
                }

                const handoff = gsap.timeline({
                    scrollTrigger: {
                        trigger: nextRow,
                        start: 'top bottom+=20%',
                        end: 'top top-=28%',
                        scrub: true,
                        invalidateOnRefresh: true,
                    },
                });

                handoff.to(nextCard, { scale: 1, ease: 'none' }, 0);
                triggers.push(handoff.scrollTrigger);

                const fade = gsap.to(card, {
                    autoAlpha: 0,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: nextRow,
                        start: 'top top+=14%',
                        end: 'top top+=2%',
                        scrub: true,
                        invalidateOnRefresh: true,
                    },
                });
                triggers.push(fade.scrollTrigger);
            });
        }

        return () => {
            for (const trigger of triggers) {
                trigger.kill();
            }

            gsap.set(animated, { clearProps: 'opacity,visibility,scale,transform' });
            document.documentElement.classList.remove('scroll-stack-anim');
        };
    });

    // Fonts shift layout after first paint; recompute trigger positions once
    // everything (including the blended section imagery) has loaded.
    window.addEventListener('load', () => ScrollTrigger.refresh());
}
