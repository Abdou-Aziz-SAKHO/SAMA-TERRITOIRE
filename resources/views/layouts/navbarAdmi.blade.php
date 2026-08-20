<header id="hdr">

    {{-- Logo --}}
    <a class="logo" href="{{ url('/Dashboard') }}">
        <img src="{{ asset('assets/img/Territoire.png') }}" alt="Logo SAMA TERRITOIRE" class="logo-img">
    </a>


    {{-- Navigation Admin --}}
    <nav style="flex:1;display:flex;justify-content:center;gap:2px;">

        {{-- Accueil --}}
        <button class="nav-btn active" data-href="{{ url('/Dashboard') }}">
            <i class="fa-solid fa-house"></i>
            Accueil
        </button>


        {{-- Cartographie --}}
        <button class="nav-btn" data-href="{{ url('/CartographieAdmi') }}">
            <i class="fa-solid fa-location-dot"></i>
            Cartographie
        </button>


        {{-- Statistiques --}}
        {{-- Statistiques (dropdown) --}}
        <div class="nav-dropdown">
            <button class="nav-btn nav-dropdown-toggle">
                <i class="fa-solid fa-chart-column"></i>
                Statistiques
                <i class="fa-solid fa-chevron-down dropdown-caret"></i>
            </button>
            <div class="nav-dropdown-menu">
                <button class="nav-dropdown-item" data-href="{{ url('/StatistiquesAdmi/Indicateur') }}">
                    <i class="fa-solid fa-gauge"></i>
                    Indicateur
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/StatistiquesAdmi/VueGenerale') }}">
                    <i class="fa-solid fa-chart-line"></i>
                    Vue Générale
                </button>
            </div>
        </div>


        {{-- Actualités --}}
        <button class="nav-btn" data-href="{{ url('/ActualitesAdmi') }}">
            <i class="fa-solid fa-newspaper"></i>
            Actualités
        </button>


        {{-- Données --}}
        <div class="nav-dropdown">
            <button class="nav-btn nav-dropdown-toggle">
                <i class="fa-solid fa-database"></i>
                Données
                <i class="fa-solid fa-chevron-down dropdown-caret"></i>
            </button>
            <div class="nav-dropdown-menu">
                <button class="nav-dropdown-item" data-href="{{ url('/DonneesAdmi/Region') }}">
                    <i class="fa-solid fa-location-dot"></i>
                    Régions
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/DonneesAdmi/Departement') }}">
                    <i class="fa-solid fa-location-dot"></i>
                    Départements
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/DonneesAdmi/Departement') }}">
                    <i class="fa-solid fa-location-dot"></i>
                    Communes
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/DonneesAdmi/Departement') }}">
                    <i class="fa-solid fa-location-dot"></i>
                    localités
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/DonneesAdmi/Departement') }}">
                    <i class="fa-solid fa-street-view"></i>
                    Secteurs
                </button>
                <button class="nav-dropdown-item" data-href="{{ url('/DonneesAdmi/Departement') }}">
                    <i class="fa-solid fa-landmark"></i>
                    infrastructures
                </button>
            </div>
        </div>


        {{-- Utilisateurs --}}
        <button class="nav-btn" data-href="{{ url('/U') }}">
            <i class="fa-solid fa-users"></i>
            Utilisateurs
        </button>

    </nav>

    {{-- Zone droite : Messages + Compte --}}
    <div class="header-right">

        {{-- Notifications --}}

        {{-- Messages --}}
        <button class="nav-icon-btn" title="Messages" data-href="{{ url('/MessagesAdmi') }}">
            <i class="fa-solid fa-message"></i>

            {{-- Nombre de messages non lus --}}
            <span class="notification-badge">3</span>
        </button>


        {{-- Compte --}}
        <div class="account-dropdown">

            <button class="nav-account-btn account-toggle">
                <i class="fa-solid fa-circle-user"></i>
                {{-- <span>Compte</span> --}}
                <i class="fa-solid fa-chevron-down account-caret"></i>
            </button>

            <div class="account-menu">

                <button class="account-menu-item" data-href="{{ url('/CompteAdmi') }}">
                    <i class="fa-solid fa-user"></i>
                    Mon profil
                </button>

                <button class="account-menu-item" data-href="{{ url('/ParametresAdmi') }}">
                    <i class="fa-solid fa-gear"></i>
                    Paramètres
                </button>

                <div class="account-divider"></div>

                <button class="account-menu-item logout">
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

</header>
