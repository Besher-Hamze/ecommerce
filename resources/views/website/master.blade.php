<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', ($storeSettings['meta_title'] ?? 'Noor-Store'))</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="{{ $storeSettings['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $storeSettings['meta_keyword'] ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="cart-count-url" content="{{ route('cart.count') }}">
    <meta name="cart-mini-url" content="{{ route('cart.mini') }}">
    <meta name="login-url" content="{{ route('login') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('website/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('website/css/noor.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css') }}">
    <style>
        body, .btn, .form-control, .nav-link { font-family: 'Outfit', sans-serif; }
        .navbar-brand h1, .display-3, .display-6, .noor-section-title {
            font-family: 'Cormorant Garamond', Georgia, serif;
        }
        .pagination { display: flex; justify-content: center; flex-wrap: wrap; list-style: none; padding: 0; margin: 20px 0; }
        .pagination li { margin: 0 5px; }
        .pagination li a, .pagination li span {
            display: block; padding: 8px 12px; text-decoration: none; color: #333;
            border: 1px solid #d18aa3; border-radius: 4px;
        }
        .pagination li.active span { background-color: #d18aa3; color: white; border-color: #d18aa3; }
    </style>
    @stack('styles')
</head>

<body>
    @include('website.header')
    @yield('content')
    @include('website.footer')

    <div class="offcanvas offcanvas-end" tabindex="-1" id="miniCartOffcanvas" aria-labelledby="miniCartLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="miniCartLabel">Your bag</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="miniCartBody">
            <p class="text-muted">Loading…</p>
        </div>
    </div>

    @php $firstError = $errors->first(); @endphp

    <script>
        // Hide the template splash spinner even if a later script fails to load.
        window.addEventListener('load', function () {
            var s = document.getElementById('spinner');
            if (s) s.classList.remove('show');
        });
        setTimeout(function () {
            var s = document.getElementById('spinner');
            if (s) s.classList.remove('show');
        }, 800);
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('website/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('website/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('website/lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('website/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('website/js/main.js') }}"></script>
    <script src="{{ asset('my_js/cart.js') }}"></script>
    <script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>
    <script>
        $(function() {
            @if(session('success')) toastr.success(@json(session('success'))); @endif
            @if(session('error')) toastr.error(@json(session('error'))); @endif
            @if(session('warning')) toastr.warning(@json(session('warning'))); @endif
            @if(session('info')) toastr.info(@json(session('info'))); @endif
            const errorMessage = @json($firstError);
            if (errorMessage) toastr.error(errorMessage);
        });
    </script>
    @stack('scripts')
</body>
</html>
