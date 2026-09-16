@extends('AppUser')


@section('content')
<div id="page-accueil" class="page active">

  {{-- ═══════════════════ BANNIÈRE HERO ═══════════════════ --}}
  <div class="hero-wrap">
    <section class="hero-banner">
      <div class="hero-banner-cta">
        <a class="hero-btn hero-btn-primary" href="{{ url('/cartographie') }}">
          <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i> Explorer la cartographie
        </a>
        <a class="hero-btn hero-btn-ghost" href="{{ route('climat') }}">
          <i class="fa-solid fa-cloud-sun" aria-hidden="true"></i> Changements climatiques
        </a>
      </div>
    </section>

    <div class="hero-stack">
      <div class="hero-kpis-bar">
        <div class="kpi-item"><div class="kpi-num" id="k-total">500</div><div class="kpi-lbl">Infrastructures</div></div>
        <div class="kpi-item"><div class="kpi-num" id="k-villages">150</div><div class="kpi-lbl">Villages Recensés</div></div>
        <div class="kpi-item"><div class="kpi-num" id="k-secteurs">8</div><div class="kpi-lbl">Secteurs d'activité</div></div>
        <div class="kpi-item"><div class="kpi-num">2022</div><div class="kpi-lbl">Année de collecte</div></div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════ PRÉSENTATION SAMA TERRITOIRE ═══════════════════ --}}
  <section class="intro-sama">
    <div class="intro-sama-deco intro-sama-deco-l"></div>
    <div class="intro-sama-deco intro-sama-deco-r"></div>
    <div class="intro-sama-inner">
      <span class="intro-sama-kicker">Plateforme de données territoriales</span>
      <img class="intro-sama-logo" src="{{ asset('assets/img/TERRITOIRE-MONOGRAME.webp') }}" alt="SAMA TERRITOIRE">
      <p class="intro-sama-text">
        SAMA TERRITOIRE est la plateforme régionale de référence pour consulter, explorer et
        valoriser les données du territoire. Elle centralise les informations géographiques, les
        infrastructures, les indicateurs sectoriels et les ressources documentaires de la région,
        afin d'appuyer la décision publique et de rendre le territoire plus lisible pour tous.
      </p>
      <div class="intro-sama-pills">
        <span><i class="fa-solid fa-magnifying-glass-chart"></i> Consulter les données territoriales</span>
        <span><i class="fa-solid fa-building"></i> Explorer les infrastructures &amp; indicateurs</span>
        <span><i class="fa-solid fa-chart-line"></i> Valoriser les informations du territoire</span>
        <span><i class="fa-solid fa-comment"></i> Vous avez votre Mot a dire </span>
      </div>
    </div>
  </section>

  {{-- ═══════════════════ MODULES ═══════════════════ --}}
  <section class="hero-modules">
    <h2 class="hero-modules-title">Explorez le territoire</h2>
    <div class="modules">
      <a class="mod" style="--mod-color:var(--primary)" href="{{ url('/cartographie') }}">
        <div class="mod-icon">🗺</div>
        <div class="mod-title">Cartographie interactive</div>
        <div class="mod-desc">Filtres en cascade Région→Département→Commune→Village→Secteur→Type. Fiches détaillées par infrastructure.</div>
        <div class="mod-arrow">→</div>
      </a>
      <a class="mod" style="--mod-color:var(--blue)" href="{{ route('statistique') }}">
        <div class="mod-icon">📊</div>
        <div class="mod-title">Tableau de bord statistique</div>
        <div class="mod-desc">Indicateurs clés, répartitions sectorielles, analyses de l'éducation, hydraulique et santé.</div>
        <div class="mod-arrow">→</div>
      </a>
      <a class="mod" style="--mod-color:var(--accent)" href="{{ route('climat') }}">
        <div class="mod-icon">🌍</div>
        <div class="mod-title">Changements Climatiques</div>
        <div class="mod-desc">Cartes d'occupation du sol 2000–2050, températures, précipitations, déforestation, zones inondables.</div>
        <div class="mod-arrow">→</div>
      </a>
      <a class="mod" style="--mod-color:var(--purple)" href="#commentaires-plaintes">
        <div class="mod-icon">💬</div>
        <div class="mod-title">Commentaires & Plaintes</div>
        <div class="mod-desc">Faite vos commentaires ou plaintes sur votre Territoire et les autres territoires .</div>
        <div class="mod-arrow">→</div>
      </a>
    </div>
  </section>

  {{-- ═══════════════════ ACTUALITÉS (6 derniers mois) ═══════════════════ --}}
  @include('PageUser.partials.actualites-mosaique', [
      'actualites'     => $actualites,
      'actusTitre'     => 'Actualités',
      'actusSousTitre' => 'Les dernières nouvelles du territoire publiées au cours des six derniers mois.',
      'actusToutesUrl' => route('actualites.publiques'),
  ])

  {{-- ═══════════════════ COMMENTAIRES & PLAINTES ═══════════════════ --}}
  <section class="comment-territoire" id="commentaires-plaintes">
    <div class="comment-territoire-grid">

      <div class="comment-territoire-intro">
        <span class="intro-sama-kicker">Votre avis compte</span>
        <h2 class="comment-territoire-title">Commentaires &amp; Plaintes</h2>
        <p class="comment-territoire-text">
          Vous êtes une association, une autorité locale, un citoyen ou un partenaire du territoire ?
          Faites-nous part de vos commentaires, suggestions ou plaintes. Votre message est transmis
          aux équipes de l'Agence Régionale de Développement pour traitement.
        </p>
        <ul class="comment-territoire-list">
          <li><i class="fa-solid fa-circle-check"></i> Aucun compte requis</li>
          <li><i class="fa-solid fa-circle-check"></i> Traitement par l'ARD Kaolack</li>
          <li><i class="fa-solid fa-circle-check"></i> Transmis aux Autorites consernees</li>
        </ul>
      </div>

      <form class="comment-territoire-form" method="POST" action="{{ route('commentaires.envoyer') }}" novalidate>
        @csrf
        <input type="hidden" name="form" value="commentaire">

        @if(session('succes_commentaire'))
          <div class="comment-territoire-succes">
            <i class="fa-solid fa-circle-check"></i> {{ session('succes_commentaire') }}
          </div>
        @endif

        <div class="comment-territoire-succes" id="ct-succes-js" hidden>
          <i class="fa-solid fa-circle-check"></i> Votre message a bien été envoyé. Merci pour votre contribution !
        </div>

        @if($errors->any() && old('form') === 'commentaire')
          <div class="comment-territoire-erreur">
            <i class="fa-solid fa-circle-exclamation"></i> Veuillez corriger les champs ci-dessous.
          </div>
        @endif

        <div class="comment-territoire-row">
          <div class="comment-territoire-champ">
            <label for="ct-nom">Nom <span style="font-weight:400;text-transform:none;letter-spacing:0">(optionnel: Si vous ne voulez pas vous identifier)</span></label>
            <input type="text" id="ct-nom" name="nom" value="{{ old('nom') }}" maxlength="255" placeholder="Votre nom (optionnel)">
            @error('nom')<span class="comment-territoire-msg">{{ $message }}</span>@enderror
          </div>
          <div class="comment-territoire-champ">
            <label for="ct-email">Adresse e-mail</label>
            <input type="email" id="ct-email" name="email" value="{{ old('email') }}" maxlength="255" placeholder="vous@exemple.com" required>
            @error('email')<span class="comment-territoire-msg">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="comment-territoire-champ">
          <label for="ct-type">Type</label>
          <select id="ct-type" name="type" required>
            <option value="commentaire" @selected(old('type') === 'commentaire')>💬 Commentaire</option>
            <option value="plainte" @selected(old('type') === 'plainte')>🔴 Plainte</option>
          </select>
          @error('type')<span class="comment-territoire-msg">{{ $message }}</span>@enderror
        </div>

        <div class="comment-territoire-champ">
          <label for="ct-message">Message</label>
          <textarea id="ct-message" name="message" rows="5" maxlength="5000" placeholder="Décrivez votre commentaire ou votre plainte…" required>{{ old('message') }}</textarea>
          @error('message')<span class="comment-territoire-msg">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="comment-territoire-envoyer">
          <i class="fa-solid fa-paper-plane"></i> Envoyer
        </button>
      </form>

    </div>
  </section>

</div>

<style>
  html { scroll-behavior:smooth; }
  /* ═══════════ PRÉSENTATION SAMA TERRITOIRE ═══════════ */
  .intro-sama { position:relative; padding: 120px 24px 48px; overflow:hidden; background:#fdfefe; }
  .intro-sama-inner { position:relative; max-width: 820px; margin: 0 auto; text-align: center; z-index:2; }
  .intro-sama-kicker,
  .comment-territoire .intro-sama-kicker {
    display:inline-flex; align-items:center; gap:8px; font-size:11px; font-weight:700; letter-spacing:.14em; text-transform:uppercase;
    color:var(--primary); margin-bottom:14px; padding:6px 16px; border:1px solid #b5dfc6; background:var(--primary-lt); border-radius:999px;
  }
  .intro-sama-kicker::before, .intro-sama-kicker::after {
    content:""; width:14px; height:2px; border-radius:2px; background:var(--primary); opacity:.5;
  }
  .intro-sama-logo { width:300px; height:auto; display:block; margin:0 auto 4px; filter:drop-shadow(0 4px 12px rgba(38,122,71,.25)); }
  .intro-sama-connector { display:flex; align-items:center; justify-content:center; gap:6px; margin:0 auto 22px; max-width:280px; }
  .intro-sama-dot { width:6px; height:6px; border-radius:50%; background:var(--blue); flex-shrink:0; }
  .intro-sama-line { flex:1; height:1px; background:repeating-linear-gradient(90deg, var(--border2) 0 5px, transparent 5px 9px); }
  .intro-sama-text { font-size:17px; color:var(--text); line-height:1.8; max-width:680px; margin:0 auto 26px; font-weight:400; }
  .intro-sama-pills { display:flex; justify-content:center; align-items:center; flex-wrap:nowrap; gap:10px 12px; }
  .intro-sama-pills span {
    display:inline-flex; align-items:center; gap:4px; font-size:12.5px; font-weight:600; color:var(--text);
    background:var(--surface); border:1px solid var(--border); padding:5px 7px; border-radius:999px;
    box-shadow:0 2px 6px rgba(26,45,34,.05); transition:box-shadow .2s, transform .2s, border-color .2s;
  }
  .intro-sama-pills span:hover { transform:translateY(-2px); border-color:#9bc8ad; box-shadow:0 6px 14px rgba(38,122,71,.12); }
  .intro-sama-pills i { color:var(--primary); }
  .intro-sama-pills span:nth-child(2) i { color:var(--blue); }

  /* Décors : filigranes carte & données */
  .intro-sama-deco { position:absolute; z-index:1; pointer-events:none; opacity:.5; }
  .intro-sama-deco-l { left:-30px; top:20px; width:150px; height:150px; }
  .intro-sama-deco-r { right:-30px; bottom:10px; width:180px; height:180px; }
  .intro-sama-deco-l, .intro-sama-deco-r {
    border:2px solid var(--border2); border-radius:16px;
    background-image:
      radial-gradient(circle at 30% 60%, var(--primary-lt) 0 3px, transparent 3.5px),
      radial-gradient(circle at 70% 28%, var(--blue-lt) 0 3px, transparent 3.5px),
      radial-gradient(circle at 80% 75%, var(--primary-lt) 0 3px, transparent 3.5px);
  }
  .intro-sama-deco-l::before, .intro-sama-deco-r::before {
    content:""; position:absolute; width:70%; height:1px; background:repeating-linear-gradient(90deg, var(--border2) 0 4px, transparent 4px 8px);
  }
  .intro-sama-deco-l::before { left:15%; top:50%; }
  .intro-sama-deco-r::before { right:15%; top:38%; }
  .intro-sama-deco-l::after, .intro-sama-deco-r::after {
    content:""; position:absolute; width:1px; height:60%; background:repeating-linear-gradient(180deg, var(--border2) 0 4px, transparent 4px 8px);
  }
  .intro-sama-deco-l::after { left:60%; top:10%; }
  .intro-sama-deco-r::after { right:55%; top:20%; }

  /* ═══════════ COMMENTAIRES & PLAINTES ═══════════ */
  .comment-territoire { background:#f0f1ef; max-width:none; margin:0; padding:64px 24px; }
  .comment-territoire-grid { max-width:1100px; margin:0 auto; }
  .comment-territoire-grid { display:grid; grid-template-columns: .9fr 1.1fr; gap:44px; align-items:start; background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:44px 48px; box-shadow:var(--shadow); }
  .comment-territoire-title { font-family:'Montserrat',sans-serif; font-size:30px; font-weight:800; color:#1a1a2e; line-height:1.15; margin-bottom:16px; }
  .comment-territoire-text { font-size:14px; color:var(--text-dim); line-height:1.7; margin-bottom:20px; }
  .comment-territoire-list { list-style:none; padding:0; margin:0; display:grid; gap:10px; }
  .comment-territoire-list li { display:flex; align-items:center; gap:9px; font-size:13px; color:var(--text); font-weight:500; }
  .comment-territoire-list i { color:var(--primary); }
  .comment-territoire-form { display:grid; gap:14px; }
  .comment-territoire-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .comment-territoire-champ { display:flex; flex-direction:column; gap:6px; }
  .comment-territoire-champ label { font-size:11px; font-weight:700; color:var(--text-dim); text-transform:uppercase; letter-spacing:.4px; }
  .comment-territoire-champ input,
  .comment-territoire-champ select,
  .comment-territoire-champ textarea {
    width:100%; padding:11px 13px; font-family:'DM Sans',sans-serif; font-size:13.5px; color:var(--text);
    background:var(--surface2); border:1px solid var(--border); border-radius:10px; outline:none; box-sizing:border-box; transition:.15s;
  }
  .comment-territoire-champ textarea { resize:vertical; min-height:120px; }
  .comment-territoire-champ input:focus,
  .comment-territoire-champ select:focus,
  .comment-territoire-champ textarea:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(38,122,71,.12); }
  .comment-territoire-msg { font-size:11.5px; color:#b3261e; }
  .comment-territoire-succes, .comment-territoire-erreur {
    display:flex; align-items:center; gap:10px; padding:12px 15px; border-radius:11px; font-size:13px; font-weight:600;
  }
  .comment-territoire-succes[hidden] { display:none; }
  .comment-territoire-succes { background:#e9f7ee; border:1px solid #b5dfc6; color:var(--primary); }
  .comment-territoire-erreur { background:#fdecea; border:1px solid #f5c6c2; color:#b3261e; }
  .comment-territoire-envoyer {
    justify-self:end; display:inline-flex; align-items:center; gap:9px; padding:12px 26px; font-family:'DM Sans',sans-serif;
    font-size:14px; font-weight:700; color:#1a1a2e; background:#d8eee2; border:none; border-radius:999px; cursor:pointer;
    box-shadow:0 3px 12px rgba(38,122,71,.22); transition:.2s;
  }
  .comment-territoire-envoyer:hover { background:#1d5f39; color:#fff; transform:translateY(-1px); }

  /* ═══ WRAPPER ═══ */
  .hero-wrap { position: relative; width: 100%; overflow: visible; }

  /* ═══ IMAGE SEULE ═══ */
  .hero-banner {
    position: relative;
    z-index: 5;
    width: 100%;
    aspect-ratio: 1792 / 592;
    max-height: 72vh;
    margin: 0 auto;
    overflow: visible;
    background-color: #0d2718;
    background-image:
      linear-gradient(rgba(255,255,255,.06), rgba(13,36,22,.18)),
      url('{{ asset('assets/img/baniere_Vert.webp') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

  /* ═══ BOUTONS (dessinés sur l'image) ═══ */
  .hero-banner-cta {
    position: absolute;
    left: 0; right: 0; bottom: 36px;
    z-index: 6;
    display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
    padding: 0 24px;
    pointer-events: none;
  }
  .hero-banner-cta .hero-btn { pointer-events: auto; }
  .hero-btn {
    display: inline-flex; align-items: center; gap: 9px;
    padding: 12px 24px; border-radius: 999px; font-size: 14px; font-weight: 700;
    text-decoration: none; font-family: 'DM Sans', sans-serif;
    background: rgba(13,36,22,.35);
    border: 1.5px solid rgba(255,255,255,.55);
    color: #fff;
    backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    box-shadow: 0 6px 20px rgba(0,0,0,.22);
    transition: transform .18s ease, background .18s ease, border-color .18s ease;
  }
  .hero-btn:hover { transform: translateY(-2px); background: rgba(13,36,22,.55); border-color: #fff; }
  .hero-btn-primary { background: rgba(13,36,22,.30); }
  .hero-btn-primary:hover { background: rgba(13,36,22,.52); }
  .hero-btn-ghost { background: rgba(13,36,22,.35); }
  .hero-btn-ghost:hover { background: rgba(13,36,22,.55); }

  /* ═══ CONTENU (KPI) ═══ */
  .hero-stack {
    position: absolute;
    left: 0; right: 0; bottom: 0;
    z-index: 5;
    width: 100%; max-width: 920px;
    margin: 0 auto;
    transform: translateY(80%);
    animation: fadeIn .7s .15s both;
  }

  /* ═══ KPI ═══ */
  .hero-kpis-bar {
    display: flex; align-items: stretch; justify-content: center; flex-wrap: wrap;
    width: 100%;
    background: #fffffffa; border: 1px solid var(--border); border-radius: 16px; overflow: hidden;
    box-shadow: var(--shadow-lg);
  }
  .hero-kpis-bar .kpi-item {
    flex: 1 1 150px; padding: 18px 26px; text-align: center;
    border-right: 1px solid var(--border); flex-direction: column; align-items: center;
  }
  .hero-kpis-bar .kpi-item:last-child { border-right: none; }
  .hero-kpis-bar .kpi-num {
    font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 2.6vw, 34px);
    font-weight: 800; color: var(--text); line-height: 1.1;
  }
  .hero-kpis-bar .kpi-lbl {
    font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .7px; margin-top: 4px;
  }

  /* Modules */
  .hero-modules { max-width:none; margin:0; padding:56px 24px 64px; background:#f0f1ef; }
  .hero-modules > h2, .hero-modules .modules { max-width:1100px; margin-left:auto; margin-right:auto; }
.hero-modules-title {
    font-family:'Montserrat',sans-serif; font-size:34px; font-weight:800; color:#1a1a2e; line-height:1.1;
    margin-bottom: 22px; text-align: left;
  }
  .hero-modules .mod { text-decoration: none; }

  /* ══════════════ TABLETTE (iPad) : 2×2 aligné ══════════════ */
  @media (min-width: 701px) and (max-width: 1100px) {
    .modules { grid-template-columns: repeat(2, 1fr) !important; gap: 14px !important; }
  }

  @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }

  /* ══════════════ MOBILE ══════════════ */
  @media (max-width: 820px) {
    /* Wrapper : flux normal, pas de dépassement */
    .hero-wrap { overflow: hidden; }

    /* Image : ratio exact, contain, fond transparent, bien centrée */
    .hero-banner {
      aspect-ratio: 1792 / 592;
      max-height: none;
      background-color: transparent;
      background-image: url('{{ asset('assets/img/baniere_Vert.webp') }}');
      background-size: contain;
      background-position: center center;
      background-repeat: no-repeat;
    }

    /* Boutons : petits, en bas à droite SUR l'image */
    .hero-banner-cta {
      left: auto; right: 10px; bottom: 8px;
      flex-direction: column; align-items: flex-end;
      gap: 6px;
      padding: 0;
      width: auto;
    }
    .hero-btn {
      pointer-events: auto;
      padding: 6px 12px; font-size: 11px; gap: 6px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.65);
      background: rgba(13,36,22,.5);
      color: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,.25);
      backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    }

    /* Contenu (KPI) : sous l'image, en flux normal */
    .hero-stack {
      position: relative;
      bottom: auto; left: auto; right: auto;
      transform: none;
      width: 100%;
      max-width: none;
      padding: 14px 16px 0;
      animation: none;
    }

    /* KPI : 2×2, compact, centré */
    .hero-kpis-bar {
      width: 100%; max-width: 400px;
      margin: 0 auto;
      border-radius: 12px;
    }
    .hero-kpis-bar .kpi-item {
      flex: 1 1 44%; padding: 10px 12px;
      border-bottom: 1px solid var(--border); border-right: 1px solid var(--border);
    }
    .hero-kpis-bar .kpi-item:nth-child(odd) { border-right: 1px solid var(--border); }
    .hero-kpis-bar .kpi-item:nth-child(even) { border-right: none; }
    .hero-kpis-bar .kpi-item:nth-child(-n+2) { border-top: none; }
    .hero-kpis-bar .kpi-item:nth-last-child(-n+2) { border-bottom: none; }
    .hero-kpis-bar .kpi-num { font-size: clamp(18px, 5vw, 24px); }
    .hero-kpis-bar .kpi-lbl { font-size: 9px; letter-spacing: .4px; margin-top: 2px; }

    .hero-modules { padding: 28px 16px 48px; }
    .hero-modules-title { font-size: 20px; margin-bottom: 16px; }

    .intro-sama { padding: 24px 16px 40px; }
    .intro-sama-text { font-size: 14px; }
    .intro-sama-pills { flex-wrap:wrap; }
    .intro-sama-pills span { font-size: 11.5px; padding: 7px 12px; }
    .intro-sama-deco { opacity:.25; }
    .intro-sama-deco-l { left:-40px; }
    .intro-sama-deco-r { right:-40px; }
    .intro-sama-logo { width:187px; }
    .comment-territoire { padding: 40px 16px; }
    .comment-territoire-grid { grid-template-columns:1fr; gap:28px; padding:28px 20px; }
    .comment-territoire-title { font-size: 24px; }
    .comment-territoire-row { grid-template-columns:1fr; }
  }

  @media (prefers-reduced-motion: reduce) {
    .hero-stack { animation: none !important; }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
      ['k-total', 'k-villages', 'k-secteurs'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var target = parseInt(el.textContent, 10);
        if (isNaN(target) || target < 1) return;
        var dur = 900, t0 = null;
        function step(ts) {
          if (!t0) t0 = ts;
          var p = Math.min((ts - t0) / dur, 1);
          var eased = 1 - Math.pow(1 - p, 3);
          el.textContent = Math.round(target * eased);
          if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
      });
    }, 650);
  });

  /* Soumission AJAX du formulaire Commentaires & Plaintes : écran immobile, succès affiché sur place */
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('.comment-territoire-form');
    if (!form) return;
    e.preventDefault();

    var bouton = form.querySelector('.comment-territoire-envoyer');
    var succes = document.getElementById('ct-succes-js');
    var erreurExistant = form.querySelector('.comment-territoire-erreur');
    if (!bouton || !succes) return;

    bouton.disabled = true;
    succes.hidden = true;
    if (erreurExistant) erreurExistant.remove();

    var donnees = new URLSearchParams(new FormData(form));
    fetch(form.action, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: donnees
    })
    .then(function (r) { return r.json(); })
    .then(function (json) {
      bouton.disabled = false;
      if (json && json.succes) {
        succes.hidden = false;
        form.reset();
      } else {
        var msg = (json && json.errors) ? Object.values(json.errors)[0][0] : '';
        if (msg) {
          alert('Erreur : ' + msg);
        } else {
          alert('Une erreur est survenue. Veuillez réessayer.');
        }
      }
    })
    .catch(function () {
      bouton.disabled = false;
      alert('Impossible de contacter le serveur. Veuillez réessayer.');
    });
  });
</script>
@endsection
