<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RFC 8288 Link headers on every page (RFC 9727 section 3), so an agent that
 * fetches any URL learns where the API catalog, docs, and status live.
 */
class AdvertiseAgentResources
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethodSafe() || $response->headers->has('Link')) {
            return $response;
        }

        $response->headers->set('Link', implode(', ', [
            '</.well-known/api-catalog>; rel="api-catalog"',
            '</openapi.json>; rel="service-desc"; type="application/openapi+json"',
            '</docs/api>; rel="service-doc"',
            '</llms.txt>; rel="describedby"; type="text/plain"',
            '</up>; rel="status"',
        ]));

        return $response;
    }
}
