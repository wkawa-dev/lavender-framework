<?php

namespace App\Http\Middleware;

use App\Support\Agents;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Markdown for Agents: a client that prefers text/markdown over text/html
 * gets the page's content as markdown. Browsers never do, so HTML stays the default.
 */
class NegotiateMarkdown
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! str_starts_with((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $response->setVary(['Accept'], false);

        if ($response->getStatusCode() !== 200 || $request->prefers(['text/html', 'text/markdown']) !== 'text/markdown') {
            return $response;
        }

        $markdown = Agents::toMarkdown((string) $response->getContent(), $request->url());

        $response->setContent($markdown);
        $response->headers->set('Content-Type', 'text/markdown; charset=UTF-8');
        // Cloudflare's Markdown for Agents convention; a rough 4 bytes per token.
        $response->headers->set('X-Markdown-Tokens', (string) max(1, intdiv(strlen($markdown), 4)));

        return $response;
    }
}
