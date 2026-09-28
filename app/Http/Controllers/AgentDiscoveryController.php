<?php

namespace App\Http\Controllers;

use App\Mcp\Servers\SiteServer;
use App\Support\Agents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Discovery documents that agent scanners and clients fetch: llms.txt,
 * auth.md, OpenAPI, the RFC 9727 API catalog, the ARD ai-catalog, the MCP
 * server card, the Agent Skills index, and OAuth metadata (RFC 8414, 9728).
 * Every URL they advertise is a real route in routes/ai.php or web.php.
 */
class AgentDiscoveryController extends Controller
{
    public function llms(): Response
    {
        $pages = array_map(fn (array $page) => "- [{$page['title']}]({$page['url']}): {$page['description']}", Agents::pages());

        $body = implode("\n", [
            '# '.site('brand'),
            '',
            '> '.site('home.seo.description'),
            '',
            'Every page below returns markdown when requested with `Accept: text/markdown`. The MCP server at '.url('/mcp').' lists and reads the same pages.',
            '',
            '## Pages',
            '',
            ...$pages,
            '',
            '## Agents',
            '',
            '- [API and agent docs]('.route('docs.api').'): MCP, OAuth, and discovery endpoints',
            '- [MCP server card]('.route('well-known.mcp-server-card').'): tools and transport',
            '- [OpenAPI]('.route('openapi').'): HTTP surface',
            '- [auth.md]('.route('auth.md').'): how agents register and get a token',
            '- [Agent skills]('.route('well-known.agent-skills').'): task instructions for this site',
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8', 'Access-Control-Allow-Origin' => '*']);
    }

    public function authMd(): Response
    {
        return response(Agents::document('auth.md'), 200, ['Content-Type' => 'text/markdown; charset=UTF-8', 'Access-Control-Allow-Origin' => '*']);
    }

    public function apiCatalog(): Response
    {
        return $this->json([
            'linkset' => [
                [
                    'anchor' => url('/'),
                    'service-desc' => [['href' => route('openapi'), 'type' => 'application/openapi+json']],
                    'service-doc' => [['href' => route('docs.api'), 'type' => 'text/html']],
                    'status' => [['href' => url('/up')]],
                ],
                [
                    'anchor' => route('mcp'),
                    'service-desc' => [['href' => route('well-known.mcp-server-card'), 'type' => 'application/json']],
                    'service-doc' => [['href' => route('docs.api'), 'type' => 'text/html']],
                    'status' => [['href' => url('/up')]],
                ],
            ],
        ], 'application/linkset+json');
    }

    public function openapi(): Response
    {
        $json = ['application/json' => ['schema' => ['type' => 'object']]];
        $form = ['application/x-www-form-urlencoded' => ['schema' => ['type' => 'object']]];

        return $this->json([
            'openapi' => '3.1.0',
            'info' => [
                'title' => site('brand'),
                'version' => '1.0.0',
                'description' => site('docs.api.seo.description'),
            ],
            'servers' => [['url' => url('/')]],
            'paths' => [
                '/mcp' => ['post' => [
                    'summary' => 'MCP server (Streamable HTTP, JSON-RPC 2.0). Reading is public; send_inquiry needs a bearer token.',
                    'operationId' => 'mcp',
                    'requestBody' => ['required' => true, 'content' => $json],
                    'responses' => ['200' => ['description' => 'JSON-RPC response']],
                ]],
                '/oauth/register' => ['post' => [
                    'summary' => 'Register an agent client (RFC 7591)',
                    'operationId' => 'registerClient',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => [
                        'type' => 'object',
                        'required' => ['client_name'],
                        'properties' => ['client_name' => ['type' => 'string']],
                    ]]]],
                    'responses' => ['201' => ['description' => 'client_id and client_secret']],
                ]],
                '/oauth/token' => ['post' => [
                    'summary' => 'Exchange client credentials for a bearer token (client_credentials grant)',
                    'operationId' => 'issueToken',
                    'requestBody' => ['required' => true, 'content' => $form],
                    'responses' => ['200' => ['description' => 'Access token'], '401' => ['description' => 'invalid_client']],
                ]],
                '/oauth/revoke' => ['post' => [
                    'summary' => 'Revoke a token (RFC 7009)',
                    'operationId' => 'revokeToken',
                    'requestBody' => ['required' => true, 'content' => $form],
                    'responses' => ['200' => ['description' => 'Revoked or unknown']],
                ]],
                '/{page}' => ['get' => [
                    'summary' => 'Any public page. Send Accept: text/markdown for markdown.',
                    'operationId' => 'getPage',
                    'parameters' => [['name' => 'page', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                    'responses' => ['200' => ['description' => 'Page', 'content' => ['text/html' => (object) [], 'text/markdown' => (object) []]]],
                ]],
                '/llms.txt' => ['get' => ['summary' => 'Site index for language models', 'operationId' => 'llms', 'responses' => ['200' => ['description' => 'Markdown index']]]],
                '/sitemap.xml' => ['get' => ['summary' => 'Sitemap', 'operationId' => 'sitemap', 'responses' => ['200' => ['description' => 'XML sitemap']]]],
                '/up' => ['get' => ['summary' => 'Health check', 'operationId' => 'health', 'responses' => ['200' => ['description' => 'Healthy']]]],
            ],
            'components' => ['securitySchemes' => ['agentToken' => [
                'type' => 'oauth2',
                'flows' => ['clientCredentials' => [
                    'tokenUrl' => route('oauth.token'),
                    'scopes' => config('agents.scopes'),
                ]],
            ]]],
        ], 'application/openapi+json');
    }

    public function aiCatalog(): Response
    {
        $host = Agents::host();

        // ARD discovery media types only; the OpenAPI document is in the RFC 9727 api-catalog.
        $entries = [
            [
                'identifier' => "urn:air:{$host}:mcp:site",
                'displayName' => site('brand').' MCP server',
                'type' => 'application/mcp-server-card+json',
                'url' => route('well-known.mcp-server-card'),
                'representativeQueries' => site('agents.queries.mcp'),
            ],
        ];

        foreach (Agents::skills() as $skill) {
            $entries[] = [
                'identifier' => "urn:air:{$host}:skill:{$skill['name']}",
                'displayName' => $skill['description'],
                'type' => 'text/markdown; profile="urn:air:agent-skills"',
                'url' => $skill['url'],
                'representativeQueries' => site("agents.queries.skills.{$skill['name']}", [$skill['description']]),
            ];
        }

        return $this->json([
            'specVersion' => '1.0',
            'host' => ['displayName' => site('brand'), 'identifier' => "did:web:{$host}"],
            'entries' => $entries,
        ], 'application/json');
    }

    public function serverCard(): JsonResponse
    {
        return response()->json([
            '$schema' => 'https://static.modelcontextprotocol.io/schemas/mcp-server-card/v1.json',
            'version' => '1.0',
            'protocolVersion' => '2025-11-25',
            'serverInfo' => [
                'name' => site('brand'),
                'title' => site('brand').' MCP',
                'version' => '1.0.0',
            ],
            'description' => site('agents.mcp_description'),
            'documentationUrl' => route('docs.api'),
            'transport' => ['type' => 'streamable-http', 'endpoint' => route('mcp')],
            'endpoint' => route('mcp'),
            'capabilities' => ['tools' => ['listChanged' => false]],
            'authentication' => [
                'required' => false,
                'schemes' => ['oauth2'],
                'protectedResourceMetadata' => route('well-known.oauth-protected-resource'),
            ],
            'tools' => SiteServer::toolDefinitions(),
            'resources' => [],
            'prompts' => [],
        ], 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_SLASHES);
    }

    public function skills(): JsonResponse
    {
        return response()->json([
            '$schema' => 'https://schemas.agentskills.io/discovery/0.2.0/schema.json',
            'skills' => array_values(array_map(fn (array $skill) => [
                'name' => $skill['name'],
                'type' => 'skill-md',
                'description' => $skill['description'],
                'url' => $skill['url'],
                'digest' => $skill['digest'],
            ], Agents::skills())),
        ], 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_SLASHES);
    }

    public function skill(string $name): Response
    {
        $skill = Agents::skills()[$name] ?? abort(404);

        return response($skill['body'], 200, ['Content-Type' => 'text/markdown; charset=UTF-8', 'Access-Control-Allow-Origin' => '*']);
    }

    /**
     * RFC 8414. The same origin is the authorization server and the resource,
     * so the issuer matches authorization_servers in the protected resource document.
     */
    public function authorizationServer(): JsonResponse
    {
        return response()->json([
            'issuer' => url('/'),
            'authorization_endpoint' => route('oauth.authorize'),
            'token_endpoint' => route('oauth.token'),
            'registration_endpoint' => route('oauth.register'),
            'revocation_endpoint' => route('oauth.revoke'),
            'jwks_uri' => route('well-known.jwks'),
            'scopes_supported' => array_keys(config('agents.scopes')),
            // No user login, so no authorization-endpoint response types.
            'response_types_supported' => [],
            'grant_types_supported' => ['client_credentials'],
            'token_endpoint_auth_methods_supported' => ['client_secret_post', 'client_secret_basic'],
            'revocation_endpoint_auth_methods_supported' => ['client_secret_post', 'client_secret_basic'],
            'service_documentation' => route('docs.api'),
            'agent_auth' => [
                'skill' => route('auth.md'),
                'register_uri' => route('oauth.register'),
                'identity_types_supported' => ['anonymous'],
                'anonymous' => [
                    'credential_types_supported' => ['client_credentials', 'bearer'],
                    'claim_uri' => route('oauth.register'),
                    'revocation_uri' => route('oauth.revoke'),
                ],
            ],
        ], 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_SLASHES);
    }

    /**
     * RFC 9728.
     */
    public function protectedResource(): JsonResponse
    {
        return response()->json([
            'resource' => url('/'),
            'authorization_servers' => [url('/')],
            'scopes_supported' => array_keys(config('agents.scopes')),
            'bearer_methods_supported' => ['header'],
            'resource_name' => site('brand'),
            'resource_documentation' => route('docs.api'),
        ], 200, ['Access-Control-Allow-Origin' => '*'], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Access tokens are opaque (Sanctum), not JWTs, so there is no signing key
     * to publish. An empty key set is the honest answer.
     */
    public function jwks(): JsonResponse
    {
        return response()->json(['keys' => []], 200, ['Access-Control-Allow-Origin' => '*']);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function json(array $body, string $type): Response
    {
        return response(
            json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => $type, 'Access-Control-Allow-Origin' => '*'],
        );
    }
}
