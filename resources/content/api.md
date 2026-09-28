## Read the site

Every public page is also markdown. Ask for it with a header; browsers keep getting HTML.

```sh
curl -H 'Accept: text/markdown' {url}/
```

The response has `Content-Type: text/markdown` and an `X-Markdown-Tokens` estimate. [llms.txt]({url}/llms.txt) lists every page with a one-line summary, and the [sitemap]({url}/sitemap.xml) lists the URLs.

## MCP server

`{url}/mcp` speaks the Model Context Protocol over Streamable HTTP.

| Tool | Auth | What it does |
| --- | --- | --- |
| `list_pages` | none | Public pages with URL, title, and description |
| `read_page` | none | One page as markdown |
| `send_inquiry` | bearer, `inquiries:write` | Sends a message to the site owners, like the contact form |

Add `{url}/mcp` to any MCP client as a Streamable HTTP server.

Browsers with WebMCP get the same tools on every page through `navigator.modelContext`.

## Authentication

Reading is public. Writing uses OAuth 2.0 client credentials: register at `/oauth/register`, exchange the credentials at `/oauth/token`, send `Authorization: Bearer <token>`. The full flow is in [auth.md]({url}/auth.md).

## Discovery

| Document | URL |
| --- | --- |
| API catalog (RFC 9727) | [/.well-known/api-catalog]({url}/.well-known/api-catalog) |
| OpenAPI 3.1 | [/openapi.json]({url}/openapi.json) |
| MCP server card | [/.well-known/mcp/server-card.json]({url}/.well-known/mcp/server-card.json) |
| Agent skills | [/.well-known/agent-skills/index.json]({url}/.well-known/agent-skills/index.json) |
| Agentic resource catalog | [/.well-known/ai-catalog.json]({url}/.well-known/ai-catalog.json) |
| OAuth authorization server | [/.well-known/oauth-authorization-server]({url}/.well-known/oauth-authorization-server) |
| OAuth protected resource | [/.well-known/oauth-protected-resource]({url}/.well-known/oauth-protected-resource) |
| Agent registration | [/auth.md]({url}/auth.md) |
| Health | [/up]({url}/up) |

Every HTML response also carries a `Link` header pointing at the API catalog, the OpenAPI document, these docs, llms.txt, and the health check.

## Crawlers

[robots.txt]({url}/robots.txt) names the major AI crawlers explicitly and declares [Content Signals](https://contentsignals.org/): search and AI answers may use the content, model training may not.
