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

{{-- Styles partagés des modales / formulaires / popups (pages Admin) --}}
<style>
    .modal-overlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.45); backdrop-filter:blur(3px); justify-content:center; align-items:center; }
    .modal-overlay.active { display:flex; }
    .modal-card { background:var(--surface); border-radius:16px; padding:0; width:95%; max-width:680px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.25); position:relative; }
    .modal-card-header { display:flex; justify-content:space-between; align-items:center; padding:20px 28px 16px; border-bottom:1px solid var(--border); }
    .modal-card-header h3 { margin:0; font-family:'Syne',sans-serif; font-size:18px; font-weight:700; color:var(--text); }
    .modal-card-body { padding:20px 28px 24px; }
    .modal-close { background:none; border:none; font-size:20px; color:var(--text-muted); cursor:pointer; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; transition:.15s; }
    .modal-close:hover { background:var(--surface2); color:var(--text); }
    .field-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .field-group { margin-bottom:14px; }
    .field-group label { display:block; font-size:12px; font-weight:600; color:var(--text-dim); margin-bottom:5px; }
    .field-group input,
    .field-group select,
    .field-group textarea {
        width:100%; padding:9px 12px; font-size:13px; font-family:'DM Sans',sans-serif;
        border:1px solid var(--border); border-radius:10px; background:var(--surface2);
        color:var(--text); outline:none; transition:border .2s, box-shadow .2s; box-sizing:border-box;
    }
    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(45,155,95,.12); }
    .field-group textarea { resize:vertical; min-height:60px; }
    .btn-submit { display:inline-flex; align-items:center; gap:6px; padding:10px 22px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:.15s; }
    .btn-submit:hover { filter:brightness(1.08); }
    .btn-cancel { display:inline-flex; align-items:center; gap:6px; padding:10px 22px; background:var(--surface2); color:var(--text-dim); border:1px solid var(--border); border-radius:10px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:.15s; }
    .btn-cancel:hover { border-color:var(--text-muted); color:var(--text); }

    /* Popup de succès */
    .success-popup { display:none; position:fixed; inset:0; z-index:10000; background:rgba(0,0,0,.4); backdrop-filter:blur(3px); justify-content:center; align-items:center; }
    .success-popup.active { display:flex; animation:fadeIn .25s ease; }
    .success-card { background:var(--surface); border-radius:16px; padding:32px 40px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    .success-icon { width:56px; height:56px; border-radius:50%; background:#e6f9ee; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
    .success-icon i { font-size:24px; color:#2d9b5f; }
    .success-card h4 { margin:0 0 6px; font-family:'Syne',sans-serif; font-size:18px; color:var(--text); }
    .success-card p { margin:0 0 20px; font-size:13px; color:var(--text-dim); }
    @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

    /* ── Sidebar réduisible (pages Admin) ── */
    .app-sidebar { width:240px; min-width:240px; transition:width .25s ease, min-width .25s ease; }
    .app-sidebar.collapsed { width:64px; min-width:64px; }
    .app-sidebar.collapsed .sidebar-label { display:none; }
    .app-sidebar.collapsed .sidebar-nav-label { display:none; }
    .app-sidebar.collapsed .sidebar-filters { display:none; }
    .app-sidebar.collapsed .sidebar-user { display:none; }
    .app-sidebar.collapsed .sidebar-title span { display:none; }
    .app-sidebar.collapsed .sidebar-title { justify-content:center; padding:0 0 12px; }
    .app-sidebar.collapsed .sidebar-item { justify-content:center; padding:12px 0; }
    .app-sidebar.collapsed .sidebar-item .sidebar-perm { display:none; }
    .app-sidebar.collapsed .sidebar-item.active-sidebar { justify-content:center; }
    .app-sidebar.collapsed .sidebar-item .sidebar-onglet-badge { display:none; }
    .app-sidebar.collapsed .sidebar-badge-attente { display:none; }
    .app-sidebar .sidebar-toggle { cursor:pointer; border:none; background:var(--surface2); color:var(--text-dim); width:28px; height:28px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:12px; transition:.15s; flex-shrink:0; }
    .app-sidebar .sidebar-toggle:hover { background:var(--border); color:var(--text); }
    .app-sidebar.collapsed .sidebar-toggle { margin:0 auto; }
</style>
</head>
<body>

@include('layouts.navbarAdmi')

@yield('content')

@include('layouts.footerAdmi')



{{-- Données (à charger avant app.js) --}}
<script src="{{ asset('assets/websig/js/data-infrastructures.js') }}"></script>
<script src="{{ asset('assets/websig/js/data-climate.js') }}"></script>
<script src="{{ asset('assets/websig/js/app.js') }}"></script>

{{-- Toggle sidebar réduisible (pages Admin) --}}
<script>
(function () {
    var KEY = 'sama_sidebar_collapsed';
    var aside = document.querySelector('.app-sidebar');
    if (!aside) return;

    // Applique l'état mémorisé au chargement
    if (localStorage.getItem(KEY) === '1') {
        aside.classList.add('collapsed');
    }

    // Bouton de bascule (créé dynamiquement dans l'en-tête de la sidebar)
    var toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'sidebar-toggle';
    toggle.title = 'Réduire / étendre';
    toggle.setAttribute('aria-label', 'Réduire / étendre le menu');
    toggle.innerHTML = aside.classList.contains('collapsed')
        ? '<i class="fa-solid fa-angles-right"></i>'
        : '<i class="fa-solid fa-angles-left"></i>';

    // Place le bouton dans l'en-tête (titre) de la sidebar
    var titleEl = aside.querySelector('.sidebar-title');
    if (titleEl) {
        titleEl.style.display = 'flex';
        titleEl.style.alignItems = 'center';
        titleEl.style.justifyContent = 'space-between';
        titleEl.appendChild(toggle);
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        var isCollapsed = aside.classList.toggle('collapsed');
        localStorage.setItem(KEY, isCollapsed ? '1' : '0');
        toggle.innerHTML = isCollapsed
            ? '<i class="fa-solid fa-angles-right"></i>'
            : '<i class="fa-solid fa-angles-left"></i>';
    });
})();
</script>


</body>
</html>
