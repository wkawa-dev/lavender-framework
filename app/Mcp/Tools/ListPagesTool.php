<?php

namespace App\Mcp\Tools;

use App\Support\Agents;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('list_pages')]
#[Title('List pages')]
#[Description('List the public pages of this website with their URL, title, and description.')]
#[IsReadOnly]
#[IsIdempotent]
class ListPagesTool extends Tool
{
    public function handle(Request $request): Response
    {
        return Response::json(array_map(
            fn (array $page) => ['url' => $page['url'], 'title' => $page['title'], 'description' => $page['description']],
            Agents::pages(),
        ));
    }
}
