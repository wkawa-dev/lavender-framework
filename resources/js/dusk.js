import Alpine from 'alpinejs';
import { siteNav } from './lib/nav.js';
import { revealOnce } from './lib/reveal.js';
import { registerWebMcpTools } from './lib/webmcp.js';

// Menu exit is a 350ms clip-path circle; the timer covers dropped frames.
Alpine.data('siteNav', () => siteNav({ exitMs: 400, desktop: '(min-width: 1024px)' }));

window.Alpine = Alpine;
Alpine.start();
registerWebMcpTools();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', revealOnce);
} else {
    revealOnce();
}

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        revealOnce();
        registerWebMcpTools();
    }
});
