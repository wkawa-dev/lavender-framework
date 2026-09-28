<?php

use App\Http\Controllers\AgentDiscoveryController;
use App\Http\Controllers\OAuthController;
use App\Mcp\Servers\SiteServer;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Facades\Mcp;

/*
 * Machine-facing routes: no session, no CSRF, public CORS on read-only documents.
 * Everything here is advertised from /.well-known/* and the Link header.
 */

Mcp::web('/mcp', SiteServer::class)->middleware('throttle:agents')->name('mcp');

Route::prefix('oauth')->name('oauth.')->controller(OAuthController::class)->group(function (): void {
    Route::post('/register', 'register')->middleware('throttle:agent-register')->name('register');
    Route::middleware('throttle:agents')->group(function (): void {
        Route::get('/authorize', 'authorize')->name('authorize');
        Route::post('/token', 'token')->name('token');
        Route::post('/revoke', 'revoke')->name('revoke');
    });
});

Route::controller(AgentDiscoveryController::class)->middleware('cache.headers:public;max_age=3600;etag')->group(function (): void {
    Route::get('/llms.txt', 'llms')->name('llms');
    Route::get('/auth.md', 'authMd')->name('auth.md');
    Route::get('/openapi.json', 'openapi')->name('openapi');

    Route::prefix('.well-known')->name('well-known.')->group(function (): void {
        Route::get('/api-catalog', 'apiCatalog')->name('api-catalog');
        Route::get('/ai-catalog.json', 'aiCatalog')->name('ai-catalog');
        Route::get('/mcp/server-card.json', 'serverCard')->name('mcp-server-card');
        Route::get('/agent-skills/index.json', 'skills')->name('agent-skills');
        Route::get('/agent-skills/{name}/SKILL.md', 'skill')->where('name', '[a-z0-9-]+')->name('agent-skill');
        Route::get('/oauth-authorization-server', 'authorizationServer')->name('oauth-authorization-server');
        Route::get('/oauth-protected-resource', 'protectedResource')->name('oauth-protected-resource');
        Route::get('/jwks.json', 'jwks')->name('jwks');
    });
});
