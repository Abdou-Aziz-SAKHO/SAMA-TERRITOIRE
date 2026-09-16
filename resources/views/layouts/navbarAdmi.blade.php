<header id="hdr">

    {{-- Logo --}}
    <a class="logo" href="{{ url('/Dashboard') }}">
        <img src="{{ asset('assets/img/TERRITOIRE.webp') }}" alt="Logo SAMA TERRITOIRE" class="logo-img">
    </a>


    {{-- Hamburger (affiché sur petit écran) --}}
    <button type="button" class="nav-hamburger" aria-label="Ouvrir le menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    {{-- Navigation Admin --}}
    <nav style="flex:1;display:flex;justify-content:center;gap:2px;">

        {{-- Accueil --}}
        <button class="nav-btn active" data-href="{{ url('/Dashboard') }}">
            <i class="fa-solid fa-house"></i>
            Accueil
        </button>


        {{-- Cartographie --}}
        {{-- <button class="nav-btn" data-href="{{ url('/CartographieAdmi') }}">
            <i class="fa-solid fa-location-dot"></i>
            Cartographie
        </button> --}}


        {{-- Statistiques --}}
        {{-- Statistiques (dropdown) --}}
        <div class="nav-dropdown">
            <button class="nav-btn nav-dropdown-toggle">
                <i class="fa-solid fa-chart-column"></i>
                Statistiques
                <i class="fa-solid fa-chevron-down dropdown-caret"></i>
            </button>
            <div class="nav-dropdown-menu">
                {{-- <button class="nav-dropdown-item" data-href="{{ url('/Dashboard') }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    Dashboard
                </button> --}}
                <button class="nav-dropdown-item" data-href="{{ url('/StatistiquesAdmi/VueGenerale') }}">
                    <i class="fa-solid fa-chart-line"></i>
                    Vue Générale
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/StatistiquesAdmi/Indicateur') }}">
                    <i class="fa-solid fa-bullseye"></i>
                    Indicateurs
                </button>
            </div>
        </div>


        {{-- Actualités --}}
        <button class="nav-btn" data-href="{{ url('/ActualitesAdmi') }}">
            <i class="fa-solid fa-newspaper"></i>
            Actualités
        </button>

        {{-- Commentaires --}}
        <button class="nav-btn" data-href="{{ url('/CommentairesAdmi') }}">
            <i class="fa-solid fa-comments"></i>
            Commentaires
            @if(($commentairesEnAttente ?? 0) > 0)
                <span style="background:var(--red,#c44030); color:#fff; font-size:9px; padding:1px 6px; border-radius:8px; font-weight:700; margin-left:3px;">{{ $commentairesEnAttente }}</span>
            @endif
        </button>


        {{-- Données --}}
        <button class="nav-btn" data-href="{{ url('/Donnees') }}">
            <i class="fa-solid fa-database"></i>
            Données
        </button>


        {{-- Utilisateurs --}}
        <button class="nav-btn" data-href="{{ url('/UtilisateursAdmi') }}">
            <i class="fa-solid fa-users"></i>
            Utilisateurs
        </button>

    </nav>

    {{-- Zone droite : Messages + Compte --}}
    <div class="header-right">

        {{-- Notifications --}}

        {{-- Messages --}}
        {{-- <button class="nav-icon-btn" title="Messages" data-href="{{ url('/MessagesAdmi') }}">
            <i class="fa-solid fa-message"></i> --}}

            {{-- Nombre de messages non lus --}}
            {{-- <span class="notification-badge">3</span>
        </button> --}}


        {{-- Compte --}}
        <div class="account-dropdown">

            <button class="nav-account-btn account-toggle">
                <i class="fa-solid fa-circle-user"></i>
                {{-- <span>Compte</span> --}}
                <i class="fa-solid fa-chevron-down account-caret"></i>
            </button>

            <div class="account-menu">

                <button class="account-menu-item" data-href="{{ url('/UtilisateursAdmi?profil=m') }}" title="Voir ma fiche">
                    <i class="fa-solid fa-user"></i>
                    Mon profil
                </button>

                <button class="account-menu-item" data-href="{{ url('/UtilisateursAdmi?modifier=m') }}" title="Modifier mes informations">
                    <i class="fa-solid fa-gear"></i>
                    Paramètres
                </button>

                <div class="account-divider"></div>

                <button class="account-menu-item logout" data-href="{{ url('/logout') }}">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Déconnexion
                </button>

            </div>

        </div>

    </div>


    {{-- Navigation JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('nav .nav-btn').forEach(function(btn) {

                btn.addEventListener('click', function() {

                    const href = btn.getAttribute('data-href');

                    if (href && href !== '#') {
                        window.location.href = href;
                    }

                });

            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Boutons de navigation simples (data-href direct)
            document.querySelectorAll('nav > .nav-btn[data-href]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const href = btn.getAttribute('data-href');
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                });
            });

            // Toggle des menus déroulants
            document.querySelectorAll('.nav-dropdown-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const parent = toggle.closest('.nav-dropdown');
                    const isOpen = parent.classList.contains('open');

                    // Ferme les autres dropdowns ouverts
                    document.querySelectorAll('.nav-dropdown.open').forEach(function(d) {
                        d.classList.remove('open');
                    });

                    if (!isOpen) {
                        parent.classList.add('open');
                    }
                });
            });

            // Clic sur un item de sous-menu
            document.querySelectorAll('.nav-dropdown-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    const href = item.getAttribute('data-href');
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                });
            });

            // Ferme les dropdowns au clic en dehors
            document.addEventListener('click', function() {
                document.querySelectorAll('.nav-dropdown.open').forEach(function(d) {
                    d.classList.remove('open');
                });
            });

        });
        // Menu Compte
        document.querySelectorAll('.account-toggle').forEach(function(toggle) {

            toggle.addEventListener('click', function(e) {

                e.stopPropagation();

                const parent = toggle.closest('.account-dropdown');

                // Fermer les autres menus
                document.querySelectorAll('.account-dropdown.open').forEach(function(menu) {
                    if (menu !== parent) {
                        menu.classList.remove('open');
                    }
                });

                parent.classList.toggle('open');
            });
        });


        // Navigation des éléments du compte
        document.querySelectorAll('.account-menu-item[data-href]').forEach(function(item) {

            item.addEventListener('click', function() {

                const href = item.getAttribute('data-href');

                if (href && href !== '#') {
                    window.location.href = href;
                }

            });

        });


        // Fermer le menu en cliquant ailleurs
        document.addEventListener('click', function() {

            document.querySelectorAll('.account-dropdown.open').forEach(function(menu) {
                menu.classList.remove('open');
            });

        });
    </script>

    {{-- Toggle hamburger (petits écrans) : ouvre/ferme le panneau de navigation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var hdr = document.getElementById('hdr');
            var burger = hdr ? hdr.querySelector('.nav-hamburger') : null;
            if (!hdr || !burger) return;

            burger.addEventListener('click', function (e) {
                e.stopPropagation();
                hdr.classList.toggle('nav-open');
                burger.innerHTML = hdr.classList.contains('nav-open')
                    ? '<i class="fa-solid fa-xmark"></i>'
                    : '<i class="fa-solid fa-bars"></i>';
            });

            // Ferme le panneau quand on choisit une destination
            document.querySelectorAll('#hdr nav .nav-btn[data-href]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    hdr.classList.remove('nav-open');
                    burger.innerHTML = '<i class="fa-solid fa-bars"></i>';
                });
            });

            // Ferme le panneau en cliquant ailleurs
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#hdr .nav-hamburger') && !e.target.closest('#hdr nav')) {
                    hdr.classList.remove('nav-open');
                    burger.innerHTML = '<i class="fa-solid fa-bars"></i>';
                }
            });
        });
    </script>

</header>
