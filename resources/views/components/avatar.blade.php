@props([
    'name' => '?',
    'imageUrl' => null,
    'hasImage' => false,
    'seed' => '',
    'class' => 'size-12 rounded-full',
    'textClass' => 'text-sm font-bold text-white',
])

@php
    $initials = \App\Support\AvatarHelper::initials($name);
    $bgColor = \App\Support\AvatarHelper::colorFor($seed ?: $name);
@endphp

@if($hasImage && $imageUrl)
    <img src="{{ $imageUrl }}" alt="{{ $name }}" {{ $attributes->merge(['class' => $class.' object-cover']) }}/>
@else
    <div {{ $attributes->merge(['class' => $class.' flex items-center justify-center shrink-0']) }} style="background-color: {{ $bgColor }}">
        <span class="{{ $textClass }}">{{ $initials }}</span>
    </div>
@endif
