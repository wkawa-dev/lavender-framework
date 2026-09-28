<?php

namespace App\Mcp\Tools;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\Inquiry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Name('send_inquiry')]
#[Title('Send inquiry')]
#[Description('Send a message to the site owners, the same as the contact form. Requires a bearer token with the inquiries:write scope (see /auth.md). Only send what the user asked you to send.')]
#[IsDestructive(false)]
#[IsIdempotent(false)]
class SendInquiryTool extends Tool
{
    public function handle(Request $request): Response
    {
        $agent = $request->user('sanctum');

        if (! $agent || ! $agent->tokenCan('inquiries:write')) {
            return Response::error('Authentication required. Register at '.url('/oauth/register').', get a token at '.url('/oauth/token').' (client_credentials, scope inquiries:write), and send it as "Authorization: Bearer <token>". Details: '.url('/auth.md'));
        }

        $request->merge(['email' => mb_strtolower(trim((string) $request->get('email')))]);

        Inquiry::create($request->validate((new StoreInquiryRequest)->rules()));

        return Response::text(site('contact.form.success'));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(120)->description('Sender name.')->required(),
            'email' => $schema->string()->format('email')->max(190)->description('Where the owners should reply.')->required(),
            'company' => $schema->string()->max(120)->description('Optional company.'),
            'message' => $schema->string()->max(5000)->description('The message.')->required(),
        ];
    }
}
