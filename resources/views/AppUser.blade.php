<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SAMA TERRITOIRE</title>

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


</body>
</html>
