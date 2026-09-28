<?php

/*
 * AI discoverability defaults: robots.txt policy, Content Signals, and the
 * OAuth tokens agents use for write tools. Read by App\Support\Agents.
 */

return [

    // Default: AI search and assistants may read and cite the site; model
    // training may not. true also lets the training crawlers below in.
    'allow_training' => (bool) env('AGENTS_ALLOW_TRAINING', false),

    // Named explicitly in robots.txt (scanners want explicit AI entries).
    'crawlers' => [
        // Search indexes and user-initiated fetches: always allowed.
        'search' => [
            'OAI-SearchBot', 'ChatGPT-User', 'Claude-SearchBot', 'Claude-User', 'Claude-Web',
            'PerplexityBot', 'Perplexity-User', 'DuckAssistBot', 'MistralAI-User',
        ],
        // Training crawlers: allowed only when allow_training is true.
        'training' => [
            'GPTBot', 'ClaudeBot', 'anthropic-ai', 'Google-Extended', 'Applebot-Extended',
            'Amazonbot', 'CCBot', 'Bytespider', 'meta-externalagent',
        ],
    ],

    // Lifetime of client_credentials access tokens, in seconds.
    'token_ttl' => (int) env('AGENTS_TOKEN_TTL', 3600),

    // Scopes an agent token can carry; tools check them with tokenCan().
    'scopes' => [
        'inquiries:write' => 'Send contact inquiries through the send_inquiry MCP tool.',
    ],

];
