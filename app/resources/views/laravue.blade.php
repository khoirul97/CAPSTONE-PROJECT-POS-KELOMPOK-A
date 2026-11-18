<!doctype html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>A-POS</title>

    <link
        rel="apple-touch-icon"
        sizes="57x57"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="60x60"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="72x72"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="76x76"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="114x114"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="120x120"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="144x144"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="152x152"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="icon"
        type="image/png"
        sizes="192x192"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="icon"
        type="image/png"
        sizes="96x96"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="/favicon/favicon_new.png"
    >

    <link
        rel="manifest"
        href="/favicon/manifest.json"
    >

    <meta
        name="msapplication-TileColor"
        content="#ffffff"
    >

    <meta
        name="msapplication-TileImage"
        content="/favicon/favicon_new.png"
    >

    <meta
        name="theme-color"
        content="#ffffff"
    >

</head>

<body>

    <div id="app">
        <app></app>
    </div>


    {{-- =========================================================
         WEBSITE UTAMA RUNTIME CONFIG
         ========================================================= --}}

    @php

        $nodeUtama = 'NO';

        $envFile = base_path('.env');

        if (file_exists($envFile)) {

            $envContent = file_get_contents($envFile);

            if (
                preg_match(
                    '/^\s*MIX_NODE_UTAMA\s*=\s*(.*?)\s*$/m',
                    $envContent,
                    $matches
                )
            ) {

                $nodeUtama = trim($matches[1]);

                // Hapus quote jika ada
                $nodeUtama = trim($nodeUtama, "\"'");
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalisasi
        |--------------------------------------------------------------------------
        */

        $nodeUtama = strtoupper($nodeUtama);

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        if (!in_array($nodeUtama, ['YES', 'NO'], true)) {
            $nodeUtama = 'NO';
        }

    @endphp


    <script>

        window.APP_CONFIG = {
            NODE_UTAMA: @json($nodeUtama)
        };

        console.log(
            '[WEBSITE UTAMA] NODE_UTAMA:',
            window.APP_CONFIG.NODE_UTAMA
        );

    </script>


    <script src="/static/tinymce4.7.5/tinymce.min.js"></script>

    <script src="{{ mix('js/vendor.js') }}"></script>

    <script src="{{ mix('js/manifest.js') }}"></script>

    <script src="{{ mix('js/app.js') }}"></script>

</body>

</html>