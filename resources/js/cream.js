import Alpine from 'alpinejs';
import { siteNav } from './lib/nav.js';
import { consentStore } from './lib/consent.js';
import { revealOnce } from './lib/reveal.js';
import { proofCountUp } from './lib/count-up.js';
import { loadScrollStacks } from './lib/lazy-scroll-stack.js';
import { registerWebMcpTools } from './lib/webmcp.js';

Alpine.store('consent', consentStore('cream-consent'));
Alpine.data('siteNav', () => siteNav({ exitMs: 280 }));

window.Alpine = Alpine;
Alpine.start();
registerWebMcpTools();

function boot() {
    revealOnce();
    proofCountUp();
    loadScrollStacks();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

// Back/forward cache restores the page without DOMContentLoaded.
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        boot();
        registerWebMcpTools();
    }
});
