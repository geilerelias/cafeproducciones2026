<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $meta = $meta ?? [];
            $metaTitle = $meta['title'] ?? config('app.name', 'CAFE Producciones');
            $metaDescription = $meta['description'] ?? 'Produccion, logistica, tecnologia y acompanamiento para eventos sociales, corporativos e institucionales en Riohacha, La Guajira y Colombia.';
            $metaImage = $meta['image'] ?? asset('images/seo-logo.png');
            $metaUrl = $meta['url'] ?? url()->current();
            $metaType = $meta['type'] ?? 'website';
        @endphp

        <title inertia>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        <link rel="canonical" href="{{ $metaUrl }}">
        <meta property="og:site_name" content="CAFE Producciones">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:type" content="{{ $metaType }}">
        <meta property="og:url" content="{{ $metaUrl }}">
        <meta property="og:image" content="{{ $metaImage }}">
        <meta property="og:image:alt" content="CAFE Producciones">
        <meta property="og:locale" content="es_CO">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $metaImage }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
