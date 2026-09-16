@extends('AppUser')

@section('content')
<div id="page-apropos" class="page active">

  {{-- ═══════════════════ HERO ═══════════════════ --}}
  <section class="apropos-hero">
    <div class="apropos-hero-inner">
      <span class="apropos-kicker"><i class="fa-solid fa-leaf"></i>A PROPOS DE SAMA TERRITOIRE</span>
      <h1 class="apropos-title">Connaitre Le Territoire Pour  <span class="apropos-title-accent">Mieux Agir</span></h1>
      <p class="apropos-hero-text">SAMA TERRITOIRE est une plateforme régionale initiée par L'Agence Régionale de Développent (ARD) de Kaolack Pour centraliser , Visualiser et valoriser les données territoriales . Elle facilite la planification, le suivie et la prise de décision pour un développement durable et inclusif du Territoire .</p>
    </div>
  </section>

  {{-- ═══════════════════ CONTENU ═══════════════════ --}}
  <section class="apropos-section">
    <div class="apropos-grid">

      <div class="apropos-card">
        <div class="apropos-card-icon">🎯</div>
        <h2>Notre mission</h2>
        <p>Offrir à chaque citoyen, élu et collectivité un accès clair et simple aux données territoriales : cartographie des infrastructures, statistiques sectorielles et suivi des changements climatiques.</p>
      </div>

      <div class="apropos-card">
        <div class="apropos-card-icon">🗺</div>
        <h2>Cartographie interactive</h2>
        <p>Explorez le territoire par une navigation en cascade Région → Département → Commune → Village → Secteur → Type, avec des fiches détaillées sur chaque infrastructure.</p>
      </div>

      <div class="apropos-card">
        <div class="apropos-card-icon">📊</div>
        <h2>Données & statistiques</h2>
        <p>Consultez les indicateurs clés des secteurs de l'éducation, de l'hydraulique de la santé et d'autres pour suivre l'évolution du territoire au fil des années.</p>
      </div>

      <div class="apropos-card">
        <div class="apropos-card-icon">🌍</div>
        <h2>Changements climatiques</h2>
        <p>Analysez les cartes d'occupation du sol 2022–2050, les températures, les précipitations, la déforestation et les zones inondables.</p>
      </div>

      <div class="apropos-card">
        <div class="apropos-card-icon">💬</div>
        <h2>Votre voix compte</h2>
        <p>Partagez vos commentaires et vos plaintes : chaque retour d'expérience contribue à améliorer l'information et les services rendus aux citoyens.</p>
      </div>

      <div class="apropos-card">
        <div class="apropos-card-icon">🤝</div>
        <h2>Un projet participatif</h2>
        <p>SAMA TERRITOIRE repose sur la participation de tous. Vos retours alimentent la connaissance du territoire et son développement durable.</p>
      </div>

    </div>
  </section>

  {{-- ═══════════════════ ARD — PRÉSENTATION ═══════════════════ --}}
  <section class="apropos-ard">
    <div class="apropos-ard-inner">

      {{-- Bandeau : logo + texte + chiffres clés --}}
      <div class="apropos-ard-hd">
        <img class="apropos-ard-logo" src="{{ asset('assets/img/LOGO-ARD.webp') }}" alt="Logo de l'ARD de Kaolack">
        <div class="apropos-ard-hd-texte">
          <span class="apropos-ard-kicker"><i class="fa-solid fa-building-columns"></i> Partenaire officiel du territoire</span>
          <h2 class="apropos-ard-titre">Agence Régionale de Développement de <span class="apropos-ard-accent">Kaolack</span></h2>
          <p class="apropos-ard-phrase">« Ensemble pour un développement local durable et une région ouverte sur le monde. »</p>
        </div>
        <div class="apropos-ard-chiffres">
          <div class="apropos-ard-chiffre"><strong>3</strong><span>Départements</span></div>
          <div class="apropos-ard-chiffre"><strong>41</strong><span>Communes</span></div>
          <div class="apropos-ard-chiffre"><strong>1</strong><span>Région ouverte sur le monde</span></div>
        </div>
      </div>

      {{-- Deux colonnes : description + axes d'intervention --}}
      <div class="apropos-ard-bas">
        <div class="apropos-ard-col-gauche">
          <p class="apropos-ard-desc">
            L'ARD de Kaolack est un dispositif technique décentralisé au service du
            développement territorial durable : elle accompagne les collectivités,
            mobilise les partenaires et valorise le potentiel de toute la région.
          </p>
          <a class="apropos-ard-btn" href="https://arddekaolack.sn/" target="_blank" rel="noopener">
            Visiter le site officiel <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </a>
          <div class="apropos-ard-coord">
            <span><i class="fa-solid fa-location-dot"></i> Villa N°379, HLM Bongré Salin, Kaolack</span>
            <span><i class="fa-solid fa-phone"></i> +221 33 941 77 53</span>
            <span><i class="fa-solid fa-envelope"></i> contact@arddekaolack.sn</span>
          </div>
        </div>

        <div class="apropos-ard-col-droite">
          <h3 class="apropos-ard-axes-titre">Nos axes d'intervention</h3>
          <div class="apropos-ard-axes">
            <div class="apropos-ard-axe"><i class="fa-solid fa-landmark"></i><span>Décentralisation</span></div>
            <div class="apropos-ard-axe"><i class="fa-solid fa-scale-balanced"></i><span>Gouvernance locale</span></div>
            <div class="apropos-ard-axe"><i class="fa-solid fa-users"></i><span>Animation territoriale</span></div>
            <div class="apropos-ard-axe"><i class="fa-solid fa-map-location-dot"></i><span>Planification locale</span></div>
            <div class="apropos-ard-axe"><i class="fa-solid fa-diagram-project"></i><span>Gestion de projet</span></div>
            <div class="apropos-ard-axe"><i class="fa-solid fa-chart-line"></i><span>Économie territoriale</span></div>
          </div>
        </div>
      </div>

      {{-- Bande défilante : partenaires --}}
      <div class="apropos-ard-partenaires">
        <h3 class="apropos-ard-partenaires-titre">Nos partenaires</h3>
        <div class="apropos-ard-partenaires-bande">
          <div class="apropos-ard-partenaires-track">
            <div class="apropos-ard-part"><i class="fa-solid fa-landmark"></i><span>Banque Mondiale</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-building-columns"></i><span>BAD</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-handshake"></i><span>Enabel</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-tractor"></i><span>PACASEN</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-seedling"></i><span>Seen Suuf</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-globe"></i><span>ID Territoire</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-people-group"></i><span>PAIJEF</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-shield-halved"></i><span>PDZP</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-house-chimney"></i><span>PNDL</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-earth-europe"></i><span>GIZ</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-flag"></i><span>Coop. Italienne</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-flag"></i><span>Coop. Espagnole</span></div>
            {{-- Duplication pour boucle infinie --}}
            <div class="apropos-ard-part"><i class="fa-solid fa-landmark"></i><span>Banque Mondiale</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-building-columns"></i><span>BAD</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-handshake"></i><span>Enabel</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-tractor"></i><span>PACASEN</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-seedling"></i><span>Seen Suuf</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-globe"></i><span>ID Territoire</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-people-group"></i><span>PAIJEF</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-shield-halved"></i><span>PDZP</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-house-chimney"></i><span>PNDL</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-earth-europe"></i><span>GIZ</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-flag"></i><span>Coop. Italienne</span></div>
            <div class="apropos-ard-part"><i class="fa-solid fa-flag"></i><span>Coop. Espagnole</span></div>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- ═══════════════════ VALEURS ═══════════════════ --}}
  <section class="apropos-valeurs">
    <div class="apropos-valeurs-inner">
      <h2 class="apropos-valeurs-title">Nos valeurs</h2>
      <div class="apropos-valeurs-row">
        <div class="apropos-valeur"><span class="apropos-valeur-num">01</span><strong>Transparence</strong><p>Des données ouvertes, accessibles à tous.</p></div>
        <div class="apropos-valeur"><span class="apropos-valeur-num">02</span><strong>Participation</strong><p>Chacun peut contribuer et donner son avis.</p></div>
        <div class="apropos-valeur"><span class="apropos-valeur-num">03</span><strong>Durabilité</strong><p>Des outils pensés pour le long terme.</p></div>
        <div class="apropos-valeur"><span class="apropos-valeur-num">04</span><strong>Proximité</strong><p>Une information ancrée dans le territoire.</p></div>
      </div>
    </div>
  </section>

  {{-- ═══════════════════ MENTIONS LÉGALES ═══════════════════ --}}
  <section class="apropos-mentions">
    <div class="apropos-mentions-inner">
      <span class="apropos-mentions-kicker"><i class="fa-solid fa-scale-balanced"></i> Cadre légal</span>
      <h2 class="apropos-mentions-title">Mentions légales</h2>

      <div class="apropos-mentions-grid">
        <div class="apropos-mention">
          <div class="apropos-mention-icone"><i class="fa-solid fa-building-shield"></i></div>
          <h3>Éditeur</h3>
          <p>La plateforme <strong>SAMA TERRITOIRE</strong> est éditée et administrée par la structure territoriale en charge de la collecte et de la mise à disposition des données du territoire ARD de kaolack.</p>
        </div>
        <div class="apropos-mention">
          <div class="apropos-mention-icone"><i class="fa-solid fa-server"></i></div>
          <h3>Hébergement & données</h3>
          <p>La plateforme est hébergée sur un serveur local . Les données présentées proviennent des services techniques et des collectivités territoriales.</p>
        </div>
        <div class="apropos-mention">
          <div class="apropos-mention-icone"><i class="fa-solid fa-copyright"></i></div>
          <h3>Propriété intellectuelle</h3>
          <p>Les textes, images, cartes et données de cette plateforme sont protégés. Toute reproduction doit mentionner la source et respecter les droits des auteurs.</p>
        </div>
      </div>

      <p class="apropos-mentions-note">
        <i class="fa-solid fa-circle-info"></i>
        Les informations présentes sur cette plateforme sont fournies à titre indicatif.
      </p>
    </div>
  </section>

</div>

<style>
  .apropos-hero { background:linear-gradient(135deg,#0d2718 0%,#1d5f39 100%); padding:72px 24px; text-align:center; }
  .apropos-hero-inner { max-width:820px; margin:0 auto; }
  .apropos-kicker { display:inline-flex; align-items:center; gap:9px; font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#a9e6c4; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18); padding:8px 16px; border-radius:999px; margin-bottom:18px; }
  .apropos-title { font-family:'Montserrat',sans-serif; font-size:clamp(30px,5vw,46px); font-weight:800; color:#fff; line-height:1.15; margin:0 0 16px; }
  .apropos-title-accent { color:#7ed9a4; }
  .apropos-hero-text { font-size:15.5px; line-height:1.8; color:rgba(255,255,255,.82); max-width:640px; margin:0 auto; }

  .apropos-section { padding:64px 24px; background:#f0f1ef; }

  /* ═══ ARD — PRÉSENTATION ═══ */
  .apropos-ard { background:#ffffff; padding:64px 24px; border-top:1px solid var(--border); border-bottom:1px solid var(--border); }
  .apropos-ard-inner { max-width:1100px; margin:0 auto; }
  .apropos-ard-hd { display:grid; grid-template-columns:auto 1fr auto; gap:34px; align-items:center; padding-bottom:38px; border-bottom:1px dashed var(--border2); margin-bottom:38px; }
  .apropos-ard-logo { width:200px; height:auto; object-fit:contain; filter:drop-shadow(0 6px 14px rgba(13,36,22,.12)); }
  .apropos-ard-kicker { display:inline-flex; align-items:center; gap:8px; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--primary); background:var(--primary-lt); padding:7px 14px; border-radius:999px; margin-bottom:12px; }
  .apropos-ard-titre { font-family:'Montserrat',sans-serif; font-size:24px; font-weight:800; color:#1a1a2e; line-height:1.2; margin:0 0 10px; }
  .apropos-ard-accent { color:var(--primary); }
  .apropos-ard-phrase { font-size:14.5px; font-style:italic; color:var(--text-dim); line-height:1.7; margin:0; }
  .apropos-ard-chiffres { display:grid; grid-template-columns:repeat(3,auto); gap:22px; }
  .apropos-ard-chiffre { text-align:center; padding:16px 18px; background:var(--surface2); border:1px solid var(--border); border-radius:14px; min-width:96px; }
  .apropos-ard-chiffre strong { display:block; font-family:'Montserrat',sans-serif; font-size:30px; font-weight:800; color:var(--primary); line-height:1; }
  .apropos-ard-chiffre span { display:block; font-size:11.5px; color:var(--text-muted); margin-top:6px; line-height:1.35; }
  .apropos-ard-bas { display:grid; grid-template-columns:1.1fr 1.4fr; gap:44px; align-items:start; }
  .apropos-ard-desc { font-size:14px; line-height:1.8; color:var(--text-dim); margin:0 0 22px; }
  .apropos-ard-btn { display:inline-flex; align-items:center; gap:9px; padding:12px 24px; font-family:'DM Sans',sans-serif; font-size:14px; font-weight:700; color:#1a1a2e; background:#d8eee2; border-radius:999px; text-decoration:none; box-shadow:0 3px 12px rgba(38,122,71,.22); transition:.2s; }
  .apropos-ard-btn:hover { background:#1d5f39; color:#fff; transform:translateY(-1px); }
  .apropos-ard-coord { display:grid; gap:9px; margin-top:24px; }
  .apropos-ard-coord span { display:flex; align-items:center; gap:10px; font-size:12.5px; color:var(--text); }
  .apropos-ard-coord i { color:var(--primary); width:16px; text-align:center; }
  .apropos-ard-axes-titre { font-family:'Montserrat',sans-serif; font-size:16px; font-weight:700; color:#1a1a2e; margin:0 0 16px; }
  .apropos-ard-axes { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
  .apropos-ard-axe { display:flex; align-items:center; gap:10px; padding:13px 14px; background:var(--surface); border:1px solid var(--border); border-radius:12px; font-size:12.5px; font-weight:600; color:var(--text); transition:.18s; }
  .apropos-ard-axe:hover { border-color:var(--primary); transform:translateY(-2px); box-shadow:var(--shadow); }
  .apropos-ard-axe i { color:var(--primary); font-size:15px; }

  /* ═══ Bande partenaires défilante ═══ */
  .apropos-ard-partenaires { margin-top:36px; }
  .apropos-ard-partenaires-titre { font-family:'Montserrat',sans-serif; font-size:14px; font-weight:700; color:var(--text-muted); text-align:center; margin:0 0 14px; letter-spacing:.5px; text-transform:uppercase; }
  .apropos-ard-partenaires-bande { overflow:hidden; -webkit-mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent); mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent); }
  .apropos-ard-partenaires-track { display:flex; gap:14px; width:max-content; animation:scrollPartenaires 48s linear infinite; }
  .apropos-ard-partenaires-bande:hover .apropos-ard-partenaires-track { animation-play-state:paused; }
  .apropos-ard-part { display:flex; align-items:center; gap:9px; padding:11px 20px; background:#ffffff; border:1px solid var(--border); border-radius:999px; font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; transition:.2s; box-shadow:0 2px 8px rgba(26,45,34,.04); flex-shrink:0; }
  .apropos-ard-part:hover { border-color:var(--primary); box-shadow:var(--shadow); }
  .apropos-ard-part i { color:var(--primary); font-size:14px; }

  @keyframes scrollPartenaires {
    from { transform:translateX(0); }
    to { transform:translateX(calc(-50% - 7px)); }
  }
  .apropos-grid { max-width:1100px; margin:0 auto; display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
  .apropos-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:30px 26px; box-shadow:var(--shadow); transition:.2s; }
  .apropos-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg); }
  .apropos-card-icon { width:52px; height:52px; display:flex; align-items:center; justify-content:center; font-size:24px; background:var(--primary-lt); border-radius:14px; margin-bottom:16px; }
  .apropos-card h2 { font-family:'Montserrat',sans-serif; font-size:19px; font-weight:700; color:#1a1a2e; margin:0 0 10px; }
  .apropos-card p { font-size:13.5px; line-height:1.7; color:var(--text-dim); margin:0; }

  .apropos-valeurs { padding:64px 24px; }
  .apropos-valeurs-inner { max-width:1100px; margin:0 auto; }
  .apropos-valeurs-title { font-family:'Montserrat',sans-serif; font-size:26px; font-weight:800; color:#1a1a2e; text-align:center; margin:0 0 28px; }
  .apropos-valeurs-row { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
  .apropos-valeur { background:var(--surface2); border:1px solid var(--border); border-radius:16px; padding:22px 20px; }
  .apropos-valeur-num { font-family:'Montserrat',sans-serif; font-size:12px; font-weight:800; color:var(--primary); letter-spacing:1px; }
  .apropos-valeur strong { display:block; font-family:'Montserrat',sans-serif; font-size:16px; font-weight:700; color:#1a1a2e; margin:6px 0 6px; }
  .apropos-valeur p { font-size:12.5px; line-height:1.6; color:var(--text-dim); margin:0; }

  /* ═══ MENTIONS LÉGALES ═══ */
  .apropos-mentions { background:#ffffff; padding:64px 24px; }
  .apropos-mentions-inner { max-width:1100px; margin:0 auto; text-align:center; }
  .apropos-mentions-kicker { display:inline-flex; align-items:center; gap:8px; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--primary); background:var(--primary-lt); padding:7px 14px; border-radius:999px; margin-bottom:12px; }
  .apropos-mentions-title { font-family:'Montserrat',sans-serif; font-size:26px; font-weight:800; color:#1a1a2e; margin:0 0 30px; }
  .apropos-mentions-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:22px; text-align:left; }
  .apropos-mention { background:var(--surface2); border:1px solid var(--border); border-radius:16px; padding:26px 24px; box-shadow:var(--shadow); transition:.2s; }
  .apropos-mention:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg); }
  .apropos-mention-icone { width:48px; height:48px; display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--primary); background:var(--primary-lt); border-radius:12px; margin-bottom:14px; }
  .apropos-mention h3 { font-family:'Montserrat',sans-serif; font-size:16px; font-weight:700; color:#1a1a2e; margin:0 0 8px; }
  .apropos-mention p { font-size:13px; line-height:1.7; color:var(--text-dim); margin:0; }
  .apropos-mention strong { color:#1a1a2e; }
  .apropos-mentions-note { display:inline-flex; align-items:center; gap:9px; margin:26px auto 0; padding:11px 18px; background:var(--surface2); border:1px solid var(--border); border-radius:999px; font-size:12.5px; color:var(--text-muted); }
  .apropos-mentions-note i { color:var(--primary); }

  @media (max-width:920px) {
    .apropos-mentions-grid { grid-template-columns:1fr; }
    .apropos-grid { grid-template-columns:repeat(2,1fr); }
    .apropos-valeurs-row { grid-template-columns:repeat(2,1fr); }
    .apropos-ard-hd { grid-template-columns:1fr; justify-items:start; text-align:left; gap:20px; }
    .apropos-ard-logo { width:170px; }
    .apropos-ard-chiffres { grid-template-columns:repeat(3,1fr); width:100%; }
    .apropos-ard-bas { grid-template-columns:1fr; gap:30px; }
  }
  @media (max-width:600px) {
    .apropos-ard { padding:44px 16px; }
    .apropos-ard-axes { grid-template-columns:repeat(2,1fr); }
    .apropos-ard-titre { font-size:20px; }
  }
  @media (max-width:600px) {
    .apropos-grid { grid-template-columns:1fr; }
    .apropos-valeurs-row { grid-template-columns:1fr; }
    .apropos-hero { padding:52px 18px; }
    .apropos-section, .apropos-valeurs, .apropos-mentions { padding:44px 16px; }
  }
</style>
@endsection
