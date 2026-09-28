# {brand} auth.md

How AI agents authenticate with {brand}. Humans do not need an account.

## What needs a token

- **Public, no token:** every page (send `Accept: text/markdown` for markdown), `/llms.txt`, `/sitemap.xml`, all `/.well-known/*` documents, and the MCP tools `list_pages` and `read_page` at `{url}/mcp`.
- **Bearer token with scope `inquiries:write`:** the MCP tool `send_inquiry`, which sends a message to the site owners.

## Identity

Anonymous agent clients. There is no user login; an agent registers itself and receives client credentials. Tokens are rate limited and can be revoked per client.

## 1. Register a client (RFC 7591)

```http
POST {url}/oauth/register
Content-Type: application/json

{"client_name": "Your agent name"}
```

The `201` response holds `client_id` and `client_secret`. Store the secret; it is shown once.

## 2. Get an access token (client_credentials)

```http
POST {url}/oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=client_credentials&client_id=…&client_secret=…&scope=inquiries:write
```

HTTP Basic client authentication also works. The response holds `access_token`, `token_type: Bearer`, and `expires_in` (seconds). Request a new token when it expires.

## 3. Use it

Send `Authorization: Bearer <access_token>` on MCP requests to `{url}/mcp`.

## Revoke

```http
POST {url}/oauth/revoke

token=<access_token>&client_id=…&client_secret=…
```

## Discovery documents

- Protected resource (RFC 9728): {url}/.well-known/oauth-protected-resource
- Authorization server (RFC 8414): {url}/.well-known/oauth-authorization-server
- MCP server card: {url}/.well-known/mcp/server-card.json
- API docs: {url}/docs/api
