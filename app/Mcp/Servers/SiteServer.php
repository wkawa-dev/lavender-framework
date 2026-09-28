<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\ListPagesTool;
use App\Mcp\Tools\ReadPageTool;
use App\Mcp\Tools\SendInquiryTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\ServerContext;

/**
 * The site's public MCP server at /mcp (Streamable HTTP). Reading needs no
 * auth; send_inquiry needs a client_credentials bearer token (see /auth.md).
 * /.well-known/mcp/server-card.json is generated from this class.
 */
#[Version('1.0.0')]
#[Instructions('Read this website as markdown and contact its owners. Call list_pages, then read_page for the pages you need. send_inquiry needs a bearer token: register at /oauth/register and exchange the client credentials at /oauth/token (details at /auth.md).')]
class SiteServer extends Server
{
    protected array $tools = [
        ListPagesTool::class,
        ReadPageTool::class,
        SendInquiryTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];

    public function createContext(): ServerContext
    {
        $this->name = config('app.name');

        return parent::createContext();
    }

    /**
     * Tool definitions for the server card, without booting a transport.
     *
     * @return list<array<string, mixed>>
     */
    public static function toolDefinitions(): array
    {
        return array_map(fn (string $tool) => app($tool)->toArray(), (new \ReflectionClass(static::class))->getDefaultProperties()['tools']);
    }
}
