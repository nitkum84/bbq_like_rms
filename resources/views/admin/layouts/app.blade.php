<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ \App\Models\WebsiteSetting::get('restaurant_name','Restaurant') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="{{ asset('admin/css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content" id="mainContent">
        @include('admin.partials.topbar')

        <div class="content-wrapper">
            @include('admin.partials.alerts')
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Admin JS -->
    <script src="{{ asset('admin/js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
