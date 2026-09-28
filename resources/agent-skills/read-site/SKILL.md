---
name: read-site
description: Read {brand} pages as markdown to answer questions about the site.
---

# Read {brand}

Use this when someone asks what {brand} is, what it offers, or anything the site answers.

1. Get the page list: {url}/llms.txt (or the MCP tool `list_pages` at {url}/mcp).
2. Read a page as markdown: `GET <page URL>` with `Accept: text/markdown`, or the MCP tool `read_page` with the path.
3. Quote the page and link its URL. Do not invent facts the pages do not state.

No authentication is needed to read.
