<?php

/**
 * All site copy. Templates read it with site('home.hero.headline').
 *
 * Both themes render the same keys, so switching THEME re-skins the site
 * without touching copy. Locale overlays go in resources/copy/{locale}.php
 * and merge over this file. Do not hardcode marketing sentences in Blade.
 */

return [

    'brand' => config('app.name'),

    'nav' => [
        'home' => 'Home',
        'kit' => 'Components',
        'contact' => 'Contact',
        'privacy' => 'Privacy',
        'api' => 'API',
        'get_started' => 'Get started',
        'open_menu' => 'Open menu',
        'close_menu' => 'Close menu',
        'menu' => 'Menu',
        'close' => 'Close',
        'skip' => 'Skip to content',
        'primary' => 'Primary',
        'mobile' => 'Menu',
    ],

    'footer' => [
        'headline' => 'Fast by default. Keep it that way.',
        'tagline' => 'Server-rendered Laravel with three themes, a 100 Lighthouse floor, and agent discovery built in.',
        'copyright' => '© :year :brand',
        'nav' => 'Footer',
        'columns' => [
            'product' => 'Product',
            'company' => 'Company',
        ],
    ],

    'home' => [
        'seo' => [
            'title' => 'Laravel starter that ships at 100 Lighthouse',
            'description' => 'Server-rendered Laravel starter with three themes, self-hosted fonts, responsive WebP, and motion that never blocks first paint.',
        ],
        'hero' => [
            'eyebrow' => 'Laravel starter',
            // Lines split on ", " in cream; `strike` gets a drawn strikethrough.
            'headline' => 'Skip the boilerplate, skip the audit, ship at 100.',
            'strike' => 'boilerplate',
            'lead' => 'Blade, Tailwind v4, and a few kilobytes of Alpine. Fonts self-host at build time, images ship as sized WebP, and heavy motion loads only when it scrolls into view.',
            'cta_primary' => 'Get started',
            'cta_secondary' => 'See components',
        ],
        'proof' => [
            'aria' => 'Starter numbers',
            'items' => [
                ['display' => '100', 'label' => 'Lighthouse in all four categories'],
                ['display' => '0', 'label' => 'Render-blocking font requests'],
                ['display' => '3', 'label' => 'Themes, one set of copy'],
            ],
        ],
        'art' => [
            'top' => 'Request ↓ first byte',
            'bottom' => 'Paint ↑ first frame',
            'rows' => ['HTML', 'Fonts', 'Motion'],
        ],
        'how' => [
            'eyebrow' => 'How it works',
            'meter' => 'Budget used',
            'headline' => 'Three rules.',
            'kicker' => 'Rule',
            'steps' => [
                [
                    'title' => 'HTML first',
                    'accent' => 'Every word is in the server response.',
                    'body' => 'Pages render in Blade. JavaScript enhances what is already there, so crawlers, screen readers, and slow phones get the full page.',
                    'marks' => ['in the server response'],
                    'caption' => 'No client-rendered content',
                    'image' => 'how-01',
                ],
                [
                    'title' => 'Fonts at build time',
                    'accent' => 'No third-party font request at runtime.',
                    'body' => 'Vite downloads the faces, fingerprints them, preloads only the above-fold weights, and generates metric-matched fallbacks so the swap does not shift layout.',
                    'marks' => ['metric-matched fallbacks'],
                    'caption' => 'Zero layout shift on swap',
                    'image' => 'how-02',
                ],
                [
                    'title' => 'Motion on demand',
                    'accent' => 'Heavy libraries wait for the scroll.',
                    'body' => 'CSS drives entrances and reveals. The one GSAP effect loads when its section is 800px away, and reduced motion gets a static page.',
                    'marks' => ['800px away'],
                    'caption' => 'Hero never pays for it',
                    'image' => 'how-03',
                ],
            ],
        ],
        'join' => [
            'headline' => 'Pick a theme, keep the performance budget.',
            'body' => 'Set THEME=cream, THEME=pixel, or THEME=dusk. Every theme renders the same routes and copy, and all of them pass the same tests.',
            'primary' => 'Start a project',
            'secondary' => 'Browse components',
        ],
        'quote' => [
            'body' => 'We deleted the performance ticket from the launch checklist.',
            'name' => 'Your first customer',
            'role' => 'replace me in resources/copy.php',
        ],
        'faq' => [
            'eyebrow' => 'FAQ',
            'headline' => 'Questions',
            'items' => [
                [
                    'q' => 'How do I switch themes?',
                    'a' => 'Set THEME in .env. Locally you can also append ?theme=pixel to any URL to preview.',
                ],
                [
                    'q' => 'Where does the copy live?',
                    'a' => 'resources/copy.php. Blade reads it with site(\'key\'). Keep marketing sentences out of templates.',
                ],
                [
                    'q' => 'How do I add an image?',
                    'a' => 'Drop the original in resources/images and run php artisan images:optimize. It writes 640, 960, and 1280 wide WebP files that <x-image> serves with srcset.',
                ],
                [
                    'q' => 'Can AI agents use the site?',
                    'a' => 'Yes. Every page is also markdown, an MCP server lists and reads pages, and agents can get a token to send messages. The API and agents page lists every discovery URL.',
                ],
                [
                    'q' => 'Can I add React?',
                    'a' => 'You can, but the budget assumes server-rendered HTML and Alpine for small state. Read AGENTS.md before adding a client framework.',
                ],
            ],
        ],
        'cta' => [
            'headline' => 'Ready when you are.',
            'muted' => 'Clone it and ship.',
            'body' => 'Run composer setup, pick a theme, and replace the copy. The tests keep every theme honest.',
            'primary' => 'Contact',
            'secondary' => 'Components',
        ],
    ],

    'contact' => [
        'seo' => [
            'title' => 'Contact',
            'description' => 'Send a message. It is stored in the inquiries table and never leaves your server unless you wire up mail.',
        ],
        'hero' => [
            'eyebrow' => 'Contact',
            'headline' => 'Say hello.',
            'lead' => 'A working form: server validation, lowercase email, a honeypot, and a success state. Messages land in the inquiries table.',
        ],
        'form' => [
            'name' => 'Name',
            'email' => 'Email',
            'company' => 'Company',
            'optional' => '(optional)',
            'message' => 'Message',
            'message_placeholder' => 'What are you building?',
            'honeypot' => 'Leave this field empty',
            'submit' => 'Send message',
            'success_title' => 'Message sent.',
            'success' => 'Thanks. We will reply by email.',
            'privacy_before' => 'We only use your details to reply. See the',
            'privacy_link' => 'privacy notice',
            'privacy_after' => '.',
        ],
    ],

    'kit' => [
        'seo' => [
            'title' => 'Components',
            'description' => 'Every component in the active theme, rendered with real markup.',
        ],
        'hero' => [
            'eyebrow' => 'Kit',
            'headline' => 'Components',
            'lead' => 'Everything the active theme ships. Copy the markup, keep the class names.',
        ],
    ],

    'privacy' => [
        'seo' => [
            'title' => 'Privacy',
            'description' => 'What this site stores and why.',
        ],
        'eyebrow' => 'Legal',
        'headline' => 'Privacy',
    ],

    'docs' => [
        'api' => [
            'seo' => [
                'title' => 'API and agents',
                'description' => 'How AI agents read this site as markdown, use its MCP server, and authenticate to send messages.',
            ],
            'eyebrow' => 'Developers',
            'headline' => 'API and agents',
            'lead' => 'Agents read every page as markdown, call the MCP server, and get a token to write. Everything is discoverable from well-known URLs.',
        ],
    ],

    // Machine-facing text: MCP server card, ai-catalog queries (2-5 per entry).
    'agents' => [
        'mcp_description' => 'List and read this website as markdown, and send the owners a message.',
        // Declarative WebMCP annotations on the contact form.
        'contact_form' => 'Fill in the contact form to message the site owners. The user reviews and submits it.',
        'honeypot' => 'Spam trap. Always leave empty.',
        'queries' => [
            'mcp' => ['what does this site offer', 'read the contact page', 'send the team a message'],
            'skills' => [
                'read-site' => ['summarize this website', 'what does this company do', 'find the privacy policy'],
                'contact-owners' => ['contact the site owners', 'send an inquiry for me'],
            ],
        ],
    ],

    'consent' => [
        'title' => 'Cookies',
        'body' => 'Essential cookies keep the site working. Optional ones help us see which pages are useful.',
        'policy' => 'Privacy notice',
        'essential' => 'Essential only',
        'accept' => 'Accept all',
        'manage' => 'Cookie settings',
    ],

    'errors' => [
        '404' => [
            'title' => 'Page not found',
            'headline' => 'Nothing here.',
            'body' => 'The page moved or never existed.',
            'home' => 'Back home',
        ],
    ],

];
