{{--
  ═══════════════════════════════════════════════════════════════
  ACTUALITÉS — Mosaïque éditoriale (réutilisable)
  Variables attendues :
    - $actualites      : collection d'actualités (obligatoire)
    - $actusTitre      : titre de section (défaut "Actualités")
    - $actusSousTitre  : sous-titre (optionnel)
    - $actusToutesUrl  : URL du bouton "Voir toutes" (optionnel)
    - $actusPagination : paginator Laravel (optionnel)
  ═══════════════════════════════════════════════════════════════
--}}

@php $actusTitre = $actusTitre ?? 'Actualités'; @endphp

<section class="actus">
    <div class="actus-hd">
        <div>
            <span class="actus-overline">Le territoire en direct</span>
            <h2 class="actus-title">{{ $actusTitre }}</h2>
            @isset($actusSousTitre)
                <p class="actus-sub">{{ $actusSousTitre }}</p>
            @endisset
        </div>
        @if(!empty($actusToutesUrl))
            <a class="actus-more" href="{{ $actusToutesUrl }}">{{ $actusToutesLabel ?? 'Voir toutes les actualités' }} <i class="fa-solid fa-arrow-right"></i></a>
        @endif
    </div>

    @if(session('succes_actualite'))
        <div class="actus-flash">
            <i class="fa-solid fa-circle-check"></i> {{ session('succes_actualite') }}
        </div>
    @endif

    @if($errors->any() && !empty(old('actu_id')))
        <div class="actus-flash actus-flash-erreur">
            <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="actus-grid">
    @forelse($actualites as $index => $actu)
        @php
            $photo   = $actu->photos->first();
            $extrait = Str::limit(strip_tags($actu->contenu), 170);
        @endphp

        <article class="actus-item">
            <div class="actus-media">
                @if($photo)
                    <img src="{{ route('photos.apercu', $photo) }}" alt="{{ $actu->titre }}" loading="lazy">
                @else
                    <div class="actus-media-none"><i class="fa-solid fa-newspaper"></i></div>
                @endif
            </div>

            <div class="actus-body">
                <div class="actus-meta">
                    <time datetime="{{ $actu->date_publication?->toIso8601String() }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        {{ $actu->date_publication ? $actu->date_publication->isoFormat('LL') : '—' }}
                    </time>
                    @if($actu->commentaires_count > 0)
                        <span class="actus-compts"><i class="fa-solid fa-comments"></i> {{ $actu->commentaires_count }}</span>
                    @endif
                </div>

                <h3 class="actus-titre">{{ $actu->titre }}</h3>
                <p class="actus-extrait">{{ $extrait }}</p>

                <div class="actus-full" id="actus-full-{{ $actu->id }}" hidden>
                    <div class="actus-panel-hd">
                        <div class="actus-full-ttl"><i class="fa-solid fa-newspaper"></i> Article complet</div>
                        <button type="button" class="actus-panel-close" data-actu="{{ $actu->id }}" aria-label="Réduire"><i class="fa-solid fa-angles-up"></i> Réduire</button>
                    </div>
                    <div class="actus-full-text">{!! nl2br(e($actu->contenu)) !!}</div>
                    @if($actu->photos->count() > 1)
                        <div class="actus-full-galerie">
                            <div class="actus-full-galerie-hd">
                                <span><i class="fa-solid fa-images"></i> Photos ({{ $actu->photos->count() }})</span>
                                <span class="actus-full-galerie-hint">← glissez pour voir →</span>
                            </div>
                            <div class="actus-full-photos">
                                @foreach($actu->photos->skip(1) as $p)
                                    <img src="{{ route('photos.apercu', $p) }}" alt="{{ $p->nom }}" loading="lazy">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="actus-actions">
                    <button type="button" class="actus-lire" data-actu="{{ $actu->id }}">
                        <span class="actus-lire-texte">Lire la suite</span> <i class="fa-solid fa-angles-down"></i>
                    </button>
                    <button type="button" class="actus-com" data-actu="{{ $actu->id }}">
                        <i class="fa-solid fa-comment-dots"></i> <span class="actus-com-texte">Commenter sur l'actualité</span>
                    </button>
                </div>

                <form class="actus-com-form" id="actus-com-form-{{ $actu->id }}" method="POST"
                      action="{{ route('actualites.commenter', $actu->id) }}">
                    @csrf
                    <input type="hidden" name="actu_id" value="{{ $actu->id }}">
                    <div class="actus-panel-hd">
                        <div class="actus-com-ttl"><i class="fa-solid fa-comment-dots"></i> Laisser un commentaire</div>
                        <div class="actus-panel-actions"><span class="actus-form-badge" aria-hidden="true">✍️</span><button type="button" class="actus-panel-close" data-actu="{{ $actu->id }}" aria-label="Réduire"><i class="fa-solid fa-angles-up"></i> Réduire</button></div>
                    </div>
                    <div class="actus-com-succes" hidden>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Votre commentaire a été ajouté. Il sera visible après modération.</span>
                    </div>
                    <div class="actus-com-corps">
                        <div class="actus-com-row">
                            <input type="text" name="nom" placeholder="Votre nom (optionnel)" maxlength="255">
                            <input type="email" name="email" placeholder="Votre e-mail" maxlength="255" required>
                        </div>
                        <textarea name="message" placeholder="Votre commentaire…" rows="3" maxlength="5000" required></textarea>
                        <button type="submit" class="actus-com-send"><i class="fa-solid fa-paper-plane"></i> Envoyer</button>
                    </div>
                </form>
            </div>
        </article>
    @empty
        <div class="actus-empty">
            <i class="fa-solid fa-newspaper"></i>
            <h3>Aucune actualité récente</h3>
            <p>Aucune actualité n'a été publiée au cours des six derniers mois. Revenez bientôt pour suivre la vie du territoire.</p>
        </div>
    @endforelse
    </div>

    @if(isset($actusPagination) && $actusPagination instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="actus-pagination">{{ $actusPagination->links() }}</div>
    @endif
</section>

<style>
    /* ── Section Actualités ── */
    .actus { max-width:none; margin:0; padding:72px 24px 20px; background:#fdfefe; display:grid; grid-template-columns:repeat(12,1fr); gap:20px; }
    .actus-hd, .actus-flash, .actus-empty, .actus-pagination { grid-column:1 / -1; }
    .actus-hd { max-width:1100px; margin:0 auto 34px; }
    .actus-flash, .actus-empty, .actus-pagination { max-width:1100px; margin-left:auto; margin-right:auto; }
    .actus-grid { grid-column:1 / -1; max-width:1100px; margin:0 auto; display:grid; grid-template-columns:repeat(12,1fr); gap:20px; }
    .actus-hd { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; margin-bottom:34px; text-align:left; }
    .actus-hd > div:first-child { flex:1 1 auto; min-width:0; text-align:left; }
    .actus-overline { display:block; font-size:11px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--primary); margin-bottom:10px; }
    .actus-title { font-family:'Montserrat',sans-serif; font-size:34px; font-weight:800; color:#1a1a2e; line-height:1.1; text-align:left; }
    .actus-sub { font-size:14px; color:var(--text-muted); margin-top:8px; max-width:560px; line-height:1.6; }
    .actus-more { display:inline-flex; align-items:center; gap:8px; margin-left:auto; padding:10px 18px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600; color:#1a1a2e; background:#d8eee2; border:none; border-radius:999px; text-decoration:none; box-shadow:0 3px 12px rgba(38,122,71,.22); transition:.2s; flex-shrink:0; }
    .actus-more:hover { background:#1d5f39; color:#fff; transform:translateY(-1px); }
    .actus-more i { transition:transform .2s; }
    .actus-more:hover i { transform:translateX(3px); }

    .actus-flash { display:flex; align-items:center; gap:10px; padding:13px 16px; border-radius:12px; font-size:13px; margin-bottom:22px; background:#e9f7ee; border:1px solid #b5dfc6; color:var(--primary); font-weight:600; }
    .actus-flash-erreur { background:#fdecea; border-color:#f5c6c2; color:#b3261e; }

    /* Grille compacte : 4 cartes par ligne */
    .actus-grid { position:relative; }
    .actus-item {
        grid-column: span 3; display:block; background:var(--surface);
        border:1px solid var(--border); border-radius:16px; overflow:visible;
        box-shadow:var(--shadow); position:relative;
        transition:box-shadow .3s ease, border-color .3s ease;
    }
    .actus-item.etendu { border-color:var(--border2); box-shadow:var(--shadow-lg); z-index:40; }

    .actus-media { height:150px; background:var(--surface3); overflow:hidden; border-radius:16px 16px 0 0; }
    .actus-media img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s ease; }
    .actus-item:hover .actus-media img { transform:scale(1.05); }
    .actus-media-none { width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, var(--primary-lt), var(--surface3)); color:rgba(38,122,71,.55); font-size:40px; }

    .actus-body { padding:16px 18px 18px; }
    .actus-meta { display:flex; align-items:center; gap:12px; flex-wrap:wrap; font-size:11px; color:var(--text-muted); margin-bottom:8px; }
    .actus-meta time { display:inline-flex; align-items:center; gap:6px; }
    .actus-compts { display:inline-flex; align-items:center; gap:6px; }
    .actus-titre { font-family:'Montserrat',sans-serif; font-size:16px; font-weight:700; color:#1a1a2e; line-height:1.3; margin-bottom:8px; }
    .actus-extrait { font-size:12.5px; color:var(--text-dim); line-height:1.6; margin-bottom:14px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

    .actus-actions { display:flex; gap:8px; flex-wrap:wrap; }
    .actus-lire { display:inline-flex; align-items:center; gap:6px; padding:7px 12px; font-family:'DM Sans',sans-serif; font-size:11.5px; font-weight:600; color:#1a1a2e; background:var(--surface2); border:1px solid var(--border); border-radius:999px; cursor:pointer; transition:.2s; }
    .actus-lire:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
    .actus-com { display:inline-flex; align-items:center; gap:6px; padding:7px 12px; font-family:'DM Sans',sans-serif; font-size:11.5px; font-weight:600; color:#1a1a2e; background:var(--surface2); border:1px solid var(--border); border-radius:999px; cursor:pointer; transition:.2s; }
    .actus-com:hover { background:var(--primary); color:#fff; border-color:var(--primary); }

    .actus-full { display:none; }
    .actus-full-ttl { display:flex; align-items:center; gap:8px; font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:var(--primary); margin-bottom:10px; }
    .actus-full-text { font-size:13px; color:var(--text-dim); line-height:1.75; white-space:pre-line; }
    .actus-full-galerie { margin-top:16px; }
    .actus-full-galerie-hd { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:10px; }
    .actus-full-galerie-hd > span:first-child { font-family:'Montserrat',sans-serif; font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--text-dim); display:inline-flex; align-items:center; gap:7px; }
    .actus-full-galerie-hd > span:first-child i { color:var(--primary); }
    .actus-full-galerie-hint { font-size:10.5px; color:var(--text-muted); font-style:italic; white-space:nowrap; }
    .actus-full-photos { display:flex; gap:10px; margin-top:0; overflow-x:auto; -webkit-overflow-scrolling:touch; scroll-snap-type:x mandatory; scroll-padding:2px; padding:2px 2px 8px; scrollbar-width:none; }
    .actus-full-photos::-webkit-scrollbar { height:0; background:transparent; }
    .actus-full-photos img { min-width:190px; height:128px; object-fit:cover; border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); flex-shrink:0; scroll-snap-align:start; transition:transform .2s ease, box-shadow .2s ease; }
    .actus-full-photos img:hover { transform:scale(1.03); box-shadow:var(--shadow-lg); }

    /* État étendu : panneau flottant côte à côte — carte à gauche, contenu à droite */
    .actus-item.etendu .actus-full,
    .actus-item.etendu .actus-com-form.ouvert {
        position:absolute; top:0; bottom:0; left:100%; width:200%;
        text-align:left; background:#ffffff;
        border:1px solid var(--border); border-radius:14px;
        box-shadow:0 18px 50px rgba(26,42,32,.18), 0 4px 14px rgba(26,42,32,.08);
        padding:20px 22px; overflow:auto; z-index:2;
    }
    .actus-item.etendu .actus-full { display:block; }
    .actus-item.etendu .actus-com-form.ouvert { display:grid; align-content:start; }
    .actus-item.etendu .actus-full-text { color:var(--text); font-size:13.5px; }
    .actus-item.etendu .actus-full-ttl,
    .actus-item.etendu .actus-com-ttl { margin-bottom:12px; }

    /* Carte en début de ligne : panneau à droite ; carte 3e et 4e : panneau à gauche */
    .actus-item.etendu { grid-column: span 3; }
    .actus-item:nth-child(4n + 3).etendu .actus-full,
    .actus-item:nth-child(4n + 3).etendu .actus-com-form.ouvert,
    .actus-item:nth-child(4n + 4).etendu .actus-full,
    .actus-item:nth-child(4n + 4).etendu .actus-com-form.ouvert {
        left:auto; right:100%; margin-left:0;
    }

    .actus-com-form { max-height:0; overflow:hidden; opacity:0; transform:translateY(-4px); transition:max-height .35s ease, opacity .3s ease, transform .3s ease; margin-top:0; padding:0 16px; background:var(--surface2); border:1px solid transparent; border-radius:18px; display:grid; gap:10px; box-sizing:border-box; position:relative; }
    .actus-com-form.ouvert { max-height:340px; opacity:1; transform:translateY(0); margin-top:12px; padding:18px 18px 16px; border-color:var(--border2); background-color:var(--surface2); background-image:radial-gradient(rgba(38,122,71,.07) 1px, transparent 1px); background-size:16px 16px; border-radius:20px; box-shadow:0 8px 24px rgba(26,42,32,.06); }
    .actus-item.etendu .actus-com-form.ouvert { max-height:none; margin-top:14px; overflow:visible; }
    .actus-com-ttl { display:flex; align-items:center; gap:8px; font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:var(--primary); margin-bottom:2px; }
    .actus-panel-actions { display:inline-flex; align-items:center; gap:8px; }
    .actus-form-badge { width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#d8eee2,#1d5f39); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; box-shadow:0 4px 12px rgba(29,95,57,.3); flex-shrink:0; }
    .actus-com-form input[type="text"], .actus-com-form input[type="email"], .actus-com-form textarea {
        width:100%; padding:10px 12px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text);
        background:#fff; border:1px solid var(--border); border-radius:8px; outline:none; box-sizing:border-box; transition:border-color .2s, box-shadow .2s;
    }
    .actus-com-form input:focus, .actus-com-form textarea:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(38,122,71,.12); }
    .actus-com-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .actus-com-send { justify-self:end; display:inline-flex; align-items:center; gap:7px; padding:9px 18px; font-family:'DM Sans',sans-serif; font-size:12.5px; font-weight:600; color:#1a1a2e; background:#d8eee2; border:none; border-radius:999px; cursor:pointer; transition:.2s; }
    .actus-com-send:hover { background:#1d5f39; color:#fff; }
    .actus-com-send:disabled { opacity:.6; cursor:wait; }

    /* Message de succès affiché dans le panneau après envoi AJAX */
    .actus-com-succes[hidden] { display:none; }
    .actus-com-succes {
        display:flex; align-items:center; gap:10px; padding:14px 15px; margin-bottom:4px;
        background:#e9f7ee; border:1px solid #b5dfc6; border-radius:12px;
        color:var(--primary); font-size:13px; font-weight:600; line-height:1.5;
    }
    .actus-com-succes i { flex-shrink:0; }

    /* En-tête de panneau : titre + bouton Réduire */
    .actus-panel-hd { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:12px; }
    .actus-panel-hd .actus-full-ttl,
    .actus-panel-hd .actus-com-ttl { margin-bottom:0; }
    .actus-panel-close { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; font-family:'DM Sans',sans-serif; font-size:11.5px; font-weight:600; color:#1a1a2e; background:var(--surface2); border:1px solid var(--border); border-radius:999px; cursor:pointer; transition:.2s; }
    .actus-panel-close:hover { background:#1d5f39; color:#fff; border-color:#1d5f39; }
    .actus-panel-close i { transition:transform .2s; }
    .actus-panel-close:hover i { transform:translateY(-2px); }

    .actus-empty { grid-column:1 / -1; text-align:center; padding:64px 24px; background:var(--surface); border:1px dashed var(--border2); border-radius:18px; }
    .actus-empty i { font-size:40px; color:var(--border2); margin-bottom:14px; }
    .actus-empty h3 { font-family:'Montserrat',sans-serif; font-size:20px; font-weight:700; color:var(--text); margin-bottom:8px; }
    .actus-empty p { font-size:13px; color:var(--text-muted); max-width:440px; margin:0 auto; line-height:1.6; }

    .actus-pagination { grid-column: span 12; display:flex; justify-content:center; margin-top:8px; }

    /* Sur tablette & smartphone : le panneau est centré à l'écran (fixe, z-index élevé) */
    @media (max-width:900px) {
        .actus-item { grid-column: span 6; }
        .actus-media { height:170px; }
        .actus-item.etendu { z-index:120; }
        .actus-item.etendu .actus-full,
        .actus-item.etendu .actus-com-form.ouvert {
            position:fixed; top:50%; left:50%; transform:translate(-50%, -50%);
            width:min(92vw, 620px); margin:0; max-width:none; max-height:84vh;
            display:block;
        }
        .actus-item.etendu .actus-com-form.ouvert { display:grid; align-content:start; }
        .actus-item:nth-child(4n + 3).etendu .actus-full,
        .actus-item:nth-child(4n + 3).etendu .actus-com-form.ouvert,
        .actus-item:nth-child(4n + 4).etendu .actus-full,
        .actus-item:nth-child(4n + 4).etendu .actus-com-form.ouvert { left:50%; right:auto; }
    }
    @media (max-width:700px) {
        .actus-item { grid-column:1 / -1; }
        .actus-media { height:170px; }
        .actus-title { font-size:27px; }
        .actus-hd { align-items:flex-end; }
        .actus-com-row { grid-template-columns:1fr; }
        .actus { padding:48px 16px 12px; }
    }
</style>

<script>
    /* Bouton Réduire intégré au panneau : referme la carte étendue */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.actus-panel-close');
        if (!btn) return;
        var item = btn.closest('.actus-item');
        if (!item) return;
        var full = item.querySelector('.actus-full');
        var form = item.querySelector('.actus-com-form');
        var btnLire = item.querySelector('.actus-lire');
        var btnCom = item.querySelector('.actus-com');

        if (full) full.setAttribute('hidden', '');
        if (form) form.classList.remove('ouvert');
        item.classList.remove('etendu');

        if (btnLire) {
            var sl = btnLire.querySelector('.actus-lire-texte');
            var il = btnLire.querySelector('i');
            if (sl) sl.textContent = 'Lire la suite';
            if (il) il.className = 'fa-solid fa-angles-down';
        }
        if (btnCom) {
            var sc = btnCom.querySelector('.actus-com-texte');
            var ic = btnCom.querySelector('i');
            if (sc) sc.textContent = 'Commenter sur l\'actualité';
            if (ic) ic.className = 'fa-solid fa-comment-dots';
        }
        return;
    });

    /* Fermeture du panneau quand on tape en dehors de la carte étendue (sauf sur les boutons) */
    document.addEventListener('click', function (e) {
        var ouvert = document.querySelector('.actus-item.etendu');
        if (!ouvert) return;
        if (ouvert.contains(e.target)) return;

        var full = ouvert.querySelector('.actus-full');
        var form = ouvert.querySelector('.actus-com-form');
        var btnLire = ouvert.querySelector('.actus-lire');
        var btnCom = ouvert.querySelector('.actus-com');

        if (full) full.setAttribute('hidden', '');
        if (form) form.classList.remove('ouvert');
        ouvert.classList.remove('etendu');

        if (btnLire) {
            var sl = btnLire.querySelector('.actus-lire-texte');
            var il = btnLire.querySelector('i');
            if (sl) sl.textContent = 'Lire la suite';
            if (il) il.className = 'fa-solid fa-angles-down';
        }
        if (btnCom) {
            var sc = btnCom.querySelector('.actus-com-texte');
            var ic = btnCom.querySelector('i');
            if (sc) sc.textContent = 'Commenter';
            if (ic) ic.className = 'fa-solid fa-comment-dots';
        }
        return;
    });

    document.addEventListener('click', function (e) {
        var cible = e.target.closest('.actus-lire, .actus-com');
        if (!cible) return;

        var id = cible.getAttribute('data-actu');
        if (!id) return;
        var item = cible.closest('.actus-item');

        if (cible.classList.contains('actus-lire')) {
            e.preventDefault();
            var full = document.getElementById('actus-full-' + id);
            var span = cible.querySelector('.actus-lire-texte');
            var icone = cible.querySelector('i');
            var form = document.getElementById('actus-com-form-' + id);
            var btnCom = item ? item.querySelector('.actus-com') : null;
            if (!full || !span || !item) return;

            var ouvert = !full.hasAttribute('hidden');
            if (!ouvert) {
                /* ouvre la lecture → on réduit le formulaire de commentaire s'il était ouvert */
                if (form && form.classList.contains('ouvert')) {
                    form.classList.remove('ouvert');
                    if (btnCom) {
                        var s2 = btnCom.querySelector('.actus-com-texte');
                        var i2 = btnCom.querySelector('i');
                        if (s2) s2.textContent = 'Commenter';
                        if (i2) i2.className = 'fa-solid fa-comment-dots';
                    }
                }
            }
            full.toggleAttribute('hidden');
            item.classList.toggle('etendu', !ouvert);
            span.textContent = ouvert ? 'Lire la suite' : 'Réduire';
            icone.className = ouvert ? 'fa-solid fa-angles-down' : 'fa-solid fa-angles-up';
            return;
        }

        if (cible.classList.contains('actus-com')) {
            e.preventDefault();
            var form = document.getElementById('actus-com-form-' + id);
            var span = cible.querySelector('.actus-com-texte');
            var icone = cible.querySelector('i');
            var full = document.getElementById('actus-full-' + id);
            var btnLire = item ? item.querySelector('.actus-lire') : null;
            if (!form) return;

            var ouvr = form.classList.toggle('ouvert');
            if (ouvr && full && !full.hasAttribute('hidden')) {
                /* ouvre le commentaire → on réduit la lecture ouverte */
                full.setAttribute('hidden', '');
                if (btnLire) {
                    var s1 = btnLire.querySelector('.actus-lire-texte');
                    var i1 = btnLire.querySelector('i');
                    if (s1) s1.textContent = 'Lire la suite';
                    if (i1) i1.className = 'fa-solid fa-angles-down';
                }
            }
            item.classList.toggle('etendu', ouvr);
            if (span) span.textContent = ouvr ? 'Réduire' : 'Commenter';
            if (icone) icone.className = ouvr ? 'fa-solid fa-angles-up' : 'fa-solid fa-comment-dots';
        }
    });

    /* Soumission AJAX : le commentaire s'envoie sans recharger l'écran */
    document.addEventListener('submit', function (e) {
        var form = e.target.closest('.actus-com-form');
        if (!form) return;
        e.preventDefault();

        var bouton = form.querySelector('.actus-com-send');
        var succes = form.querySelector('.actus-com-succes');
        var corps = form.querySelector('.actus-com-corps');
        if (!bouton || !succes) return;

        bouton.disabled = true;

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
                if (corps) corps.hidden = true;
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
