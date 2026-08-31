<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SAMA TERRITOIRE</title>

<link rel="stylesheet" href="{{ asset('assets/vendor/leaflet/leaflet.css') }}">
<script src="{{ asset('assets/vendor/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('assets/vendor/chartjs/chart.umd.min.js') }}"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css')}}" integrity="..." crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('assets/websig/css/style.css') }}">
</head>
<body>

@include('layouts.navbarAdmi')

@yield('content')

@include('layouts.footerAdmi')



{{-- Données (à charger avant app.js) --}}
<script src="{{ asset('assets/websig/js/data-infrastructures.js') }}"></script>
<script src="{{ asset('assets/websig/js/data-climate.js') }}"></script>
<script src="{{ asset('assets/websig/js/app.js') }}"></script>


</body>
</html>
