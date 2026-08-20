<!DOCTYPE html>
<html lang="en" dir="rtl">

<!-- Mirrored from thevectorlab.net/flatlab-rtl/ by HTTrack Website Copier/3.x [XR&CO'2010], Thu, 13 Apr 2017 09:53:31 GMT -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="img/favicon.html">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FlatLab - Flat & Responsive Bootstrap Admin Template</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="{{ asset('dashboard/css/bs-rtl/bootstrap-rtl.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/bs-rtl/bootstrap-flipped.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/bootstrap-reset.css') }}" rel="stylesheet">
    <!--external css-->
    <link href="{{ asset('dashboard/assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css') }}" rel="stylesheet"
        type="text/css" media="screen" />
    <link rel="stylesheet" href="{{ asset('dashboard/css/owl.carousel.css') }}" type="text/css">

    <!--right slidebar-->
    <link href="{{ asset('dashboard/css/slidebars.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->

    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/style-responsive.css') }}" rel="stylesheet" />


    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard/assets/bootstrap-fileupload/bootstrap-fileupload.css') }}" />
    <!--Form Wizard-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/css/jquery.steps.css') }}" />
    <!--right slidebar-->
    <!--right slidebar-->
    <link href="{{ asset('dashboard/css/slidebars.css') }}" rel="stylesheet">
    <!-- PNotify -->
    <link href="{{ asset('pnotify/dist/pnotify.css') }}" rel="stylesheet">
    <link href="{{ asset('pnotify/dist/pnotify.buttons.css') }}" rel="stylesheet">
    <link href="{{ asset('pnotify/dist/pnotify.nonblock.css') }}" rel="stylesheet">
    <!--datepicker-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard/assets/bootstrap-daterangepicker/daterangepicker-bs3.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard/assets/bootstrap-datepicker/css/datepicker.css') }}" />
    <!--dropzone-->
        <link href="{{asset('dashboard/assets/dropzone/css/dropzone.css')}}" rel="stylesheet" />
    <!--select 2-->
    <link rel="stylesheet" type="text/css" href="{{asset('dashboard/assets/select2/css/select2.min.css')}}"/>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
        <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
        <style>
            .card-header h1 {
                font-family: Tahoma, Geneva, sans-serif;
                font-weight: 600;
                font-size: 24px;
                color: #4c4d5a;
                display: inline-block;
                margin-bottom: 15px;
                text-shadow: 0 1px #fff;
            }

            .card-header li a {
                color: #999999;
                font-size: 20px;
                padding: 0px;
                margin: 0px;
            }

            #nav-accordion>li>a {
                font-size: 18px;
                color: #b3cbdd;
                padding-right: 13px;
                border-bottom: 0px solid #585858;
            }

            #nav-accordion li a {
                text-decoration: none;
                display: block;
                padding: 20px;
                cursor: pointer;
                border-bottom: 0px solid #515151;
            }

            .control-label {
                font-size: 14;
                font-weight: bold;
            }
        </style>
        @yield('style')
</head>

<body>
    @include('dashboard.header')
    <div>
        @yield('content')
    </div>
    @include('dashboard.footer')
    <!-- js placed at the end of the document so the pages load faster -->
    <script src="{{ asset('dashboard/js/jquery.js') }}"></script>
    <script src="{{ asset('dashboard/js/bootstrap.min.js') }}"></script>
    <script class="include" type="text/javascript" src="{{ asset('dashboard/js/jquery.dcjqaccordion.2.7.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.nicescroll.js') }}" type="text/javascript"></script>
    <script src="{{ asset('dashboard/js/jquery.sparkline.js') }}" type="text/javascript"></script>
    <script src="{{ asset('dashboard/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js') }}"></script>
    <script src="{{ asset('dashboard/js/owl.carousel.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.customSelect.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/respond.min.js') }}"></script>
    <!--right slidebar-->
    <script src="{{ asset('dashboard/js/slidebars.min.js') }}"></script>
    <!--common script for all pages-->
    <script src="{{ asset('dashboard/js/common-scripts.js') }}"></script>
    <!--script for this page-->
    <script src="{{ asset('dashboard/js/sparkline-chart.js') }}"></script>
    <script src="{{ asset('dashboard/js/easy-pie-chart.js') }}"></script>
    <script src="{{ asset('dashboard/js/count.js') }}"></script>
    <!--for datatables-->
    <script type="text/javascript" src="{{ asset('dashboard/assets/data-tables/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('dashboard/assets/data-tables/DT_bootstrap.js') }}"></script>
    <!---->
    <script src="{{ asset('dashboard/js/jquery.stepy.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery-ui-1.9.2.custom.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery-migrate-1.2.1.min.js') }}"></script>
    <!--script for this page only-->
    <script src="{{ asset('dashboard/js/editable-table.js') }}"></script>
    <!-- END JAVASCRIPTS -->
    <!--summernote-->
    <script src="{{ asset('dashboard/assets/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/advanced-form-components.js') }}"></script>
    <script type="text/javascript" src="{{ asset('dashboard/assets/bootstrap-fileupload/bootstrap-fileupload.js') }}">
    </script>
    <!---for steps--->
    <!-- PNotify -->
    <script src="{{ asset('pnotify/dist/pnotify.js') }}"></script>
    <script src="{{ asset('pnotify/dist/pnotify.buttons.js') }}"></script>
    <script src="{{ asset('pnotify/dist/pnotify.nonblock.js') }}"></script>
    <!---date-->
    <script type="text/javascript" src="{{ asset('dashboard/assets/bootstrap-datepicker/js/bootstrap-datepicker.js') }}">
    </script>
    <script type="text/javascript"
        src="{{ asset('dashboard/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <!--select2-->
    <script type="text/javascript" src="{{asset('dashboard/assets/select2/js/select2.min.js')}}"></script>

    <script>
        jQuery(document).ready(function() {

            $('.summernote').summernote({
                height: 200, // set editor height

                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor

                focus: true // set focus to editable area after initializing summernote
            });
        });
    </script>
    <script>
        jQuery(document).ready(function() {
            EditableTable.init();
        });
    </script>

    <script>
        //owl carousel
        $(document).ready(function() {
            $("#owl-demo").owlCarousel({
                navigation: true,
                slideSpeed: 300,
                paginationSpeed: 400,
                singleItem: true,
                autoPlay: true,
                rtl: true

            });
        });

        //custom select box

        $(function() {
            $('select.styled').customSelect();
        });

        $(window).on("resize", function() {
            var owl = $("#owl-demo").data("owlCarousel");
            owl.reinit();
        });
        //
    </script>
    <script>
        $(function() {
            $('#default').stepy({
                backLabel: 'Previous',
                block: true,
                nextLabel: 'Next',
                titleClick: true,
                titleTarget: '.stepy-tab'
            });
        });
    </script>
    @yield('script')
</body>

</html>
