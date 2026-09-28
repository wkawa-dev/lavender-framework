/**
 * Mobile navigation on a native <dialog>.
 *
 * showModal() gives an inert background and focus containment for free.
 * Scroll is locked by pinning <body> (position: fixed) instead of
 * overflow: hidden, which breaks sticky headers on iOS Safari, and the
 * restore jump bypasses `scroll-behavior: smooth` so closing never scrolls
 * visibly. Closing waits for the panel's exit transition, with a timer
 * fallback so a throttled frame can never leave a stuck fullscreen layer.
 *
 * Markup contract (see the theme layout): x-ref="dialog", "panel", "open",
 * "close"; the panel animates while [data-open] is set on the dialog.
 */
export function siteNav({ exitMs = 280, desktop = '(min-width: 768px)' } = {}) {
    return {
        open: false,
        lockedY: 0,
        media: null,
        _onBreakpoint: null,
        _onCancel: null,
        _onNativeClose: null,
        _onPanelEnd: null,
        _exitTimer: null,
        _closing: false,

        init() {
            this.media = window.matchMedia(desktop);
            this._onBreakpoint = () => {
                if (this.media.matches && (this.open || this.$refs.dialog?.open)) {
                    this.close({ restore: 'desktop', immediate: true });
                }
            };
            this.media.addEventListener('change', this._onBreakpoint);

            this._onCancel = (event) => {
                event.preventDefault();
                this.close({ restore: 'toggle' });
            };
            this.$refs.dialog.addEventListener('cancel', this._onCancel);

            this._onNativeClose = () => {
                this.finishClose();
            };
            this.$refs.dialog.addEventListener('close', this._onNativeClose);
        },

        destroy() {
            this.media?.removeEventListener('change', this._onBreakpoint);
            this.$refs.dialog?.removeEventListener('cancel', this._onCancel);
            this.$refs.dialog?.removeEventListener('close', this._onNativeClose);
            this.clearExitWatch();
            this.unlockScroll();
        },

        prefersReducedMotion() {
            return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        },

        lockScroll() {
            this.lockedY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${this.lockedY}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';
            document.body.style.width = '100%';
        },

        unlockScroll() {
            if (document.body.style.position !== 'fixed') {
                return;
            }

            const y = this.lockedY;
            const html = document.documentElement;
            const prevBehavior = html.style.scrollBehavior;
            // Site CSS uses `scroll-behavior: smooth`, which would animate the
            // restore jump (top -> locked position) as a visible scroll. Force
            // an instant jump while body-fixed styles are removed.
            html.style.scrollBehavior = 'auto';

            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';
            window.scrollTo(0, y);

            html.style.scrollBehavior = prevBehavior;
        },

        clearExitWatch() {
            if (this._exitTimer !== null) {
                clearTimeout(this._exitTimer);
                this._exitTimer = null;
            }

            if (this._onPanelEnd && this.$refs.panel) {
                this.$refs.panel.removeEventListener('transitionend', this._onPanelEnd);
                this._onPanelEnd = null;
            }
        },

        openMenu() {
            if (this.media?.matches) {
                return;
            }

            this.clearExitWatch();
            this._closing = false;

            if (this.$refs.dialog.open) {
                this.open = true;
                this.$refs.dialog.dataset.open = '';
                this.$nextTick(() => this.$refs.close?.focus({ preventScroll: true }));

                return;
            }

            this.lockScroll();
            this.$refs.dialog.showModal();
            this.open = true;

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    if (! this.open || ! this.$refs.dialog.open) {
                        return;
                    }

                    this.$refs.dialog.dataset.open = '';
                    this.$refs.close?.focus({ preventScroll: true });
                });
            });
        },

        close({ restore = 'toggle', immediate = false } = {}) {
            if (! this.$refs.dialog?.open && ! this.open) {
                return;
            }

            this.clearExitWatch();
            this._closing = true;
            this.open = false;
            delete this.$refs.dialog.dataset.open;

            const finish = () => {
                if (! this._closing) {
                    return;
                }

                this._closing = false;
                this.clearExitWatch();

                if (this.$refs.dialog.open) {
                    this.$refs.dialog.close();
                }

                // Do not rely on the native `close` event alone: background tabs
                // may never deliver it, which would leave the page scroll-locked.
                this.finishClose();

                this.restoreFocus(restore);
            };

            if (immediate || this.prefersReducedMotion()) {
                finish();

                return;
            }

            this._onPanelEnd = (event) => {
                if (event.target !== this.$refs.panel || event.propertyName !== 'clip-path' && event.propertyName !== 'opacity') {
                    return;
                }

                finish();
            };
            this.$refs.panel.addEventListener('transitionend', this._onPanelEnd);
            this._exitTimer = setTimeout(finish, exitMs);
        },

        finishClose() {
            this.open = false;
            this._closing = false;
            delete this.$refs.dialog?.dataset.open;
            this.unlockScroll();
        },

        restoreFocus(restore) {
            this.$nextTick(() => {
                if (restore === 'desktop') {
                    document.querySelector('[data-nav-desktop-focus]')?.focus({ preventScroll: true });

                    return;
                }

                if (restore === 'toggle') {
                    this.$refs.open?.focus({ preventScroll: true });
                }
            });
        },
    };
}
