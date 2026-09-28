<?php

namespace Tests\Feature;

use App\Models\AgentClient;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The isitagentready.com checks, one test each, asserted against what the
 * scanner fetches. Plus the flows behind them: MCP, OAuth, markdown.
 */
class AgentReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_robots_names_ai_crawlers_and_declares_content_signals(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();

        foreach (['GPTBot', 'OAI-SearchBot', 'Claude-Web', 'Google-Extended', 'Amazonbot', 'anthropic-ai', 'Bytespider', 'CCBot', 'Applebot-Extended'] as $bot) {
            $this->assertStringContainsString("User-agent: {$bot}\n", $robots);
        }

        $this->assertStringContainsString("User-agent: *\nContent-Signal: search=yes, ai-input=yes, ai-train=no\n", $robots);
        $this->assertStringContainsString("User-agent: OAI-SearchBot\nAllow: /", $robots);
        $this->assertStringContainsString("User-agent: GPTBot\nDisallow: /", $robots);
        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $robots);
    }

    public function test_allowing_training_flips_the_signal_and_the_training_crawlers(): void
    {
        config(['agents.allow_training' => true]);

        $robots = $this->get('/robots.txt')->getContent();

        $this->assertStringContainsString('ai-train=yes', $robots);
        $this->assertStringContainsString("User-agent: GPTBot\nAllow: /", $robots);
    }

    #[DataProvider('pages')]
    public function test_pages_advertise_discovery_link_headers(string $path): void
    {
        $link = (string) $this->get($path)->assertOk()->headers->get('Link');

        foreach (['api-catalog', 'service-desc', 'service-doc', 'describedby', 'status'] as $rel) {
            $this->assertStringContainsString("rel=\"{$rel}\"", $link);
        }

        preg_match_all('/<([^>]+)>/', $link, $targets);
        foreach ($targets[1] as $target) {
            $this->assertNotSame(404, $this->get($target)->getStatusCode(), "Link target {$target} must exist.");
        }
    }

    #[DataProvider('pages')]
    public function test_pages_negotiate_markdown(string $path): void
    {
        $response = $this->get($path, ['Accept' => 'text/markdown'])->assertOk();

        $this->assertStringStartsWith('text/markdown', (string) $response->headers->get('Content-Type'));
        $this->assertGreaterThan(0, (int) $response->headers->get('X-Markdown-Tokens'));
        $this->assertStringStartsWith('# ', $response->getContent());
        $this->assertDoesNotMatchRegularExpression('/<(div|span|section|nav|main|header|footer)\b/', $response->getContent(), 'No HTML left over.');
        $this->assertStringContainsString('Accept', (string) $response->headers->get('Vary'));
    }

    public function test_browsers_still_get_html(): void
    {
        $this->get('/', ['Accept' => 'text/html,application/xhtml+xml,*/*;q=0.8'])
            ->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=utf-8')
            ->assertHeader('Vary', 'Accept');
    }

    public function test_api_catalog_is_an_rfc_9727_linkset(): void
    {
        $response = $this->get('/.well-known/api-catalog')->assertOk()->assertHeader('Content-Type', 'application/linkset+json');

        foreach ($response->json('linkset') as $entry) {
            $this->assertNotEmpty($entry['anchor']);
            $this->assertNotEmpty($entry['service-desc'][0]['href']);
            $this->assertNotEmpty($entry['service-doc'][0]['href']);
            $this->assertNotEmpty($entry['status'][0]['href']);
        }

        $this->get('/openapi.json')->assertOk()->assertHeader('Content-Type', 'application/openapi+json')->assertJsonPath('openapi', '3.1.0');
    }

    public function test_oauth_metadata_is_consistent(): void
    {
        $server = $this->get('/.well-known/oauth-authorization-server')->assertOk();
        $resource = $this->get('/.well-known/oauth-protected-resource')->assertOk();

        foreach (['issuer', 'authorization_endpoint', 'token_endpoint', 'jwks_uri', 'grant_types_supported', 'response_types_supported'] as $key) {
            $this->assertArrayHasKey($key, $server->json());
        }

        $this->assertSame([$server->json('issuer')], $resource->json('authorization_servers'));
        $this->assertSame(['header'], $resource->json('bearer_methods_supported'));
        $this->assertNotEmpty($resource->json('scopes_supported'));
        $this->assertSame(route('auth.md'), $server->json('agent_auth.skill'));
        $this->assertSame(['anonymous'], $server->json('agent_auth.identity_types_supported'));
        $this->assertNotEmpty($server->json('agent_auth.anonymous.claim_uri'));

        $this->get('/.well-known/jwks.json')->assertOk()->assertExactJson(['keys' => []]);
    }

    public function test_auth_md_has_the_marker_heading(): void
    {
        $body = $this->get('/auth.md')->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')->getContent();

        $this->assertMatchesRegularExpression('/^# .*auth\.md/m', $body);
        $this->assertStringNotContainsString('{url}', $body);
    }

    public function test_mcp_server_card_matches_the_server(): void
    {
        $card = $this->get('/.well-known/mcp/server-card.json')->assertOk();

        $this->assertSame(site('brand'), $card->json('serverInfo.name'));
        $this->assertNotEmpty($card->json('serverInfo.version'));
        $this->assertSame(route('mcp'), $card->json('transport.endpoint'));
        $this->assertArrayHasKey('tools', $card->json('capabilities'));

        $listed = collect($this->mcp('tools/list')->json('result.tools'))->pluck('name')->all();
        $this->assertSame($listed, collect($card->json('tools'))->pluck('name')->all());
    }

    public function test_agent_skills_index_digests_match_the_served_files(): void
    {
        $index = $this->get('/.well-known/agent-skills/index.json')->assertOk();

        $this->assertSame('https://schemas.agentskills.io/discovery/0.2.0/schema.json', $index->json('$schema'));
        $this->assertNotEmpty($index->json('skills'));

        foreach ($index->json('skills') as $skill) {
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $skill['name']);
            $this->assertSame('skill-md', $skill['type']);
            $this->assertNotEmpty($skill['description']);

            $body = $this->get($skill['url'])->assertOk()->getContent();
            $this->assertSame('sha256:'.hash('sha256', $body), $skill['digest']);
        }
    }

    public function test_ai_catalog_entries_are_valid_ard(): void
    {
        $catalog = $this->get('/.well-known/ai-catalog.json')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertHeader('Access-Control-Allow-Origin', '*');

        $this->assertNotEmpty($catalog->json('specVersion'));
        $this->assertNotEmpty($catalog->json('host.displayName'));
        $this->assertNotEmpty($catalog->json('host.identifier'));

        foreach ($catalog->json('entries') as $entry) {
            $this->assertMatchesRegularExpression('/^urn:air:[^:]+:[a-z]+:[a-z0-9-]+$/', $entry['identifier']);
            $this->assertNotEmpty($entry['displayName']);
            $this->assertNotEmpty($entry['type']);
            $this->assertTrue(isset($entry['url']) xor isset($entry['data']), 'Exactly one of url or data.');
            $this->assertThat(count($entry['representativeQueries']), $this->logicalAnd($this->greaterThanOrEqual(2), $this->lessThanOrEqual(5)));
        }
    }

    public function test_head_links_the_ai_catalog_and_webmcp_ships_in_both_themes(): void
    {
        $this->get('/')->assertSee('<link rel="ai-catalog" href="/.well-known/ai-catalog.json">', false);

        foreach (['cream', 'pixel'] as $theme) {
            $this->assertStringContainsString('registerWebMcpTools()', file_get_contents(resource_path("js/{$theme}.js")));
        }
    }

    public function test_llms_txt_lists_every_page(): void
    {
        $body = $this->get('/llms.txt')->assertOk()->getContent();

        $this->assertStringStartsWith('# '.site('brand'), $body);
        foreach (['home', 'docs.api', 'contact', 'privacy'] as $name) {
            $this->assertStringContainsString('('.route($name).')', $body);
        }
    }

    public function test_mcp_lists_and_reads_pages_without_auth(): void
    {
        $this->mcp('initialize', ['protocolVersion' => '2025-11-25', 'capabilities' => (object) [], 'clientInfo' => ['name' => 'test', 'version' => '1']])
            ->assertJsonPath('result.serverInfo.name', site('brand'));

        $this->assertSame(
            ['list_pages', 'read_page', 'send_inquiry'],
            collect($this->mcp('tools/list')->json('result.tools'))->pluck('name')->all(),
        );

        $this->assertStringContainsString(route('contact'), $this->mcp('tools/call', ['name' => 'list_pages', 'arguments' => (object) []])->json('result.content.0.text'));

        $read = $this->mcp('tools/call', ['name' => 'read_page', 'arguments' => ['path' => '/privacy']]);
        $this->assertStringStartsWith('# '.site('privacy.seo.title'), $read->json('result.content.0.text'));

        $this->mcp('tools/call', ['name' => 'read_page', 'arguments' => ['path' => '/nope']])->assertJsonPath('result.isError', true);
    }

    public function test_send_inquiry_requires_a_token_with_the_scope(): void
    {
        $args = ['name' => 'send_inquiry', 'arguments' => ['name' => 'Ada', 'email' => 'ADA@example.com', 'message' => 'Hello']];

        $this->mcp('tools/call', $args)->assertJsonPath('result.isError', true);
        $this->assertSame(0, Inquiry::count());

        $client = AgentClient::factory()->create();
        $scopeless = $client->createToken('t', [], now()->addHour())->plainTextToken;
        $this->mcp('tools/call', $args, $scopeless)->assertJsonPath('result.isError', true);

        $token = $client->createToken('t', ['inquiries:write'], now()->addHour())->plainTextToken;
        $this->mcp('tools/call', $args, $token)->assertJsonPath('result.isError', false);
        $this->assertDatabaseHas('inquiries', ['email' => 'ada@example.com', 'message' => 'Hello']);

        $expired = $client->createToken('t', ['inquiries:write'], now()->subMinute())->plainTextToken;
        $this->mcp('tools/call', $args, $expired)->assertJsonPath('result.isError', true);
    }

    public function test_client_credentials_flow(): void
    {
        $registered = $this->postJson('/oauth/register', ['client_name' => 'Test agent'])
            ->assertCreated()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('grant_types', ['client_credentials']);

        $credentials = ['client_id' => $registered->json('client_id'), 'client_secret' => $registered->json('client_secret')];

        $this->post('/oauth/token', ['grant_type' => 'client_credentials', 'client_id' => $credentials['client_id'], 'client_secret' => 'wrong'])
            ->assertUnauthorized()
            ->assertJsonPath('error', 'invalid_client');
        $this->post('/oauth/token', ['grant_type' => 'password'] + $credentials)->assertStatus(400)->assertJsonPath('error', 'unsupported_grant_type');
        $this->post('/oauth/token', ['grant_type' => 'client_credentials', 'scope' => 'admin'] + $credentials)->assertStatus(400)->assertJsonPath('error', 'invalid_scope');

        $token = $this->withBasicAuth($credentials['client_id'], $credentials['client_secret'])
            ->post('/oauth/token', ['grant_type' => 'client_credentials'])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('expires_in', config('agents.token_ttl'))
            ->json('access_token');

        $this->flushHeaders();
        $this->post('/oauth/revoke', ['token' => $token] + $credentials)->assertOk();
        $this->mcp('tools/call', ['name' => 'send_inquiry', 'arguments' => ['name' => 'A', 'email' => 'a@b.co', 'message' => 'x']], $token)
            ->assertJsonPath('result.isError', true);

        $this->get('/oauth/authorize')->assertStatus(400)->assertJsonPath('error', 'unsupported_response_type');
    }

    public function test_api_docs_page_documents_every_discovery_url(): void
    {
        $page = $this->get('/docs/api')->assertOk();

        foreach (['/.well-known/api-catalog', '/openapi.json', '/.well-known/mcp/server-card.json', '/.well-known/agent-skills/index.json', '/.well-known/ai-catalog.json', '/auth.md', '/llms.txt'] as $path) {
            $page->assertSee(url($path), false);
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pages(): array
    {
        return ['home' => ['/'], 'docs' => ['/docs/api'], 'contact' => ['/contact'], 'privacy' => ['/privacy']];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function mcp(string $method, array $params = [], ?string $token = null): TestResponse
    {
        $headers = ['Accept' => 'application/json, text/event-stream', 'MCP-Protocol-Version' => '2025-11-25'];

        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        app('auth')->forgetGuards();

        return $this->postJson('/mcp', ['jsonrpc' => '2.0', 'id' => 1, 'method' => $method] + ($params ? ['params' => $params] : []), $headers)->assertOk();
    }
}
