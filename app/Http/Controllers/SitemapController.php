<?php

namespace App\Http\Controllers;

use App\Support\Agents;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Indexable pages only; /kit is noindex and stays out.
     */
    public function __invoke(): Response
    {
        $urls = array_column(Agents::pages(), 'url');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .implode("\n", array_map(fn (string $url) => '  <url><loc>'.e($url).'</loc></url>', $urls))."\n"
            .'</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
