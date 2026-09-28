<div
    x-cloak
    x-show="!$store.consent.decided"
    x-transition:enter="motion-sheet"
    x-transition:enter-start="motion-sheet-from"
    x-transition:enter-end="motion-sheet-to"
    x-transition:leave="motion-sheet"
    x-transition:leave-start="motion-sheet-to"
    x-transition:leave-end="motion-sheet-from"
    role="dialog"
    aria-modal="false"
    aria-labelledby="cookie-banner-title"
    aria-describedby="cookie-banner-body"
    class="cookie-banner fixed inset-x-0 bottom-0 z-50 border-t border-line bg-paper"
>
    <div class="wrap flex flex-col gap-6 py-5 md:flex-row md:items-end md:justify-between md:gap-10">
        <div class="max-w-xl">
            <p id="cookie-banner-title" class="text-sm font-medium text-ink">{{ site('consent.title') }}</p>
            <p id="cookie-banner-body" class="mt-2 text-sm leading-relaxed text-muted">{{ site('consent.body') }}</p>
            <a href="{{ route('privacy') }}" class="link mt-3 inline-block text-sm">{{ site('consent.policy') }}</a>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button type="button" class="btn btn-secondary" @click="$store.consent.essentialOnly()">{{ site('consent.essential') }}</button>
            <button type="button" class="btn btn-primary" @click="$store.consent.acceptAll()">{{ site('consent.accept') }}</button>
        </div>
    </div>
</div>
