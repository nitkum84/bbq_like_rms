<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($restaurantName ?? config('app.name', 'Restaurant Booking')))</title>
    <meta name="description" content="@yield('meta_description', 'Restaurant booking and dining experience homepage.')">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/front.css', 'resources/js/front.js'])
    @stack('head')
</head>
<body>
    <div class="site-shell">
        @include('front.partials.sidebar', ['sidebarSections' => $sidebarSections])
        @include('front.partials.header', ['primaryNavigation' => $primaryNavigation, 'profileUrl' => $profileUrl])
        <main>
            @yield('content')
        </main>
        @include('front.partials.footer', ['sidebarSections' => $sidebarSections])
    </div>
</body>
</html>
