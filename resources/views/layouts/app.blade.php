<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="max-h-screen overflow-hidden">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
