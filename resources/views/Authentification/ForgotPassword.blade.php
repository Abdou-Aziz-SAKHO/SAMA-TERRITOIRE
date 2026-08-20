<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié — SAMA TERRITOIRE</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap');

  :root{
    --ink: #1F3B2C;
    --ink-2: #2C5039;
    --paper: #FFFFFF;
    --paper-2: #F3FAF4;
    --sand: #4C9A6A;
    --sand-light: #8FCBA5;
    --moss: #6E8F72;
    --slate: #2E3440;
    --line: rgba(31,59,44,0.14);
    --error: #C0392B;
  }

  *{ box-sizing: border-box; }
  html,body{ height:100%; }
  body{
    margin:0;
    font-family:'IBM Plex Sans', sans-serif;
    color: var(--slate);
    background: #e9f3eb;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 32px;
  }

  .screen{
    width:100%;
    max-width: 760px;
    display:grid;
    grid-template-columns: 1fr 1.05fr;
    border-radius: 20px;
    overflow:hidden;
    box-shadow: 0 30px 70px -30px rgba(31,59,44,0.28);
    border: 1px solid var(--line);
  }

  /* ---------- LEFT: territory panel ---------- */
  .territory{
    position:relative;
    background:
      radial-gradient(700px 400px at 20% 15%, rgba(143,203,165,0.18), transparent 60%),
      linear-gradient(180deg, var(--ink) 0%, var(--ink-2) 100%);
    color: var(--paper);
    padding: 40px 36px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    overflow:hidden;
  }

  .contours{
    position:absolute;
    inset:0;
    opacity:0.5;
    pointer-events:none;
  }

  .brandmark{
    display:flex;
    align-items:center;
    gap:14px;
    position:relative;
    z-index:2;
  }
  .brandmark .glyph{
    width:38px; height:38px;
    border-radius:50%;
    border:1.5px solid var(--sand-light);
    display:flex; align-items:center; justify-content:center;
    position:relative;
    flex-shrink:0;
  }
  .brandmark .glyph::before{
    content:"";
    width:6px; height:6px;
    background: var(--sand-light);
    border-radius:50%;
  }
  .brandmark .glyph::after{
    content:"";
    position:absolute;
    inset:-9px;
    border:1px solid rgba(143,203,165,0.35);
    border-radius:50%;
  }
  .brandmark span img{
    max-width: 250px;
    height: auto;
  }

  .territory-copy{
    position:relative;
    z-index:2;
    max-width: 280px;
  }
  .territory-copy .eyebrow{
    font-family:'IBM Plex Mono', monospace;
    font-size:11px;
    letter-spacing:0.14em;
    text-transform:uppercase;
    color: var(--sand-light);
    margin:0 0 14px;
  }
  .territory-copy h1{
    font-family:'Fraunces', serif;
    font-optical-sizing:auto;
    font-weight:500;
    font-size: 26px;
    line-height:1.15;
    margin:0 0 14px;
    color: var(--paper);
  }
  .territory-copy p{
    font-size:13.5px;
    line-height:1.55;
    color: rgba(255,255,255,0.68);
    margin:0;
  }

  .coords{
    position:relative;
    z-index:2;
    font-family:'IBM Plex Mono', monospace;
    font-size:11px;
    color: rgba(255,255,255,0.55);
    display:flex;
    justify-content:space-between;
    border-top:1px solid rgba(255,255,255,0.14);
    padding-top:16px;
  }
  .coords .dot{
    display:inline-block;
    width:6px; height:6px;
    border-radius:50%;
    background: var(--sand-light);
    margin-right:8px;
    box-shadow: 0 0 0 3px rgba(143,203,165,0.18);
  }

  /* ---------- RIGHT: form panel ---------- */
  .form-panel{
    background: var(--paper);
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 40px 36px;
  }

  .form-card{
    width:100%;
    max-width: 320px;
  }

  .form-card .kicker{
    font-family:'IBM Plex Mono', monospace;
    font-size:10.5px;
    letter-spacing:0.14em;
    text-transform:uppercase;
    color: var(--sand);
    margin:0 0 8px;
  }

  .form-card h2{
    font-family:'Fraunces', serif;
    font-weight:500;
    font-size:24px;
    margin:0 0 6px;
    color: var(--ink);
  }
  .form-card .sub{
    font-size:13px;
    color:#6B7280;
    margin:0 0 22px;
  }

  /* status / session message */
  .status-msg{
    background: var(--paper-2);
    border:1px solid var(--line);
    color: var(--ink-2);
    font-size:12.5px;
    padding:10px 12px;
    border-radius:8px;
    margin:0 0 18px;
  }

  .field{ margin-bottom:16px; }
  .field label{
    display:block;
    font-size:12.5px;
    font-weight:600;
    color: var(--ink);
    margin-bottom:7px;
  }
  .field-wrap{
    position:relative;
    display:flex;
    align-items:center;
    background: var(--paper-2);
    border:1.5px solid var(--line);
    border-radius:10px;
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  .field-wrap:focus-within{
    border-color: var(--sand);
    box-shadow: 0 0 0 3px rgba(76,154,106,0.14);
  }
  .field-wrap.has-error{
    border-color: var(--error);
  }
  .field-wrap.has-error:focus-within{
    box-shadow: 0 0 0 3px rgba(192,57,43,0.14);
  }
  .field-wrap svg{
    flex-shrink:0;
    margin-left:12px;
    color:#8FA593;
  }
  .field-wrap input{
    width:100%;
    border:none;
    outline:none;
    background:transparent;
    padding:11px 10px;
    font-size:14px;
    font-family:'IBM Plex Sans', sans-serif;
    color: var(--ink);
  }
  .field-wrap input::placeholder{ color:#A9B3AC; }

  .field-error{
    font-size:12px;
    color: var(--error);
    margin:6px 0 0;
  }

  .row-between{
    display:flex;
    justify-content:flex-end;
    margin: -6px 0 22px;
  }
  .row-between a{
    font-size:12.5px;
    color: var(--sand);
    text-decoration:none;
    font-weight:500;
  }
  .row-between a:hover{ text-decoration:underline; }

  .btn-submit{
    width:100%;
    border:none;
    border-radius:10px;
    padding:12px 18px;
    background: var(--ink);
    color: var(--paper);
    font-size:14.5px;
    font-weight:600;
    font-family:'IBM Plex Sans', sans-serif;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    transition: background .15s ease, transform .1s ease;
  }
  .btn-submit:hover{ background: var(--ink-2); }
  .btn-submit:active{ transform: translateY(1px); }
  .btn-submit:focus-visible{ outline:2px solid var(--sand); outline-offset:3px; }

  .foot{
    margin-top:24px;
    font-size:12px;
    color:#9CA8A0;
    display:flex;
    align-items:center;
    gap:8px;
  }
  .foot .sep{ width:4px; height:4px; border-radius:50%; background:#C6D3CA; }

  input, button { font-family: inherit; }

  a, button, input { outline-offset: 2px; }

  @media (max-width: 680px){
    body{ padding: 16px; }
    .screen{ grid-template-columns: 1fr; max-width: 380px; }
    .territory{ padding: 26px 24px; }
    .territory-copy{ max-width:none; }
    .territory-copy h1{ font-size:22px; }
    .territory-copy p{ display:none; }
    .coords{ display:none; }
    .form-panel{ padding: 30px 24px 32px; }
  }

  @media (prefers-reduced-motion: reduce){
    *{ animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; }
  }

</style>
</head>
<body>

<div class="screen">

  <!-- LEFT PANEL -->
  <div class="territory">
    <svg class="contours" viewBox="0 0 500 700" preserveAspectRatio="none" aria-hidden="true">
      <path d="M-20,120 C 80,60 160,180 260,110 S 420,40 520,120" stroke="rgba(143,203,165,0.20)" stroke-width="1" fill="none"/>
      <path d="M-30,220 C 70,160 170,280 270,210 S 430,140 530,230" stroke="rgba(143,203,165,0.15)" stroke-width="1" fill="none"/>
      <path d="M-20,320 C 90,270 150,380 270,320 S 410,250 520,340" stroke="rgba(255,255,255,0.09)" stroke-width="1" fill="none"/>
      <path d="M-30,430 C 80,390 190,480 280,420 S 440,370 530,450" stroke="rgba(255,255,255,0.08)" stroke-width="1" fill="none"/>
      <path d="M-20,540 C 100,500 170,590 280,530 S 420,470 520,560" stroke="rgba(143,203,165,0.14)" stroke-width="1" fill="none"/>
      <path d="M-30,630 C 90,600 190,670 290,620 S 430,570 530,650" stroke="rgba(255,255,255,0.06)" stroke-width="1" fill="none"/>
    </svg>

    <div class="brandmark">
      <span class="logo"><img src="{{ asset('assets/img/TERRITOIRE-login.png') }}" alt="Logo SAMA TERRITOIRE" class="logo-img"></span>
    </div>

    <div class="territory-copy">
      <p class="eyebrow">Espace administrateur</p>
      <h1>Mot de passe oublié ?</h1>
      <p>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
    </div>

    <div class="coords">
      <span><span class="dot"></span>Connexion sécurisée</span>
      <span>Agence Regional de Developement</span>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="form-panel">
    <div class="form-card">
      <p class="kicker">Récupération</p>
      <h2>Réinitialiser le mot de passe</h2>
      <p class="sub">Un lien de réinitialisation sera envoyé à votre adresse email.</p>

      @if ($errors->any())
        <div class="status-msg" style="border-color: var(--error); color: var(--error);">
          <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="status-msg">{{ session('success') }}</div>
      @endif

      <form method="POST" action="{{ route('forgot-password.post') }}">
        @csrf

        {{-- Email --}}
        <div class="field">
          <label for="email">Adresse email</label>
          <div class="field-wrap {{ $errors->has('email') ? 'has-error' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
            <input type="email" id="email" name="email"
                placeholder="admin@email.com" value="{{ old('email') }}" required autofocus>
          </div>
          @error('email')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn-submit">
          Envoyer le lien
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>
      </form>

      {{-- Retour à la connexion --}}
      <div class="row-between" style="margin-top:18px; margin-bottom:0;">
        <a href="{{ route('login') }}">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px; margin-right:4px;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Retour à la connexion
        </a>
      </div>

      <p class="foot">Sama Territoire <span class="sep"></span> Espace Administration</p>
    </div>
  </div>

</div>

</body>
</html>
