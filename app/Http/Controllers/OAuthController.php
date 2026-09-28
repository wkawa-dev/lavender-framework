<?php

namespace App\Http\Controllers;

use App\Models\AgentClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Machine-to-machine OAuth 2.0 for agents: dynamic client registration
 * (RFC 7591), the client_credentials grant (RFC 6749 §4.4), and token
 * revocation (RFC 7009). Tokens are opaque Sanctum tokens, so write tools
 * can be rate limited and revoked per agent. There is no user login, so the
 * authorization endpoint only answers with a spec-shaped error.
 */
class OAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_name' => ['required', 'string', 'max:120'],
        ]);

        if ($validator->fails()) {
            return $this->error('invalid_client_metadata', $validator->errors()->first(), 400);
        }

        $secret = Str::random(48);
        $client = AgentClient::create([
            'name' => $validator->validated()['client_name'],
            'client_id' => (string) Str::uuid(),
            'secret' => $secret,
        ]);

        return response()->json([
            'client_id' => $client->client_id,
            'client_secret' => $secret,
            'client_id_issued_at' => $client->created_at->getTimestamp(),
            'client_secret_expires_at' => 0,
            'client_name' => $client->name,
            'grant_types' => ['client_credentials'],
            'token_endpoint_auth_method' => 'client_secret_post',
            'scope' => implode(' ', array_keys(config('agents.scopes'))),
        ], 201, ['Cache-Control' => 'no-store']);
    }

    public function token(Request $request): JsonResponse
    {
        if ($request->input('grant_type') !== 'client_credentials') {
            return $this->error('unsupported_grant_type', 'Only client_credentials is supported. See /auth.md.', 400);
        }

        $client = $this->client($request);

        if (! $client) {
            return $this->error('invalid_client', 'Unknown client or wrong secret.', 401);
        }

        $supported = array_keys(config('agents.scopes'));
        $requested = array_filter(explode(' ', (string) $request->input('scope', implode(' ', $supported))));

        if (array_diff($requested, $supported) !== []) {
            return $this->error('invalid_scope', 'Supported scopes: '.implode(' ', $supported).'.', 400);
        }

        $client->tokens()->where('expires_at', '<', now())->delete();
        $ttl = (int) config('agents.token_ttl');
        $token = $client->createToken('client_credentials', $requested, now()->addSeconds($ttl));

        return response()->json([
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $ttl,
            'scope' => implode(' ', $requested),
        ], 200, ['Cache-Control' => 'no-store']);
    }

    public function revoke(Request $request): JsonResponse
    {
        $client = $this->client($request);

        if (! $client) {
            return $this->error('invalid_client', 'Unknown client or wrong secret.', 401);
        }

        $token = PersonalAccessToken::findToken((string) $request->input('token'));

        if ($token && $token->tokenable?->is($client)) {
            $token->delete();
        }

        // RFC 7009 §2.2: unknown tokens are not an error.
        return response()->json([], 200, ['Cache-Control' => 'no-store']);
    }

    public function authorize(): JsonResponse
    {
        return $this->error('unsupported_response_type', 'This server has no user login. Agents use client_credentials at /oauth/token. See /auth.md.', 400);
    }

    /**
     * client_secret_basic or client_secret_post.
     */
    private function client(Request $request): ?AgentClient
    {
        $id = $request->getUser() ?? $request->input('client_id');
        $secret = $request->getPassword() ?? $request->input('client_secret');

        if (! is_string($id) || ! is_string($secret) || ! Str::isUuid($id)) {
            return null;
        }

        $client = AgentClient::where('client_id', $id)->first();

        return $client && Hash::check($secret, $client->secret) ? $client : null;
    }

    private function error(string $code, string $description, int $status): JsonResponse
    {
        return response()->json(['error' => $code, 'error_description' => $description], $status, ['Cache-Control' => 'no-store']);
    }
}
