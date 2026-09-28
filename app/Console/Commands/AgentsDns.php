<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * DNS-AID and ARD records are DNS, not HTTP, so the app cannot serve them.
 * This prints the zone lines to add at your DNS provider for APP_URL's host.
 */
#[Signature('agents:dns {--host= : Domain to publish under (defaults to the APP_URL host)}')]
#[Description('Print the DNS-AID and ai-catalog DNS records for agent discovery')]
class AgentsDns extends Command
{
    public function handle(): int
    {
        $host = $this->option('host') ?: (string) parse_url((string) config('app.url'), PHP_URL_HOST);

        if ($host === '' || in_array($host, ['localhost', '127.0.0.1'], true)) {
            $this->error('Set APP_URL to the public domain first, or pass --host=example.com.');

            return self::FAILURE;
        }

        $this->line("; DNS for AI Discovery (draft-mozleywilliams-dnsop-dnsaid): the MCP server at https://{$host}/mcp");
        $this->line("_mcp._agents.{$host}. 3600 IN SVCB 1 {$host}. alpn=\"h2,h3\" port=443 mandatory=alpn,port");
        $this->line("_index._agents.{$host}. 3600 IN TXT \"mcp=_mcp._agents.{$host}\"");
        $this->newLine();
        $this->line('; ARD: where the agentic resource catalog lives');
        $this->line("_catalog._agents.{$host}. 3600 IN TXT \"url=https://{$host}/.well-known/ai-catalog.json\"");
        $this->newLine();
        $this->comment('Sign the zone with DNSSEC so validating resolvers return authenticated answers.');

        return self::SUCCESS;
    }
}
