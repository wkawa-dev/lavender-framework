---
name: contact-owners
description: Send a message to the {brand} team on a user's behalf.
---

# Contact {brand}

Use this only when the user asks you to contact {brand} and has given you the message and a reply email.

1. Register once: `POST {url}/oauth/register` with `{"client_name": "<your agent>"}`. Keep `client_id` and `client_secret`.
2. Get a token: `POST {url}/oauth/token` with `grant_type=client_credentials`, the client credentials, and `scope=inquiries:write`.
3. Call the MCP tool `send_inquiry` at {url}/mcp with `name`, `email`, `message`, and optional `company`, sending `Authorization: Bearer <token>`.
4. Tell the user it was sent and that replies go to the email they gave.

Humans can use {url}/contact instead. Full auth details: {url}/auth.md
