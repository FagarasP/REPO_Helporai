<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; color: #111827;">
    <main style="max-width: 1100px; margin: 0 auto; padding: 24px;">
        <header style="margin-bottom: 20px;">
            <h1 style="margin: 0 0 6px;">Admin Panel</h1>
            <p style="margin: 0; color: #4b5563;">Systemübersicht und Schnellzugriffe.</p>
        </header>

        <section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">
            @foreach($stats as $label => $value)
                <article style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px;">
                    <p style="margin: 0; text-transform: uppercase; font-size: 12px; color: #6b7280;">{{ ucfirst($label) }}</p>
                    <p style="margin: 8px 0 0; font-size: 28px; font-weight: 700;">{{ $value }}</p>
                </article>
            @endforeach
        </section>

        <section style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;">
            <div style="padding: 14px; border-bottom: 1px solid #f3f4f6;">
                <h2 style="margin: 0;">Quick Links</h2>
            </div>
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($quickLinks as $link)
                    <li style="border-top: 1px solid #f3f4f6;">
                        <a href="{{ $link['url'] }}" style="display: block; padding: 14px; text-decoration: none; color: inherit;">
                            <p style="margin: 0 0 4px; font-weight: 600;">{{ $link['label'] }}</p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px;">{{ $link['description'] }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
</body>
</html>
