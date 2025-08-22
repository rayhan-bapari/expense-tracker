<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex,nofollow" />
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/perfect-scrollbar/perfect-scrollbar.min.css') }}">
    @stack('header_script')

    @notyfyreStyles
</head>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        @include('partials.topbar')
        @include('partials.sidebar')
        <div class="page-wrapper">
            @yield('page-title')
            <div class="container-fluid">
                @yield('content')
            </div>
            @include('partials.footer')
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/libs/perfect-scrollbar/perfect-scrollbar.jquery.min.js') }}"></script>
    @stack('footer_script')

    @stack('custom_js')

    @notyfyreScripts

    @if (session('success') || session('error') || session('warning') || session('info'))
        <script>
            notyfyre.options({
                position: 'top-right',
                autoClose: 4000,
                progress: true
            });

            @if (session('success'))
                notyfyre.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                notyfyre.error("{{ session('error') }}");
            @endif

            @if (session('warning'))
                notyfyre.warning("{{ session('warning') }}");
            @endif

            @if (session('info'))
                notyfyre.info("{{ session('info') }}");
            @endif
        </script>
    @endif
</body>

</html>
