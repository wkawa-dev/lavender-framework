@props(['rows' => []])

{{-- Hairline list: meta left, title right. Rows nudge 2px on fine-pointer hover only. --}}
<ul {{ $attributes->merge(['class' => 'divide-y divide-line border-t border-line']) }}>
    @foreach ($rows as $row)
        <li>
            <a href="{{ $row['href'] }}" class="list-row group flex flex-col gap-2 py-6 sm:flex-row sm:items-baseline sm:justify-between">
                <span class="text-sm text-muted">{{ $row['meta'] }}</span>
                <span class="font-medium group-hover:underline group-hover:decoration-line group-hover:underline-offset-4 sm:max-w-xl sm:text-right">{{ $row['title'] }}</span>
            </a>
        </li>
    @endforeach
</ul>
