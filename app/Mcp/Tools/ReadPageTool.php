<?php

namespace App\Mcp\Tools;

use App\Support\Agents;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('read_page')]
#[Title('Read page')]
#[Description('Read one page of this website as markdown. Pass a URL or path from list_pages, e.g. "/contact".')]
#[IsReadOnly]
#[IsIdempotent]
class ReadPageTool extends Tool
{
    public function handle(Request $request): Response
    {
        $page = Agents::page((string) $request->get('path', '/'));

        if (! $page) {
            return Response::error('Unknown page. Call list_pages for the pages you can read.');
        }

        return Response::text(Agents::pageMarkdown($page['route']));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'path' => $schema->string()->description('Page path or URL, e.g. "/" or "/contact".')->required(),
        ];
    }
}
