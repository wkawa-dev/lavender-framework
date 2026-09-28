import Alpine from 'alpinejs';
import { siteNav } from './lib/nav.js';
import { revealOnce } from './lib/reveal.js';
import { proofCountUp } from './lib/count-up.js';
import { registerWebMcpTools } from './lib/webmcp.js';

// Menu exit is 250ms of stepped clip-path; the timer covers dropped frames.
Alpine.data('siteNav', () => siteNav({ exitMs: 300, desktop: '(min-width: 1024px)' }));

window.Alpine = Alpine;
Alpine.start();
registerWebMcpTools();

function boot() {
    revealOnce();
    proofCountUp();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        boot();
        registerWebMcpTools();
    }
});
