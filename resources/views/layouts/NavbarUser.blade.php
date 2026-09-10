<header id="hdr">
   <a class="logo" href="{{ url('/') }}">
        <img src="{{ asset('assets/img/Territoire.png') }}" alt="Logo SAMA TERRITOIRE" class="logo-img">
    </a>

    {{-- Hamburger (affiché sur petit écran) --}}
    <button type="button" class="nav-hamburger" aria-label="Ouvrir le menu">
        <i class="fa-solid fa-bars"></i>
    </button>

  <nav style="flex:1;display:flex;justify-content:center;">
    <button class="nav-btn active" data-href="{{ url('/') }}"><i class="fa-solid fa-home"></i> Accueil</button>
    <button class="nav-btn " data-href="{{ url('cartographie') }}"><i class="fa-solid fa-map"></i> Cartographie</button>
    <button class="nav-btn " data-href="{{ url('statistique') }}"><i class="fa-solid fa-chart-bar"></i> Statistiques</button>
    <button class="nav-btn" data-href="{{ url('climat') }}"><i class="fa-solid fa-temperature-high"></i> Changements Climatiques<span class="badge-new">Nouveau</span></button>
  </nav>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('nav .nav-btn').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const href = btn.getAttribute('data-href');
          if(href) window.location.href = href;
        });
      });

      // Toggle hamburger pour les petits écrans
      var hdr = document.getElementById('hdr');
      var burger = hdr.querySelector('.nav-hamburger');
      if (burger) {
        burger.addEventListener('click', function (e) {
          e.stopPropagation();
          hdr.classList.toggle('nav-open');
          burger.innerHTML = hdr.classList.contains('nav-open')
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';
        });
        document.addEventListener('click', function (e) {
          if (!e.target.closest('#hdr .nav-hamburger') && !e.target.closest('#hdr nav')) {
            hdr.classList.remove('nav-open');
            burger.innerHTML = '<i class="fa-solid fa-bars"></i>';
          }
        });
      }
    });
  </script>
</header>
