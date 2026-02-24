<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <main class="max-w-6xl mx-auto px-4 py-8 space-y-8">
        <header>
            <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
            <p class="text-sm text-gray-600">Systemübersicht und Schnellzugriffe.</p>
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($stats as $label => $value)
                <article class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ ucfirst($label) }}</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-2">{{ $value }}</p>
                </article>
            @endforeach
        </section>

        <section class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Quick Links</h2>
            </div>
            <ul class="divide-y divide-gray-100">
                @foreach($quickLinks as $link)
                    <li>
                        <a href="{{ route($link['route']) }}" class="block p-4 hover:bg-gray-50 transition">
                            <p class="font-medium text-gray-900">{{ $link['label'] }}</p>
                            <p class="text-sm text-gray-600">{{ $link['description'] }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
</body>
</html>
