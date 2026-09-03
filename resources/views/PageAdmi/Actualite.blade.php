@extends('AppAdmi')
@section('content')

{{--
  ══════════════════════════════════════════════════════════════
  PAGE GESTION DES ACTUALITÉS — Admin
  Layout : sidebar gauche (filtres) + zone principale (tableau)
  ══════════════════════════════════════════════════════════════
--}}

<div style="display:flex; height:calc(100vh - var(--hdr)); margin-top:var(--hdr);">

    {{-- ═══ SIDEBAR GAUCHE ═══ --}}
    <aside class="app-sidebar" style="background:var(--white); border-right:1px solid var(--border); padding:20px 16px; display:flex; flex-direction:column; gap:6px; overflow-y:auto;">

        {{-- Titre sidebar --}}
        <div class="sidebar-title" style="font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:var(--text); padding:0 8px 12px; border-bottom:1px solid var(--border);">
            <span><i class="fa-solid fa-newspaper" style="margin-right:8px; color:var(--primary);"></i> Actualités</span>
        </div>

        {{-- Nombre total de commentaires en attente --}}
        @if($totalEnAttente > 0)
            <div class="sidebar-badge-attente" style="display:flex; align-items:center; gap:8px; padding:9px 11px; background:#fdecea; border:1px solid #f5c6c2; border-radius:9px; font-size:12px; color:#b3261e; font-weight:600; margin-top:6px;">
                <i class="fa-solid fa-comment-dots"></i>
                {{ $totalEnAttente }} commentaire(s) en attente
            </div>
        @endif

        {{-- ═══ Zone filtres ═══ --}}
        <div class="sidebar-filters" style="margin-top:6px; padding-top:12px; border-top:1px solid var(--border);">
            <div style="font-size:11px; color:var(--text-muted); padding:0 0 10px; text-transform:uppercase; letter-spacing:.5px; font-weight:600;">
                <i class="fa-solid fa-filter" style="margin-right:4px;"></i> Filtres
            </div>

            {{-- Filtre Région --}}
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Région</label>
                <form method="GET" action="{{ route('ActualitesAdmi') }}">
                    @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                    @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                    @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                    <select name="region_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">Toutes les régions</option>
                        @foreach($allRegions as $r)
                            <option value="{{ $r->id }}" {{ $regionId == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Filtre Département --}}
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Département</label>
                <form method="GET" action="{{ route('ActualitesAdmi') }}">
                    @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                    @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                    @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                    <select name="departement_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">Tous les départements</option>
                        @php
                            $filteredDepts = $regionId
                                ? $allDepartements->where('region_id', $regionId)
                                : $allDepartements;
                        @endphp
                        @foreach($filteredDepts as $d)
                            <option value="{{ $d->id }}" {{ $departementId == $d->id ? 'selected' : '' }}>{{ $d->nom }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Filtre Commune --}}
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Commune</label>
                <form method="GET" action="{{ route('ActualitesAdmi') }}">
                    @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                    @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                    @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                    <select name="commune_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">Toutes les communes</option>
                        @php
                            $filteredCommunes = $departementId
                                ? $allCommunes->where('departement_id', $departementId)
                                : ($regionId
                                    ? $allCommunes->filter(fn ($c) => in_array($c->departement_id, $allDepartements->where('region_id', $regionId)->pluck('id')->all()))
                                    : $allCommunes);
                        @endphp
                        @foreach($filteredCommunes as $c)
                            <option value="{{ $c->id }}" {{ $communeId == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Filtre Localité --}}
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Localité</label>
                <form method="GET" action="{{ route('ActualitesAdmi') }}">
                    @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                    @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                    @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                    <select name="localite_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">Toutes les localités</option>
                        @php
                            $filteredLocalites = $communeId
                                ? $allLocalites->where('commune_id', $communeId)
                                : ($departementId
                                    ? $allLocalites->filter(fn ($l) => in_array($l->commune_id, $allCommunes->where('departement_id', $departementId)->pluck('id')->all()))
                                    : ($regionId
                                        ? $allLocalites->filter(fn ($l) => in_array($l->commune_id, $allCommunes->filter(fn ($c) => in_array($c->departement_id, $allDepartements->where('region_id', $regionId)->pluck('id')->all()))->pluck('id')->all()))
                                        : $allLocalites));
                        @endphp
                        @foreach($filteredLocalites as $l)
                            <option value="{{ $l->id }}" {{ $localiteId == $l->id ? 'selected' : '' }}>{{ $l->nom }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Filtre Infrastructure --}}
            <div style="margin-bottom:10px;">
                <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Infrastructure</label>
                <form method="GET" action="{{ route('ActualitesAdmi') }}">
                    @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                    @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                    @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                    @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                    <select name="infrastructure_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">Toutes les infrastructures</option>
                        @foreach($filteredInfrastructures as $inf)
                            <option value="{{ $inf->id }}" {{ $infrastructureId == $inf->id ? 'selected' : '' }}>{{ $inf->nom }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Bouton réinitialiser --}}
            @if($regionId || $departementId || $communeId || $localiteId || $infrastructureId)
                <a href="{{ route('ActualitesAdmi') }}" style="display:block; text-align:center; padding:7px; font-size:12px; color:var(--text-dim); border:1px solid var(--border); border-radius:7px; text-decoration:none; margin-top:4px;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                    <i class="fa-solid fa-xmark" style="margin-right:4px;"></i> Effacer les filtres
                </a>
            @endif
        </div>

        <div class="sidebar-user" style="font-size:11px; color:var(--text-muted); padding:10px 8px 0; border-top:1px solid var(--border);">
            @if($connecte)
                Connecté : <strong style="color:var(--text);">{{ $connecte->prenom }} {{ $connecte->nom }}</strong>
                <br><span style="display:inline-block; margin-top:4px; padding:2px 8px; border-radius:6px; background:{{ $connecte->role === 'SUPER_ADMINISTRATEUR' ? '#eef4ff' : '#eefaf1' }}; color:{{ $connecte->role === 'SUPER_ADMINISTRATEUR' ? '#1d4ed8' : '#1d8a4e' }}; font-size:11px; font-weight:600;">{{ $connecte->role === 'SUPER_ADMINISTRATEUR' ? 'Super Admin' : 'Admin' }}</span>
            @else
                Non connecté (lecture seule)
            @endif
        </div>
    </aside>

    {{-- ═══ ZONE PRINCIPALE ═══ --}}
    <main style="flex:1; overflow-y:auto; padding:24px 28px;">

        {{-- Bandeau d'erreurs --}}
        @if($errors->any() || session('error'))
            <div style="background:#fdecea; border:1px solid #f5c6c2; border-radius:10px; padding:14px 18px; margin-bottom:20px;">
                <ul style="margin:0; padding-left:18px; color:#b3261e; font-size:13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @if(session('error'))<li>{{ session('error') }}</li>@endif
                </ul>
            </div>
        @endif

        {{-- ═══ EN-TÊTE + BOUTON AJOUTER ═══ --}}
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Actualités</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $actualites->total() }} actu(s) au total</div>
            </div>
            <button onclick="openFormModal('modal-actu')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                <i class="fa-solid fa-plus"></i> Ajouter
            </button>
        </div>

        {{-- ═══ TABLEAU ═══ --}}
        <div style="background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:var(--surface2); text-align:left;">
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Titre</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Date</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600;">Territoire</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600; text-align:center;">Photos</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600; text-align:center;">Commentaires</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-dim); font-weight:600; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actualites as $a)
                        @php
                            $territoire = $a->localite?->nom
                                ?? $a->commune?->nom
                                ?? $a->departement?->nom
                                ?? $a->region?->nom
                                ?? '—';
                            $niveau = $a->localite_id ? 'Localité'
                                : ($a->commune_id ? 'Commune'
                                : ($a->departement_id ? 'Département'
                                : ($a->region_id ? 'Région' : '—')));
                            $extrait = Str::limit(strip_tags($a->contenu), 80);
                            $nbEnAttente = $a->commentaires_en_attente;
                            // Lieu de l'infrastructure : sa commune (ou à défaut son département)
                            $infraLieu = $a->infrastructure?->commune?->nom
                                ?? $a->infrastructure?->departement?->nom
                                ?? null;
                            // Valeurs pour la modal Modifier (rattachement territorial profond + photos)
                            $editVals = json_encode([
                                'id'                  => $a->id,
                                'titre'               => $a->titre,
                                'contenu'             => $a->contenu,
                                'date_publication'    => $a->date_publication?->format('Y-m-d\TH:i'),
                                'region_id'           => $a->region_id,
                                'departement_id'      => $a->departement_id,
                                'commune_id'          => $a->commune_id,
                                'localite_id'         => $a->localite_id,
                                'infrastructure_id'   => $a->infrastructure_id,
                                'photos'              => $a->photos->map(fn ($p) => ['id' => $p->id, 'src' => route('photos.apercu', $p), 'nom' => $p->nom])->values()->all(),
                            ]);
                        @endphp
                        <tr style="border-bottom:1px solid var(--border2);">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:500; color:var(--text);">{{ $a->titre }}</div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $extrait }}</div>
                            </td>
                            <td style="padding:12px 16px; color:var(--text-dim); white-space:nowrap;">{{ $a->date_publication ? $a->date_publication->format('d/m/Y H:i') : '—' }}</td>
                            <td style="padding:12px 16px; color:var(--text-dim);">
                                @if($niveau !== '—')
                                    <span style="font-size:10px; color:var(--text-muted); text-transform:uppercase;">{{ $niveau }}</span><br>{{ $territoire }}
                                @endif
                                @if($a->infrastructure)
                                    <br><span style="font-size:11px; color:var(--primary);"><i class="fa-solid fa-landmark" style="font-size:10px;"></i> {{ $a->infrastructure->nom }}@if($infraLieu) <span style="color:var(--text-dim);">— {{ $infraLieu }}</span>@endif</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px; text-align:center; color:var(--text-dim);">
                                {{ $a->photos_count }}
                            </td>
                            <td style="padding:12px 16px; text-align:center;">
                                <span style="color:var(--text-dim);">{{ $a->commentaires_total }}</span>
                                @if($nbEnAttente > 0)
                                    <span style="display:inline-block; background:#c0392b; color:#fff; font-size:10px; font-weight:700; padding:1px 6px; border-radius:10px; margin-left:4px;">{{ $nbEnAttente }}</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px; text-align:center; white-space:nowrap;">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                <button type="button" class="btn-voir-actu" data-id="{{ $a->id }}"
                                    data-titre="{{ $a->titre }}"
                                    data-contenu="{{ $a->contenu }}"
                                    data-date="{{ $a->date_publication ? $a->date_publication->format('d/m/Y H:i') : '' }}"
                                    data-territoire="{{ $territoire }}"
                                    data-niveau="{{ $niveau }}"
                                    data-infrastructure="{{ $a->infrastructure?->nom ?? '' }}"
                                    data-photos="{{ json_encode($a->photos->map(fn ($p) => ['src' => route('photos.apercu', $p), 'nom' => $p->nom])->values()->all()) }}"
                                    data-commentaires="{{ json_encode($a->commentaires->map(fn ($c) => ['id' => $c->id, 'nom' => $c->nom, 'email' => $c->email, 'message' => $c->message, 'date' => $c->created_at?->format('d/m/Y H:i'), 'statut' => $c->statut])->values()->all()) }}"
                                    title="Consulter"
                                    style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:13px;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button type="button" class="btn-edit-actu" data-id="{{ $a->id }}"
                                    data-values="{{ $editVals }}"
                                    title="Modifier"
                                    style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--primary); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:13px;">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-del-actu" data-id="{{ $a->id }}"
                                    data-titre="{{ $a->titre }}"
                                    title="Supprimer"
                                    style="width:32px; height:32px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--red); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:13px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="padding:32px; text-align:center; color:var(--text-muted); font-size:13px;">Aucune actualité trouvée</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">{{ $actualites->withQueryString()->links() }}</div>

    </main>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — AJOUTER UNE ACTUALITÉ
     ══════════════════════════════════════════════════════════════ --}}
<div id="modal-actu" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-actu')">
    <div class="modal-card" style="max-width:740px;">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-newspaper" style="color:var(--primary); margin-right:8px;"></i> Ajouter une Actualité</h3>
            <button class="modal-close" onclick="closeFormModal('modal-actu')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('actualites.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="field-row">
                    <div class="field-group"><label>Titre *</label><input type="text" name="titre" required maxlength="255" placeholder="Ex : Inauguration centre de santé"></div>
                    <div class="field-group">
                        <label>Date de publication</label>
                        <input type="datetime-local" name="date_publication">
                    </div>
                </div>

                <div class="field-group"><label>Contenu *</label><textarea name="contenu" rows="5" required placeholder="Texte de l'actualité…"></textarea></div>

                {{-- Photos --}}
                <div class="field-group">
                    <label>Photos <span style="font-weight:400; color:var(--text-muted);">(facultatif — images JPG/PNG/GIF/WebP, 5 Mo max chacune)</span></label>
                    <input type="file" name="photos[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">10 images maximum.</div>
                </div>

                {{-- ═══ Rattachement territorial ═══ --}}
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin:16px 0 10px;">
                    <i class="fa-solid fa-link" style="margin-right:4px;"></i> Rattachement territorial <span style="font-weight:400; text-transform:none;">(facultatif — s'arrêter au niveau voulu)</span>
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label>Région</label>
                        <select name="region_id" id="actu-region" onchange="majDepartementsActu()">
                            <option value="">— Aucune région —</option>
                            @foreach($allRegions as $rg)
                                <option value="{{ $rg->id }}">{{ $rg->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Département</label>
                        <select name="departement_id" id="actu-departement" onchange="choisirDepartementActu()">
                            <option value="">— Aucun département —</option>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label>Commune</label>
                        <select name="commune_id" id="actu-commune" onchange="choisirCommuneActu()">
                            <option value="">— Aucune commune —</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Localité</label>
                        <select name="localite_id" id="actu-localite">
                            <option value="">— Aucune localité —</option>
                        </select>
                    </div>
                </div>

                {{-- Infrastructure liée --}}
                <div class="field-group">
                    <label>Infrastructure liée <span style="font-weight:400; color:var(--text-muted);">(facultatif)</span></label>
                    <select name="infrastructure_id" style="width:100%; padding:9px 12px; font-size:13px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">— Aucune infrastructure —</option>
                        @foreach($allInfrastructures as $inf)
                            <option value="{{ $inf->id }}">{{ $inf->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-actu')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — CONSULTER UNE ACTUALITÉ (contenu + photos + commentaires)
     ══════════════════════════════════════════════════════════════ --}}
<div id="modal-voir-actu" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-voir-actu')">
    <div class="modal-card" style="max-width:800px;">
        <div class="modal-card-header">
            <h3 id="voir-actu-titre">Consulter l'actualité</h3>
            <button class="modal-close" onclick="closeFormModal('modal-voir-actu')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">

            {{-- Métadonnées --}}
            <div id="voir-actu-meta" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(190px,1fr)); gap:10px 20px; margin-bottom:16px;"></div>

            {{-- Contenu --}}
            <div style="margin-bottom:20px;">
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px;">Contenu</div>
                <div id="voir-actu-contenu" style="font-size:14px; color:var(--text); line-height:1.7; background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:16px;"></div>
            </div>

            {{-- Photos --}}
            <div id="voir-actu-photos-section" style="display:none; margin-bottom:20px;">
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Photos</div>
                <div id="voir-actu-photos" style="display:flex; flex-wrap:wrap; gap:10px;"></div>
            </div>

            {{-- Commentaires --}}
            <div style="border-top:1px solid var(--border); padding-top:16px;">
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:10px;">
                    <i class="fa-solid fa-comments" style="margin-right:4px;"></i> Commentaires <span id="voir-actu-nb-commentaires"></span>
                </div>
                <div id="voir-actu-commentaires"></div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" class="btn-cancel" onclick="closeFormModal('modal-voir-actu')">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL — MODIFIER UNE ACTUALITÉ ═══ --}}
<div id="modal-edit-actu" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-edit-actu')">
    <div class="modal-card" style="max-width:740px;">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-pen" style="color:var(--primary); margin-right:8px;"></i> Modifier l'actualité</h3>
            <button class="modal-close" onclick="closeFormModal('modal-edit-actu')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form id="edit-actu-form" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="field-row">
                    <div class="field-group"><label>Titre *</label><input type="text" name="titre" id="ed-actu-titre" required maxlength="255"></div>
                    <div class="field-group">
                        <label>Date de publication</label>
                        <input type="datetime-local" name="date_publication" id="ed-actu-date">
                    </div>
                </div>

                <div class="field-group"><label>Contenu *</label><textarea name="contenu" id="ed-actu-contenu" rows="5" required></textarea></div>

                {{-- Photos : vignettes existantes (cocher pour supprimer) + ajout --}}
                <div class="field-group" id="ed-actu-photos-groupe">
                    <label>Photos <span style="font-weight:400; color:var(--text-muted);">(cochez pour supprimer une photo existante)</span></label>
                    <div id="ed-actu-photos-existantes"></div>
                    <input type="file" name="photos[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Nouvelles photos : JPG/PNG/GIF/WebP, 5 Mo max chacune — 10 images maximum au total.</div>
                </div>

                {{-- ═══ Rattachement territorial ═══ --}}
                <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin:16px 0 10px;">
                    <i class="fa-solid fa-link" style="margin-right:4px;"></i> Rattachement territorial <span style="font-weight:400; text-transform:none;">(facultatif — s'arrêter au niveau voulu)</span>
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label>Région</label>
                        <select name="region_id" id="ed-actu-region" onchange="majDepartementsActuEd()">
                            <option value="">— Aucune région —</option>
                            @foreach($allRegions as $rg)
                                <option value="{{ $rg->id }}">{{ $rg->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Département</label>
                        <select name="departement_id" id="ed-actu-departement" onchange="choisirDepartementActuEd()">
                            <option value="">— Aucun département —</option>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label>Commune</label>
                        <select name="commune_id" id="ed-actu-commune" onchange="choisirCommuneActuEd()">
                            <option value="">— Aucune commune —</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Localité</label>
                        <select name="localite_id" id="ed-actu-localite">
                            <option value="">— Aucune localité —</option>
                        </select>
                    </div>
                </div>

                {{-- Infrastructure liée --}}
                <div class="field-group">
                    <label>Infrastructure liée <span style="font-weight:400; color:var(--text-muted);">(facultatif)</span></label>
                    <select name="infrastructure_id" id="ed-actu-infrastructure" style="width:100%; padding:9px 12px; font-size:13px; border:1px solid var(--border); border-radius:8px; background:var(--surface2); color:var(--text); cursor:pointer;">
                        <option value="">— Aucune infrastructure —</option>
                        @foreach($allInfrastructures as $inf)
                            <option value="{{ $inf->id }}">{{ $inf->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-edit-actu')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL — CONFIRMATION DE SUPPRESSION ═══ --}}
<div id="modal-suppr-actu" class="modal-overlay" onclick="if(event.target===this) fermerModalSuppressionActu()">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3>Confirmer la suppression</h3>
            <button type="button" class="modal-close" onclick="fermerModalSuppressionActu()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <p id="del-actu-intro" style="margin:0 0 14px 0; font-size:15px;"></p>
            <ul id="del-actu-liste" style="margin:0; padding-left:20px; line-height:2;"></ul>
            <p id="del-actu-note" style="display:none; margin-top:12px; padding:10px 12px; background:#fff8e1; border-left:4px solid #f0a500; border-radius:4px; font-size:13px;"></p>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" class="btn-cancel" onclick="fermerModalSuppressionActu()">Annuler</button>
                <form id="del-actu-form" method="POST" action="" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-submit" style="background:#c0392b;"><i class="fa-solid fa-trash"></i> Oui, supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══ POPUP SUCCÈS ═══ --}}
<div id="successPopup" class="success-popup">
    <div class="success-card">
        <div class="success-icon"><i class="fa-solid fa-check"></i></div>
        <h4>Succès !</h4>
        <p id="successMessage">{{ session('success', '') }}</p>
        <button class="btn-submit" onclick="hideSuccess()" style="margin:0 auto;">
            <i class="fa-solid fa-thumbs-up"></i> OK
        </button>
    </div>
</div>

{{-- ═══ JAVASCRIPT ═══ --}}
<script>
    // Token CSRF pour les appels AJAX (commentaires)
    const CSRF_TOKEN = '{{ csrf_token() }}';

    // ── Données pour les cascades ──
    const DB_REGIONS      = @json($allRegions->map(fn ($r) => ['id' => $r->id, 'nom' => $r->nom])->values()->all());
    const DB_DEPARTEMENTS = @json($allDepartements->map(fn ($d) => ['id' => $d->id, 'nom' => $d->nom, 'region_id' => $d->region_id])->values()->all());
    const DB_COMMUNES     = @json($allCommunes->map(fn ($c) => ['id' => $c->id, 'nom' => $c->nom, 'departement_id' => $c->departement_id])->values()->all());
    const DB_LOCALITES    = @json($allLocalites->map(fn ($l) => ['id' => $l->id, 'nom' => $l->nom, 'commune_id' => $l->commune_id])->values()->all());

    function escHtml(v) {
        return String(v ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // ═══ FONCTIONS MODALES ═══
    function openFormModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeFormModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }
    function hideSuccess() {
        document.getElementById('successPopup').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Fermeture par Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(function(m) {
                m.classList.remove('active');
            });
            hideSuccess();
            document.body.style.overflow = '';
        }
    });

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('successPopup').classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(hideSuccess, 3000);
        });
    @endif

    // ═══ CASCADES — formulaire création ═══
    function majDepartementsActu() {
        const regionId = document.getElementById('actu-region').value;
        const selDept = document.getElementById('actu-departement');
        selDept.innerHTML = '<option value="">— Aucun département —</option>';
        (regionId === '' ? DB_DEPARTEMENTS : DB_DEPARTEMENTS.filter(d => String(d.region_id) === regionId))
            .forEach(d => selDept.insertAdjacentHTML('beforeend', `<option value="${d.id}">${escHtml(d.nom)}</option>`));
        document.getElementById('actu-commune').innerHTML = '<option value="">— Aucune commune —</option>';
        document.getElementById('actu-localite').innerHTML = '<option value="">— Aucune localité —</option>';
    }

    function choisirDepartementActu() {
        const deptId = document.getElementById('actu-departement').value;
        if (deptId !== '') document.getElementById('actu-region').value = '';
        const selCommune = document.getElementById('actu-commune');
        selCommune.innerHTML = '<option value="">— Aucune commune —</option>';
        DB_COMMUNES.filter(c => String(c.departement_id) === deptId)
            .forEach(c => selCommune.insertAdjacentHTML('beforeend', `<option value="${c.id}">${escHtml(c.nom)}</option>`));
        document.getElementById('actu-localite').innerHTML = '<option value="">— Aucune localité —</option>';
    }

    function choisirCommuneActu() {
        const communeId = document.getElementById('actu-commune').value;
        if (communeId !== '') {
            document.getElementById('actu-departement').value = '';
            document.getElementById('actu-region').value = '';
        }
        const selLoc = document.getElementById('actu-localite');
        selLoc.innerHTML = '<option value="">— Aucune localité —</option>';
        DB_LOCALITES.filter(l => String(l.commune_id) === communeId)
            .forEach(l => selLoc.insertAdjacentHTML('beforeend', `<option value="${l.id}">${escHtml(l.nom)}</option>`));
    }

    // ═══ CONSULTATION D'UNE ACTUALITÉ ═══
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-voir-actu');
        if (!btn) return;

        const titre       = btn.dataset.titre;
        const contenu     = btn.dataset.contenu;
        const date        = btn.dataset.date;
        const territoire  = btn.dataset.territoire;
        const niveau      = btn.dataset.niveau;
        const infra       = btn.dataset.infrastructure;
        let photos        = [];
        let commentaires  = [];
        try { photos = JSON.parse(btn.dataset.photos || '[]'); } catch {}
        try { commentaires = JSON.parse(btn.dataset.commentaires || '[]'); } catch {}

        // Titre
        document.getElementById('voir-actu-titre').textContent = 'Consulter — ' + titre;

        // Métadonnées
        const metaEl = document.getElementById('voir-actu-meta');
        metaEl.innerHTML = '';
        const lignes = [
            ['Titre', titre],
            ['Publication', date || '—'],
            ['Rattachement', niveau + ' : ' + territoire],
        ];
        if (infra) lignes.push(['Infrastructure', infra]);
        lignes.forEach(function([lib, val]) {
            const bloc = document.createElement('div');
            bloc.innerHTML = `<div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">${escHtml(lib)}</div>` +
                `<div style="font-size:13px; color:var(--text); font-weight:500; margin-top:2px;">${escHtml(val)}</div>`;
            metaEl.appendChild(bloc);
        });

        // Contenu
        document.getElementById('voir-actu-contenu').innerHTML = contenu.replace(/\n/g, '<br>');

        // Photos
        const photosSection = document.getElementById('voir-actu-photos-section');
        const photosWrap    = document.getElementById('voir-actu-photos');
        photosWrap.innerHTML = '';
        if (photos.length > 0) {
            photosSection.style.display = 'block';
            photos.forEach(function(p) {
                const fig = document.createElement('figure');
                fig.style.cssText = 'display:inline-block; margin:0; text-align:center;';
                fig.innerHTML = `<img src="${escHtml(p.src)}" alt="${escHtml(p.nom)}" loading="lazy" style="width:120px; height:120px; object-fit:cover; border-radius:10px; border:1px solid var(--border); cursor:zoom-in; background:var(--surface2);" onclick="window.open(this.src,'_blank','noopener')">` +
                    `<figcaption style="font-size:11px; color:var(--text-muted); margin-top:4px; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${escHtml(p.nom)}">${escHtml(p.nom)}</figcaption>`;
                photosWrap.appendChild(fig);
            });
        } else {
            photosSection.style.display = 'none';
        }

        // Commentaires
        renderCommentaires(commentaires);

        openFormModal('modal-voir-actu');
    });

    function renderCommentaires(commentaires) {
        const nbEl = document.getElementById('voir-actu-nb-commentaires');
        const wrap = document.getElementById('voir-actu-commentaires');
        wrap.innerHTML = '';
        nbEl.textContent = '(' + commentaires.length + ')';

        if (commentaires.length === 0) {
            wrap.innerHTML = '<div style="font-size:13px; color:var(--text-muted); font-style:italic; padding:12px 0;">Aucun commentaire.</div>';
            return;
        }

        commentaires.forEach(function(c) {
            const estLue = c.statut === 'lue';
            const badge = estLue
                ? '<span style="font-size:10px; font-weight:600; color:#267a47; background:#e8f5e9; padding:2px 8px; border-radius:10px;">Lu</span>'
                : '<span style="font-size:10px; font-weight:600; color:#c0392b; background:#fdecea; padding:2px 8px; border-radius:10px;">En attente</span>';

            const div = document.createElement('div');
            div.style.cssText = 'background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:12px; margin-bottom:10px;';
            div.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <div>
                        <span style="font-weight:600; color:var(--text); font-size:13px;">${escHtml(c.nom || 'Anonyme')}</span>
                        <span style="font-size:11px; color:var(--text-muted); margin-left:6px;">${escHtml(c.email)}</span>
                        ${badge}
                    </div>
                    <span style="font-size:11px; color:var(--text-muted);">${escHtml(c.date || '')}</span>
                </div>
                <div style="font-size:13px; color:var(--text-dim); line-height:1.5; margin-bottom:8px;">${escHtml(c.message)}</div>
                <div style="display:flex; gap:8px;">
                    ${!estLue ? `<button onclick="marquerLue(${c.id}, this)" style="font-size:11px; padding:4px 10px; background:var(--primary); color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-check"></i> Marquer lu</button>` : ''}
                    <button onclick="supprimerCommentaire(${c.id}, this)" style="font-size:11px; padding:4px 10px; background:#c0392b; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-trash"></i> Supprimer</button>
                </div>`;
            wrap.appendChild(div);
        });
    }

    // ── AJAX : marquer un commentaire comme lu ──
    async function marquerLue(id, btn) {
        btn.disabled = true;
        try {
            const resp = await fetch(`/Actualites/Commentaire/${id}/lue`, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            });
            if (resp.ok) {
                // Met à jour le badge dans le bouton data-commentaires
                const row = btn.closest('[data-commentaires]');
                if (row) {
                    let comms = [];
                    try { comms = JSON.parse(row.dataset.commentaires || '[]'); } catch {}
                    const c = comms.find(x => x.id === id);
                    if (c) c.statut = 'lue';
                    row.dataset.commentaires = JSON.stringify(comms);
                    renderCommentaires(comms);
                }
                showFlash('Commentaire marqué comme lu.');
            }
        } catch { btn.disabled = false; }
    }

    // ── AJAX : supprimer un commentaire ──
    async function supprimerCommentaire(id, btn) {
        if (!confirm('Supprimer ce commentaire ?')) return;
        btn.disabled = true;
        try {
            const resp = await fetch(`/Actualites/Commentaire/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            });
            if (resp.ok) {
                const row = btn.closest('[data-commentaires]');
                if (row) {
                    let comms = [];
                    try { comms = JSON.parse(row.dataset.commentaires || '[]'); } catch {}
                    comms = comms.filter(x => x.id !== id);
                    row.dataset.commentaires = JSON.stringify(comms);
                    renderCommentaires(comms);
                }
                showFlash('Commentaire supprimé.');
            }
        } catch { btn.disabled = false; }
    }

    function showFlash(msg) {
        document.getElementById('successMessage').textContent = msg;
        document.getElementById('successPopup').classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(hideSuccess, 2000);
    }

    // ═══ MODIFIER — ouverture de la modal avec les valeurs de la ligne ═══
    function openEditModalActu(btn) {
        let v = {};
        try { v = JSON.parse(btn.dataset.values || '{}'); } catch { v = {}; }

        document.getElementById('ed-actu-titre').value = v.titre || '';
        document.getElementById('ed-actu-contenu').value = v.contenu || '';
        document.getElementById('ed-actu-date').value = v.date_publication || '';
        document.getElementById('ed-actu-infrastructure').value = v.infrastructure_id || '';

        // Rattachement territorial (cascade)
        const regionSel    = document.getElementById('ed-actu-region');
        const deptSel      = document.getElementById('ed-actu-departement');
        const communeSel   = document.getElementById('ed-actu-commune');
        const localiteSel  = document.getElementById('ed-actu-localite');
        regionSel.value = v.region_id || '';

        // Départements (filtrés par région si précisée)
        deptSel.innerHTML = '<option value="">— Aucun département —</option>';
        (v.region_id ? DB_DEPARTEMENTS.filter(d => String(d.region_id) === String(v.region_id)) : DB_DEPARTEMENTS)
            .forEach(d => deptSel.insertAdjacentHTML('beforeend', `<option value="${d.id}">${escHtml(d.nom)}</option>`));
        deptSel.value = v.departement_id || '';

        // Communes (filtrées par département si précisé)
        communeSel.innerHTML = '<option value="">— Aucune commune —</option>';
        (v.departement_id ? DB_COMMUNES.filter(c => String(c.departement_id) === String(v.departement_id)) : DB_COMMUNES)
            .forEach(c => communeSel.insertAdjacentHTML('beforeend', `<option value="${c.id}">${escHtml(c.nom)}</option>`));
        communeSel.value = v.commune_id || '';

        // Localités (filtrées par commune si précisée)
        localiteSel.innerHTML = '<option value="">— Aucune localité —</option>';
        (v.commune_id ? DB_LOCALITES.filter(l => String(l.commune_id) === String(v.commune_id)) : DB_LOCALITES)
            .forEach(l => localiteSel.insertAdjacentHTML('beforeend', `<option value="${l.id}">${escHtml(l.nom)}</option>`));
        localiteSel.value = v.localite_id || '';

        // Photos existantes (cocher pour supprimer) + formulaire PUT
        const zone = document.getElementById('ed-actu-photos-existantes');
        if ((v.photos || []).length) {
            zone.innerHTML = `<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:10px; margin-bottom:10px;">` +
                v.photos.map(p => `
                    <div style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:8px; text-align:center;">
                        <img src="${escHtml(p.src)}" alt="${escHtml(p.nom)}" style="width:100%; height:80px; object-fit:cover; border-radius:7px; display:block; margin-bottom:6px;">
                        <div style="font-size:11px; color:var(--text-dim); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-bottom:6px;" title="${escHtml(p.nom)}">${escHtml(p.nom)}</div>
                        <label style="display:inline-flex; align-items:center; gap:5px; font-size:11px; color:var(--red); cursor:pointer; font-weight:600;">
                            <input type="checkbox" name="photos_supprimer[]" value="${p.id}"> Supprimer
                        </label>
                    </div>`).join('') + `</div>`;
        } else {
            zone.innerHTML = `<div style="font-size:12px; color:var(--text-muted); font-style:italic; margin-bottom:10px;">Aucune photo actuellement.</div>`;
        }

        document.getElementById('edit-actu-form').action = `/Actualites/Actualite/${v.id}`;
        openFormModal('modal-edit-actu');
    }

    // ── Cascades du formulaire Modifier ──
    function majDepartementsActuEd() {
        const regionId = document.getElementById('ed-actu-region').value;
        const selDept = document.getElementById('ed-actu-departement');
        selDept.innerHTML = '<option value="">— Aucun département —</option>';
        (regionId === '' ? DB_DEPARTEMENTS : DB_DEPARTEMENTS.filter(d => String(d.region_id) === regionId))
            .forEach(d => selDept.insertAdjacentHTML('beforeend', `<option value="${d.id}">${escHtml(d.nom)}</option>`));
        document.getElementById('ed-actu-commune').innerHTML = '<option value="">— Aucune commune —</option>';
        document.getElementById('ed-actu-localite').innerHTML = '<option value="">— Aucune localité —</option>';
    }
    function choisirDepartementActuEd() {
        const deptId = document.getElementById('ed-actu-departement').value;
        if (deptId !== '') document.getElementById('ed-actu-region').value = '';
        const selCommune = document.getElementById('ed-actu-commune');
        selCommune.innerHTML = '<option value="">— Aucune commune —</option>';
        DB_COMMUNES.filter(c => String(c.departement_id) === deptId)
            .forEach(c => selCommune.insertAdjacentHTML('beforeend', `<option value="${c.id}">${escHtml(c.nom)}</option>`));
        document.getElementById('ed-actu-localite').innerHTML = '<option value="">— Aucune localité —</option>';
    }
    function choisirCommuneActuEd() {
        const communeId = document.getElementById('ed-actu-commune').value;
        if (communeId !== '') {
            document.getElementById('ed-actu-departement').value = '';
            document.getElementById('ed-actu-region').value = '';
        }
        const selLoc = document.getElementById('ed-actu-localite');
        selLoc.innerHTML = '<option value="">— Aucune localité —</option>';
        DB_LOCALITES.filter(l => String(l.commune_id) === communeId)
            .forEach(l => selLoc.insertAdjacentHTML('beforeend', `<option value="${l.id}">${escHtml(l.nom)}</option>`));
    }

    // Délégation : bouton Modifier
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-actu');
        if (!btn) return;
        openEditModalActu(btn);
    });

    // ═══ SUPPRIMER — confirmation avec impact (photos + commentaires) ═══
    let delActuId = null;
    function fermerModalSuppressionActu() {
        document.getElementById('modal-suppr-actu').classList.remove('active');
        document.body.style.overflow = '';
        delActuId = null;
    }
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-del-actu');
        if (!btn) return;

        delActuId = btn.dataset.id;
        document.getElementById('del-actu-form').action = `/Actualites/Actualite/${delActuId}`;
        document.getElementById('del-actu-intro').textContent = 'Chargement de l’impact…';
        document.getElementById('del-actu-liste').innerHTML = '';
        document.getElementById('del-actu-note').style.display = 'none';
        openFormModal('modal-suppr-actu');

        fetch(`/Actualites/Impact/${delActuId}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                document.getElementById('del-actu-intro').textContent = data.intro || 'Confirmer la suppression ?';
                const liste = document.getElementById('del-actu-liste');
                if (data.lignes && data.lignes.length) {
                    liste.innerHTML = data.lignes.map(l =>
                        `<li><strong>${l.nombre}</strong> ${l.label}${l.exemples ? ' : ' + escHtml(l.exemples) : ''}</li>`
                    ).join('');
                } else {
                    liste.innerHTML = '<li style="font-style:italic; color:var(--text-muted);">Aucun élément lié.</li>';
                }
                const note = document.getElementById('del-actu-note');
                if (data.note) { note.textContent = data.note; note.style.display = 'block'; }
            })
            .catch(() => {
                document.getElementById('del-actu-intro').textContent = 'Confirmer la suppression ?';
            });
    });

    // ═══ INIT ═══
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('actu-departement')) majDepartementsActu();
    });
</script>

@endsection
