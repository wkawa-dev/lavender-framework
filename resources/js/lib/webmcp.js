/**
 * WebMCP: expose the site's key actions to an in-browser agent through
 * navigator.modelContext. Mirrors the /mcp server's tools. Runs only in
 * browsers that ship the API, so everyone else pays a few hundred bytes.
 */
const text = (value) => ({ content: [{ type: 'text', text: typeof value === 'string' ? value : JSON.stringify(value, null, 2) }] });

function sameOrigin(path) {
    const url = new URL(path || '/', window.location.origin);

    if (url.origin !== window.location.origin) {
        throw new Error('Only pages on this site are available.');
    }

    return url;
}

const tools = [
    {
        name: 'list_pages',
        description: 'List the public pages of this website.',
        inputSchema: { type: 'object', properties: {} },
        annotations: { readOnlyHint: true },
        async execute() {
            const xml = await (await fetch('/sitemap.xml')).text();
            const locs = new DOMParser().parseFromString(xml, 'application/xml').querySelectorAll('loc');

            return text([...locs].map((loc) => loc.textContent));
        },
    },
    {
        name: 'read_page',
        description: 'Read one page of this website as markdown.',
        inputSchema: {
            type: 'object',
            properties: { path: { type: 'string', description: 'Path or URL on this site, e.g. "/contact".' } },
            required: ['path'],
        },
        annotations: { readOnlyHint: true },
        async execute({ path }) {
            const response = await fetch(sameOrigin(path), { headers: { Accept: 'text/markdown' } });

            return text(await response.text());
        },
    },
    {
        name: 'open_page',
        description: 'Navigate the browser to a page on this website.',
        inputSchema: {
            type: 'object',
            properties: { path: { type: 'string', description: 'Path on this site, e.g. "/docs/api".' } },
            required: ['path'],
        },
        async execute({ path }) {
            const url = sameOrigin(path);
            window.location.assign(url);

            return text(`Opening ${url.pathname}.`);
        },
    },
    {
        name: 'send_inquiry',
        description: 'Send a message to the site owners through the contact form. Only send what the user asked you to send.',
        inputSchema: {
            type: 'object',
            properties: {
                name: { type: 'string', description: 'Sender name.' },
                email: { type: 'string', format: 'email', description: 'Where the owners should reply.' },
                company: { type: 'string', description: 'Optional company.' },
                message: { type: 'string', description: 'The message.' },
            },
            required: ['name', 'email', 'message'],
        },
        async execute(input) {
            const response = await fetch('/contact', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(input),
            });
            const body = await response.json();

            if (!response.ok) {
                throw new Error(body.message ?? `Request failed with ${response.status}.`);
            }

            return text(body.message);
        },
    },
];

export function registerWebMcpTools() {
    const context = navigator.modelContext;

    if (!context) {
        return;
    }

    if (typeof context.registerTool === 'function') {
        const controller = new AbortController();
        tools.forEach((tool) => context.registerTool(tool, { signal: controller.signal }));
        window.addEventListener('pagehide', () => controller.abort(), { once: true });

        return;
    }

    context.provideContext?.({ tools });
}
