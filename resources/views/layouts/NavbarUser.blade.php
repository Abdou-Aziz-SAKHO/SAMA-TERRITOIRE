<header id="hdr">
   <a class="logo" href="{{ url('/') }}">
        <img src="{{ asset('assets/img/Territoire.png') }}" alt="Logo SAMA TERRITOIRE" class="logo-img">
    </a>

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
    });
  </script>
</header>
