<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SAMA TERRITOIRE</title>

<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
<link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64x64.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon-512x512.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('assets/websig/css/style.css') }}">
@stack('page-css')
</head>
<body>

@include('layouts.navbarUser')

@yield('content')

@include('layouts.footerUser')



{{-- Données & libs spécifiques à la page (à charger avant app.js) --}}
@stack('page-scripts')
<script src="{{ asset('assets/websig/js/app.js') }}"></script>
<script src="{{ asset('assets/websig/js/responsive-tables.js') }}"></script>

@include('partials.chatbot')


</body>
</html>
