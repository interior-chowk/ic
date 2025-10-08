<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="_token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('asset/img/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('asset/img/favicon.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('asset/img/favicon.png') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{  asset('asset/css/font-awesome.min.css') }}">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/slick.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/seller-custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/responsive.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/toastr.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/style.css') }}">

  <!-- links from 2.html -->

<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'INTERIOR CHOWK') }}</title>
    
    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="Molla - Bootstrap eCommerce Template">
    <meta name="author" content="p-themes">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('website/assets/images/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('website/assets/images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('website/assets/images/icons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('website/assets/images/icons/site.html') }}">
    <link rel="mask-icon" href="{{ asset('website/assets/images/icons/safari-pinned-tab.svg') }}" color="#666666">
    <link rel="shortcut icon" href="{{ asset('website/assets/images/icons/favicon.ico') }}">

    <meta name="msapplication-TileColor" content="#cc9966">
    <meta name="msapplication-config" content="{{ asset('assets/images/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('website/assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/billing.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/plugins/owl-carousel/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/plugins/magnific-popup/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/plugins/jquery.countdown.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/skins/skin-demo-3.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/demos/demo-3.css') }}">
  

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <title>InteriorChowk</title>
  
  <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1176419510435563');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1176419510435563&ev=PageView&noscript=1"
/></noscript>

<!-- End Meta Pixel Code -->

 <!-- Plugins JS File -->
  <!-- jQuery and JS Dependencies -->
<script src="{{ asset('website/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('website/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('website/assets/js/jquery.hoverIntent.min.js') }}"></script>
<script src="{{ asset('website/assets/js/jquery.waypoints.min.js') }}"></script>
<!-- <script src="{{ asset('website/assets/js/superfish.min.js') }}"></script> -->
<script src="{{ asset('website/assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('website/assets/js/bootstrap-input-spinner.js') }}"></script>
<script src="{{ asset('website/assets/js/jquery.plugin.min.js') }}"></script>
<script src="{{ asset('website/assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('website/assets/js/jquery.countdown.min.js') }}"></script>

<!-- Main JS File -->
<!-- <script src="{{ asset('website/assets/js/main.js') }}"></script>
<script src="{{ asset('website/assets/js/demos/demo-9.js') }}"></script> -->
<!-- new -->
<!-- <script src="{{ asset('website/new/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.hoverIntent.min.js') }}"></script> -->
<!-- <script src="{{ asset('website/new/assets/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/superfish.min.js') }}"></script> -->
<script src="{{ asset('website/new/assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/bootstrap-input-spinner.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.elevateZoom.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.magnific-popup.min.js') }}"></script>
<!-- Main JS File -->
<script src="{{ asset('website/new/assets/js/main.js') }}"></script>



</head>
