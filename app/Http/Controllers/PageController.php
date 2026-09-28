<?php

namespace App\Http\Controllers;

use App\Support\Agents;
use Illuminate\Support\Str;
use Illuminate\View\View;
use League\CommonMark\Extension\Table\TableExtension;

class PageController extends Controller
{
    public function home(): View
    {
        return view('theme::pages.home', [
            'title' => site('home.seo.title'),
            'description' => site('home.seo.description'),
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebSite',
                        'name' => site('brand'),
                        'url' => url('/'),
                    ],
                    [
                        '@type' => 'FAQPage',
                        'mainEntity' => array_map(fn (array $item) => [
                            '@type' => 'Question',
                            'name' => $item['q'],
                            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
                        ], site('home.faq.items')),
                    ],
                ],
            ],
        ]);
    }

    public function kit(): View
    {
        return view('theme::pages.kit', [
            'title' => site('kit.seo.title').' · '.site('brand'),
            'description' => site('kit.seo.description'),
            'robots' => 'noindex',
        ]);
    }

    public function privacy(): View
    {
        return view('theme::pages.privacy', [
            'title' => site('privacy.seo.title').' · '.site('brand'),
            'description' => site('privacy.seo.description'),
            'body' => Str::markdown((string) file_get_contents(resource_path('content/privacy.md')), [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]),
        ]);
    }

    public function docs(): View
    {
        return view('theme::pages.docs', [
            'title' => site('docs.api.seo.title').' · '.site('brand'),
            'description' => site('docs.api.seo.description'),
            'body' => Str::markdown(Agents::document('api.md'), [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ], [new TableExtension]),
        ]);
    }
}
