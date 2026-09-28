<?php

return [

    // false emits noindex and Disallow: / (staging, previews). Lighthouse SEO needs true.
    'indexable' => (bool) env('SITE_INDEXABLE', true),

    // Optional CDN origin for public/images (e.g. an R2 custom domain).
    'asset_url' => env('ASSET_CDN_URL', ''),

    // Absolute URL of a 1200x630 PNG for og:image; empty omits the tags.
    'og_image' => env('SITE_OG_IMAGE', ''),

    // Bottom consent banner + $store.consent. Off until you load optional scripts.
    'consent_banner' => (bool) env('SITE_CONSENT_BANNER', false),

];
