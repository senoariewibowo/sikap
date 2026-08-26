@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-indigo-50 text-indigo-700'
            : 'flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
