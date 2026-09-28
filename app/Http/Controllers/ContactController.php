<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('theme::pages.contact', [
            'title' => site('contact.seo.title').' · '.site('brand'),
            'description' => site('contact.seo.description'),
        ]);
    }

    /**
     * JSON callers (the WebMCP send_inquiry tool) get 201 instead of a redirect.
     */
    public function store(StoreInquiryRequest $request): RedirectResponse|JsonResponse
    {
        // Honeypot filled: nothing is stored, but the bot still sees success.
        if (blank($request->input('website'))) {
            Inquiry::create($request->validated());
        }

        return $request->expectsJson()
            ? response()->json(['message' => site('contact.form.success')], 201)
            : to_route('contact')->with('status', site('contact.form.success'));
    }
}
