@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-left mb-2">
    <h1 class="text-2xl md:text-3xl font-medium tracking-tight text-[#111111] mb-2">{{ $title }}</h1>
    <p class="text-sm text-[#787774]">{{ $description }}</p>
</div>
