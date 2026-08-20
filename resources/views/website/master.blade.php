<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Fruitables - Vegetable Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="cart-count-url" content="{{ route('cart.count') }}">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset('website/lib/lightbox/css/lightbox.min.css')}}" rel="stylesheet">
    <link href="{{asset('website/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{asset('website/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{asset('website/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css') }}">

    <style>
        .pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            /* يسمح بالنزول لسطر ثاني إذا ضاق المكان */
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a,
        .pagination li span {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
            border: 1px solid #d18aa3;
            border-radius: 4px;
        }

        .pagination li.active span {
            background-color: #d18aa3;
            color: white;
            border-color: #d18aa3;
        }
    </style>
</head>

<body>
    @include('website.header')

    @yield('content')

    @include('website.footer')
    @php
    $firstError = $errors->first();
    @endphp
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('website/lib/easing/easing.min.js')}}"></script>
    <script src="{{asset('website/lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset('website/lib/lightbox/js/lightbox.min.js')}}"></script>
    <script src="{{asset('website/lib/owlcarousel/owl.carousel.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{asset('website/js/main.js')}}"></script>
    <script src="{{asset('my_js/cart.js')}}"></script>
    <!-- PNotify -->
    <script src="{{ asset('pnotify/dist/pnotify.js') }}"></script>
    <script src="{{ asset('pnotify/dist/pnotify.buttons.js') }}"></script>
    <script src="{{ asset('pnotify/dist/pnotify.nonblock.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Toastr JS (بعد jQuery) -->
    <script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>

    <!-- تشغيل toastr من رسائل Laravel session -->
    <script>
        $(function() {
            @if(session('success'))
            toastr.success(@json(session('success')));
            @endif


            @if(session('error'))
            toastr.error(@json(session('error')));
            @endif

            @if(session('warning'))
            toastr.warning(@json(session('warning')));
            @endif

            @if(session('info'))
            toastr.info(@json(session('info')));
            @endif
        });
        $(function() {

            const errorMessage = @json($firstError);

            if (errorMessage) {
                toastr.error(errorMessage);
            }

        });
    </script>
</body>

</html>