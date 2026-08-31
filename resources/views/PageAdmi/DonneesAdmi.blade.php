@extends('AppAdmi')
@section('content')

{{--
  ══════════════════════════════════════════════════════════════
  PAGE GESTION DES DONNÉES — Admin
  Layout : sidebar gauche (menu + filtres) + zone principale (tableau)
  Filtres cascendants : Région → Département → Commune → Localité
  ══════════════════════════════════════════════════════════════
--}}

<div style="display:flex; height:calc(100vh - var(--hdr)); margin-top:var(--hdr);">

    {{-- ═══ SIDEBAR GAUCHE ═══ --}}
    <aside style="width:240px; min-width:240px; background:var(--white); border-right:1px solid var(--border); padding:16px 12px; overflow-y:auto; display:flex; flex-direction:column; gap:4px;">

        {{-- Titre sidebar --}}
        <div style="font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:var(--text); margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid var(--border);">
            <i class="fa-solid fa-database" style="margin-right:6px; color:var(--primary);"></i> Gestion des données
        </div>

        {{-- ═══ Menu catégorie : chaque clic charge l'onglet correspondant ═══ --}}
        @php
            $menuItems = [
                'regions'         => ['icon' => 'fa-solid fa-location-dot', 'label' => 'Régions'],
                'departements'    => ['icon' => 'fa-solid fa-map',          'label' => 'Départements'],
                'communes'        => ['icon' => 'fa-solid fa-city',         'label' => 'Communes'],
                'localites'       => ['icon' => 'fa-solid fa-house',        'label' => 'Localités'],
                'secteurs'        => ['icon' => 'fa-solid fa-street-view',  'label' => 'Secteurs'],
                'infrastructures' => ['icon' => 'fa-solid fa-landmark',     'label' => 'Infrastructures'],
            ];
        @endphp

        @foreach($menuItems as $key => $item)
            <a href="{{ route('DonneesAdmi', ['tab' => $key]) }}"
               style="display:flex; align-items:center; gap:9px; padding:9px 11px; border-radius:8px; font-size:13px; text-decoration:none; transition:.2s; border:1px solid transparent;
                      {{ $tab === $key ? 'color:var(--primary); background:var(--primary-lt); border-color:#b5dfc6; font-weight:600;' : 'color:var(--text-dim);' }}"
               onmouseover="if('{{ $tab }}' !== '{{ $key }}') this.style.color='var(--text)'; this.style.background='var(--surface2)'"
               onmouseout="if('{{ $tab }}' !== '{{ $key }}') this.style.color='var(--text-dim)'; this.style.background='transparent'">
                <i class="{{ $item['icon'] }}"></i>
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- ═══ Zone filtres (apparaît selon l'onglet actif) ═══ --}}
        <div style="margin-top:16px; padding-top:12px; border-top:1px solid var(--border);">
            <div style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.6px; margin-bottom:10px;">
                <i class="fa-solid fa-filter" style="margin-right:4px;"></i> Filtres
            </div>

            {{-- Filtre Région : visible pour Départements, Communes, Localités, Infrastructures --}}
            @if(in_array($tab, ['departements', 'communes', 'localites', 'infrastructures']))
                <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Région</label>
                    <form id="filter-region-form" method="GET" action="{{ route('DonneesAdmi') }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                        @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                        @if($secteurId) <input type="hidden" name="secteur_id" value="{{ $secteurId }}"> @endif
                        @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                        <select name="region_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                            <option value="">Toutes les régions</option>
                            @foreach($allRegions as $r)
                                <option value="{{ $r->id }}" {{ $regionId == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

            {{-- Filtre Département : visible pour Communes, Localités, Infrastructures --}}
            @if(in_array($tab, ['communes', 'localites', 'infrastructures']))
                <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Département</label>
                    <form id="filter-dept-form" method="GET" action="{{ route('DonneesAdmi') }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                        @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                        @if($secteurId) <input type="hidden" name="secteur_id" value="{{ $secteurId }}"> @endif
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
            @endif

            {{-- Filtre Commune : visible pour Localités et Infrastructures --}}
            @if(in_array($tab, ['localites', 'infrastructures']))
                <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Commune</label>
                    <form id="filter-commune-form" method="GET" action="{{ route('DonneesAdmi') }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                        @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                        @if($secteurId) <input type="hidden" name="secteur_id" value="{{ $secteurId }}"> @endif
                        @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                        <select name="commune_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                            <option value="">Toutes les communes</option>
                            @php
                                // Communes proposées dans le filtre : cohérentes avec la
                                // région et/ou le département déjà choisis (cascades).
                                $filteredCommunes = $departementId
                                    ? $allCommunes->where('departement_id', $departementId)
                                    : ($regionId
                                        // Aucun département mais une région → communes des
                                        // départements de cette région.
                                        ? $allCommunes->filter(fn ($c) => in_array($c->departement_id, $allDepartements->where('region_id', $regionId)->pluck('id')->all()))
                                        : $allCommunes);
                            @endphp
                            @foreach($filteredCommunes as $c)
                                <option value="{{ $c->id }}" {{ $communeId == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

            {{-- ═══ Filtres spécifiques aux Infrastructures : Localité + Secteur ═══ --}}
            @if($tab === 'infrastructures')
                {{-- Filtre Localité --}}
                <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Localité</label>
                    <form id="filter-localite-form" method="GET" action="{{ route('DonneesAdmi') }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                        @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                        @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                        @if($secteurId) <input type="hidden" name="secteur_id" value="{{ $secteurId }}"> @endif
                        <select name="localite_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                            <option value="">Toutes les localités</option>
                            @php
                                // Localités proposées : cohérentes avec la commune déjà choisie,
                                // sinon avec la région/département (cascades complètes).
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

                {{-- Filtre Secteur --}}
                <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11px; font-weight:600; color:var(--text-dim); margin-bottom:4px;">Secteur</label>
                    <form id="filter-secteur-form" method="GET" action="{{ route('DonneesAdmi') }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if($regionId) <input type="hidden" name="region_id" value="{{ $regionId }}"> @endif
                        @if($departementId) <input type="hidden" name="departement_id" value="{{ $departementId }}"> @endif
                        @if($communeId) <input type="hidden" name="commune_id" value="{{ $communeId }}"> @endif
                        @if($localiteId) <input type="hidden" name="localite_id" value="{{ $localiteId }}"> @endif
                        <select name="secteur_id" onchange="this.form.submit()" style="width:100%; padding:7px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface2); color:var(--text); cursor:pointer;">
                            <option value="">Tous les secteurs</option>
                            @foreach($allSecteurs as $s)
                                <option value="{{ $s->id }}" {{ $secteurId == $s->id ? 'selected' : '' }}>{{ $s->nom }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

            {{-- Bouton réinitialiser les filtres --}}
            @if($regionId || $departementId || $communeId || $secteurId || $localiteId)
                <a href="{{ route('DonneesAdmi', ['tab' => $tab]) }}"
                   style="display:flex; align-items:center; justify-content:center; gap:6px; padding:7px; font-size:11px; font-weight:500; color:var(--text-dim); text-decoration:none; border:1px solid var(--border); border-radius:7px; transition:.2s; margin-top:4px;"
                   onmouseover="this.style.borderColor='var(--red)'; this.style.color='var(--red)'"
                   onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-dim)'">
                    <i class="fa-solid fa-xmark"></i> Réinitialiser les filtres
                </a>
            @endif
        </div>

    </aside>

    {{-- ═══ ZONE PRINCIPALE ═══ --}}
    <main style="flex:1; overflow-y:auto; padding:28px 36px; background:var(--bg);">

        {{-- Bandeau d'erreurs : validation des formulaires OU erreurs métier (suppression bloquée...) --}}
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

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ONGLET RÉGIONS                                          --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @if($tab === 'regions')
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Régions</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $regions->total() }} région(s) au total</div>
                </div>
                <button onclick="openFormModal('modal-region')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
            <div style="background:var(--surface); border-radius:14px; box-shadow:var(--card-shadow); padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);">
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase; letter-spacing:.5px;">Nom</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Superficie</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Population</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Ménages</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Infrastructures</th>
                            <th style="text-align:center; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regions as $r)
                            <tr style="border-bottom:1px solid var(--border2); transition:background .15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 12px; font-weight:500; color:var(--text);">{{ $r->nom }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $r->superficie ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $r->taille_population ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $r->nbre_menage ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $r->nbre_infrastructure ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    {{-- Consulter : ouvre la modal de consultation avec les détails de la ligne --}}
                                    <button type="button" class="btn-voir" data-entity="region" data-id="{{ $r->id }}"
                                        data-values="{{ json_encode($r->only(['nom','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','nbre_infrastructure','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:13px; padding:4px 8px;" title="Consulter"><i class="fa-solid fa-eye"></i></button>
                                    {{-- Modifier : ouvre la modal d'édition générique avec les valeurs de la ligne --}}
                                    <button type="button" class="btn-edit" data-entity="region" data-id="{{ $r->id }}"
                                        data-values="{{ json_encode($r->only(['nom','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','nbre_infrastructure','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:13px; padding:4px 8px;" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    {{-- Supprimer : formulaire DELETE avec confirmation (protection CSRF) --}}
                                    <form action="{{ route('donnees.destroyRegion', $r) }}" method="POST" style="display:inline-block;" onsubmit="confirmerSuppression(event, 'region', {{ $r->id }}, @js($r->nom));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; padding:4px 8px;" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="padding:24px; text-align:center; color:var(--text-muted);">Aucune région trouvée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;">{{ $regions->withQueryString()->links() }}</div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ONGLET DÉPARTEMENTS                                      --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @elseif($tab === 'departements')
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Départements</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $departements->total() }} département(s) au total</div>
                </div>
                <button onclick="openFormModal('modal-departement')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
            <div style="background:var(--surface); border-radius:14px; box-shadow:var(--card-shadow); padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);">
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Nom</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Région</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Superficie</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Population</th>
                            <th style="text-align:center; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departements as $d)
                            <tr style="border-bottom:1px solid var(--border2); transition:background .15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 12px; font-weight:500; color:var(--text);">{{ $d->nom }}</td>
                                <td style="padding:10px 12px; color:var(--text-dim);">{{ $d->region->nom ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $d->superficie ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $d->taille_population ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <button type="button" class="btn-voir" data-entity="departement" data-id="{{ $d->id }}"
                                        data-values="{{ json_encode($d->only(['nom','region_id','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:13px; padding:4px 8px;" title="Consulter"><i class="fa-solid fa-eye"></i></button>
                                    <button type="button" class="btn-edit" data-entity="departement" data-id="{{ $d->id }}"
                                        data-values="{{ json_encode($d->only(['nom','region_id','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:13px; padding:4px 8px;" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('donnees.destroyDepartement', $d) }}" method="POST" style="display:inline-block;" onsubmit="confirmerSuppression(event, 'departement', {{ $d->id }}, @js($d->nom));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; padding:4px 8px;" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="padding:24px; text-align:center; color:var(--text-muted);">Aucun département trouvé</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;">{{ $departements->withQueryString()->links() }}</div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ONGLET COMMUNES                                          --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @elseif($tab === 'communes')
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div style="font-family:'Montserrat'; font-size:30px;  font-weight:800; color:var(--text);">Communes</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $communes->total() }} commune(s) au total</div>
                </div>
                <button onclick="openFormModal('modal-commune')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
            <div style="background:var(--surface); border-radius:14px; box-shadow:var(--card-shadow); padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);">
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Nom</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Département</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Superficie</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Population</th>
                            <th style="text-align:center; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($communes as $c)
                            <tr style="border-bottom:1px solid var(--border2); transition:background .15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 12px; font-weight:500; color:var(--text);">{{ $c->nom }}</td>
                                <td style="padding:10px 12px; color:var(--text-dim);">{{ $c->departement->nom ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $c->superficie ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $c->taille_population ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <button type="button" class="btn-voir" data-entity="commune" data-id="{{ $c->id }}"
                                        data-values="{{ json_encode($c->only(['nom','departement_id','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:13px; padding:4px 8px;" title="Consulter"><i class="fa-solid fa-eye"></i></button>
                                    <button type="button" class="btn-edit" data-entity="commune" data-id="{{ $c->id }}"
                                        data-values="{{ json_encode($c->only(['nom','departement_id','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:13px; padding:4px 8px;" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('donnees.destroyCommune', $c) }}" method="POST" style="display:inline-block;" onsubmit="confirmerSuppression(event, 'commune', {{ $c->id }}, @js($c->nom));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; padding:4px 8px;" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="padding:24px; text-align:center; color:var(--text-muted);">Aucune commune trouvée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;">{{ $communes->withQueryString()->links() }}</div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ONGLET LOCALITÉS                                         --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @elseif($tab === 'localites')
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Localités</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $localites->total() }} localité(s) au total</div>
                </div>
                <button onclick="openFormModal('modal-localite')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
            <div style="background:var(--surface); border-radius:14px; box-shadow:var(--card-shadow); padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);">
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Nom</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Commune</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Superficie</th>
                            <th style="text-align:right; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Population</th>
                            <th style="text-align:center; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($localites as $l)
                            <tr style="border-bottom:1px solid var(--border2); transition:background .15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 12px; font-weight:500; color:var(--text);">{{ $l->nom }}</td>
                                <td style="padding:10px 12px; color:var(--text-dim);">{{ $l->commune->nom ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $l->superficie ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:right; color:var(--text-dim);">{{ $l->taille_population ?? '—' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <button type="button" class="btn-voir" data-entity="localite" data-id="{{ $l->id }}"
                                        data-values="{{ json_encode($l->only(['nom','commune_id','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:13px; padding:4px 8px;" title="Consulter"><i class="fa-solid fa-eye"></i></button>
                                    <button type="button" class="btn-edit" data-entity="localite" data-id="{{ $l->id }}"
                                        data-values="{{ json_encode($l->only(['nom','commune_id','superficie','taille_population','nbre_menage','nbre_homme','nbre_femme','latitude','longitude'])) }}"
                                        style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:13px; padding:4px 8px;" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('donnees.destroyLocalite', $l) }}" method="POST" style="display:inline-block;" onsubmit="confirmerSuppression(event, 'localite', {{ $l->id }}, @js($l->nom));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; padding:4px 8px;" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="padding:24px; text-align:center; color:var(--text-muted);">Aucune localité trouvée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;">{{ $localites->withQueryString()->links() }}</div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ONGLET SECTEURS                                          --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @elseif($tab === 'secteurs')
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Secteurs</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $secteurs->total() }} secteur(s) au total</div>
                </div>
                <button onclick="openFormModal('modal-secteur')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
            <div style="background:var(--surface); border-radius:14px; box-shadow:var(--card-shadow); padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);">
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Nom</th>
                            <th style="text-align:center; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($secteurs as $s)
                            <tr style="border-bottom:1px solid var(--border2); transition:background .15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 12px; font-weight:500; color:var(--text);">{{ $s->nom }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <button type="button" class="btn-voir" data-entity="secteur" data-id="{{ $s->id }}"
                                        data-values="{{ json_encode($s->only(['nom'])) }}"
                                        style="background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:13px; padding:4px 8px;" title="Consulter"><i class="fa-solid fa-eye"></i></button>
                                    <button type="button" class="btn-edit" data-entity="secteur" data-id="{{ $s->id }}"
                                        data-values="{{ json_encode(array_merge($s->only(['nom']), [
                                            'indicateurs' => $s->indicateurs->map(fn ($ind) => [
                                                'id' => $ind->id,
                                                'nom_indicateur' => $ind->nom_indicateur,
                                                'unites' => $ind->unites,
                                                'description' => $ind->description,
                                            ])->values()->all(),
                                        ])) }}"
                                        style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:13px; padding:4px 8px;" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('donnees.destroySecteur', $s) }}" method="POST" style="display:inline-block;" onsubmit="confirmerSuppression(event, 'secteur', {{ $s->id }}, @js($s->nom));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; padding:4px 8px;" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="padding:24px; text-align:center; color:var(--text-muted);">Aucun secteur trouvé</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;">{{ $secteurs->withQueryString()->links() }}</div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ONGLET INFRASTRUCTURES                                   --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @elseif($tab === 'infrastructures')
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <div style="font-family:'Montserrat'; font-size:30px; font-weight:800; color:var(--text);">Infrastructures</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $infrastructures->total() }} infrastructure(s) au total</div>
                </div>
                <button onclick="openFormModal('modal-infrastructure')" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer;">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </div>
            <div style="background:var(--surface); border-radius:14px; box-shadow:var(--card-shadow); padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);">
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Nom</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Type</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">inplantation</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Secteur</th>
                            <th style="text-align:left; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">État</th>
                            <th style="text-align:center; padding:10px 12px; font-weight:600; color:var(--text-dim); font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($infrastructures as $i)
                            <tr style="border-bottom:1px solid var(--border2); transition:background .15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 12px; font-weight:500; color:var(--text);">{{ $i->nom }}</td>
                                <td style="padding:10px 12px; color:var(--text-dim);">{{ $i->type_infrastructure ?? '—' }}</td>
                                {{-- Rattachement : le niveau effectivement choisi (localités > commune > département) --}}
                                <td style="padding:10px 12px; color:var(--text-dim);">
                                    @if($i->localitesCouvertes->isNotEmpty())
                                        <span style="display:inline-block; background:var(--primary-lt); color:var(--primary); font-size:10px; font-weight:700; border-radius:20px; padding:2px 8px; margin-bottom:4px;">
                                            Localités ({{ $i->localitesCouvertes->count() }})
                                        </span>
                                        <span style="display:block;">{{ $i->localitesCouvertes->pluck('nom')->implode(', ') }}</span>
                                        <span style="display:block; font-size:11px; color:var(--text-muted); margin-top:2px;">Commune : {{ $i->commune?->nom ?? '—' }} • Pop. couverte : {{ number_format($i->localitesCouvertes->sum('pivot.nbre_population_couvert'), 0, ',', ' ') }} hab.</span>
                                    @elseif($i->commune_id)
                                        <span style="font-weight:600; color:var(--text);">Commune :</span> {{ $i->commune?->nom ?? '—' }}
                                    @else
                                        <span style="font-weight:600; color:var(--text);">Département :</span> {{ $i->departement?->nom ?? $i->commune?->departement?->nom ?? '—' }}
                                    @endif
                                </td>
                                <td style="padding:10px 12px; color:var(--text-dim);">{{ $i->secteur?->nom ?? '—' }}</td>
                                <td style="padding:10px 12px; color:var(--text-dim);">{{ $i->etat_lieu ? str_replace('_', ' ', $i->etat_lieu) : '—' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    {{-- Valeurs passées à la modal de consultation : champs simples + IDs des localités couvertes + population totale actuelle + valeurs des indicateurs --}}
                                    <button type="button" class="btn-voir" data-entity="infrastructure" data-id="{{ $i->id }}"
                                        data-values="{{ json_encode(array_merge($i->only(['nom','description','type_infrastructure','departement_id','commune_id','secteur_id','etat_lieu','latitude','longitude']), [
                                            'date_creation' => $i->date_creation?->format('Y-m-d'),
                                            'localites' => $i->localitesCouvertes->pluck('id')->all(),
                                            'population_couverte' => $i->localitesCouvertes->sum('pivot.nbre_population_couvert') ?: null,
                                            'indicateurs_valeurs' => $i->indicateurs->mapWithKeys(fn ($ind) => [$ind->id => $ind->pivot->valeur])->all(),
                                        ])) }}"
                                        style="background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:13px; padding:4px 8px;" title="Consulter"><i class="fa-solid fa-eye"></i></button>
                                    {{-- Valeurs passées à la modal d'édition : champs simples + IDs des localités couvertes + population totale actuelle + valeurs des indicateurs --}}
                                    <button type="button" class="btn-edit" data-entity="infrastructure" data-id="{{ $i->id }}"
                                        data-values="{{ json_encode(array_merge($i->only(['nom','description','type_infrastructure','departement_id','commune_id','secteur_id','etat_lieu','latitude','longitude']), [
                                            'date_creation' => $i->date_creation?->format('Y-m-d'),
                                            'localites' => $i->localitesCouvertes->pluck('id')->all(),
                                            'population_couverte' => $i->localitesCouvertes->sum('pivot.nbre_population_couvert') ?: null,
                                            'indicateurs_valeurs' => $i->indicateurs->mapWithKeys(fn ($ind) => [$ind->id => $ind->pivot->valeur])->all(),
                                        ])) }}"
                                        style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:13px; padding:4px 8px;" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('donnees.destroyInfrastructure', $i) }}" method="POST" style="display:inline-block;" onsubmit="confirmerSuppression(event, 'infrastructure', {{ $i->id }}, @js($i->nom));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; padding:4px 8px;" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="padding:24px; text-align:center; color:var(--text-muted);">Aucune infrastructure trouvée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px;">{{ $infrastructures->withQueryString()->links() }}</div>
        @endif

    </main>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODALS DE CRÉATION — 6 formulaires, un par entité           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}

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
</style>

{{-- ═══ MODAL RÉGION ═══ --}}
<div id="modal-region" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-region')">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-location-dot" style="color:var(--primary); margin-right:8px;"></i> Ajouter une Région</h3>
            <button class="modal-close" onclick="closeFormModal('modal-region')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('donnees.storeRegion') }}">
                @csrf
                <div class="field-row">
                    <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div>
                    <div class="field-group"><label>Superficie</label><input type="text" name="superficie"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Population</label><input type="number" name="taille_population"></div>
                    <div class="field-group"><label>Ménages</label><input type="number" name="nbre_menage"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Hommes</label><input type="number" name="nbre_homme"></div>
                    <div class="field-group"><label>Femmes</label><input type="number" name="nbre_femme"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Infrastructures</label><input type="number" name="nbre_infrastructure"></div>
                    <div class="field-group"><label>Latitude</label><input type="text" name="latitude" placeholder="ex: 14.6928"></div>
                </div>
                <div class="field-group"><label>Longitude</label><input type="text" name="longitude" placeholder="ex: -17.4467"></div>
                {{-- Message d'aide : la somme hommes + femmes ne doit pas dépasser la population.
                     Affiché automatiquement par le JS (majMessageSommePop) quand la somme excède. --}}
                <div data-msg-somme style="display:none; padding:10px 12px; border-radius:8px; background:#fdecea; border-left:4px solid #c0392b; color:#c0392b; font-size:12px; font-weight:600; margin-bottom:12px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> La somme des hommes et des femmes (<span data-somme>0</span>) ne doit pas être supérieure à la population (<span data-pop>0</span>).
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-region')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL DÉPARTEMENT ═══ --}}
<div id="modal-departement" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-departement')">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-map" style="color:var(--primary); margin-right:8px;"></i> Ajouter un Département</h3>
            <button class="modal-close" onclick="closeFormModal('modal-departement')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('donnees.storeDepartement') }}">
                @csrf
                <div class="field-row">
                    <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div>
                    <div class="field-group">
                        <label>Région *</label>
                        <select name="region_id" required>
                            <option value="">Sélectionner une région</option>
                            @foreach($allRegions as $r)
                                <option value="{{ $r->id }}">{{ $r->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Superficie</label><input type="text" name="superficie"></div>
                    <div class="field-group"><label>Population</label><input type="number" name="taille_population"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Ménages</label><input type="number" name="nbre_menage"></div>
                    <div class="field-group"><label>Hommes</label><input type="number" name="nbre_homme"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Femmes</label><input type="number" name="nbre_femme"></div>
                    <div class="field-group"><label>Latitude</label><input type="text" name="latitude"></div>
                </div>
                <div class="field-group"><label>Longitude</label><input type="text" name="longitude"></div>
                {{-- Message d'aide : la somme hommes + femmes ne doit pas dépasser la population --}}
                <div data-msg-somme style="display:none; padding:10px 12px; border-radius:8px; background:#fdecea; border-left:4px solid #c0392b; color:#c0392b; font-size:12px; font-weight:600; margin-bottom:12px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> La somme des hommes et des femmes (<span data-somme>0</span>) ne doit pas être supérieure à la population (<span data-pop>0</span>).
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-departement')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL COMMUNE ═══ --}}
<div id="modal-commune" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-commune')">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-city" style="color:var(--primary); margin-right:8px;"></i> Ajouter une Commune</h3>
            <button class="modal-close" onclick="closeFormModal('modal-commune')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('donnees.storeCommune') }}">
                @csrf
                {{-- ═══ FILTRE DE RECHERCHE : Région → Département ═══
                     La région n'est PAS enregistrée : elle sert uniquement
                     à filtrer la liste des départements pour trouver vite. --}}
                <div class="field-row">
                    <div class="field-group">
                        <label>Région <span style="font-weight:400; color:var(--text-muted);">(filtre)</span></label>
                        <select id="com-region" onchange="majDepartementsCom()">
                            <option value="">— Toutes les régions —</option>
                            @foreach($allRegions as $rg)
                                <option value="{{ $rg->id }}">{{ $rg->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Département *</label>
                        {{-- Liste remplie par le JS selon la région choisie --}}
                        <select name="departement_id" id="com-departement" required>
                            <option value="">Sélectionner un département</option>
                            @foreach($allDepartements as $d)
                                <option value="{{ $d->id }}">{{ $d->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div> --}}
                </div>
                   <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div>
                <div class="field-row">
                    <div class="field-group"><label>Superficie</label><input type="text" name="superficie"></div>
                    <div class="field-group"><label>Population</label><input type="number" name="taille_population"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Ménages</label><input type="number" name="nbre_menage"></div>
                    <div class="field-group"><label>Hommes</label><input type="number" name="nbre_homme"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Femmes</label><input type="number" name="nbre_femme"></div>
                    <div class="field-group"><label>Latitude</label><input type="text" name="latitude"></div>
                </div>
                <div class="field-group"><label>Longitude</label><input type="text" name="longitude"></div>
                {{-- Message d'aide : la somme hommes + femmes ne doit pas dépasser la population --}}
                <div data-msg-somme style="display:none; padding:10px 12px; border-radius:8px; background:#fdecea; border-left:4px solid #c0392b; color:#c0392b; font-size:12px; font-weight:600; margin-bottom:12px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> La somme des hommes et des femmes (<span data-somme>0</span>) ne doit pas être supérieure à la population (<span data-pop>0</span>).
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-commune')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL LOCALITÉ ═══ --}}
<div id="modal-localite" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-localite')">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-house" style="color:var(--primary); margin-right:8px;"></i> Ajouter une Localité</h3>
            <button class="modal-close" onclick="closeFormModal('modal-localite')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('donnees.storeLocalite') }}">
                @csrf
                {{-- ═══ FILTRES DE RECHERCHE : Région → Département → Commune ═══
                     La région et le département ne sont PAS enregistrés : ils servent
                     uniquement à filtrer la liste des communes pour la trouver vite. --}}
                <div class="field-row">
                    <div class="field-group">
                        <label>Région <span style="font-weight:400; color:var(--text-muted);">(filtre)</span></label>
                        <select name="region_filtre" id="loc-region" onchange="majDepartementsLoc()">
                            <option value="">— Toutes les régions —</option>
                            @foreach($allRegions as $rg)
                                <option value="{{ $rg->id }}">{{ $rg->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Département <span style="font-weight:400; color:var(--text-muted);">(filtre)</span></label>
                        <select name="departement_filtre" id="loc-departement" onchange="majCommunesLoc()">
                            <option value="">— Tous les départements —</option>
                        </select>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div>
                    <div class="field-group">
                        <label>Commune *</label>
                        {{-- Liste remplie par le JS selon les filtres région/département --}}
                        <select name="commune_id" id="loc-commune" required>
                            <option value="">Sélectionner une commune</option>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Superficie</label><input type="text" name="superficie"></div>
                    <div class="field-group"><label>Population</label><input type="number" name="taille_population"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Ménages</label><input type="number" name="nbre_menage"></div>
                    <div class="field-group"><label>Hommes</label><input type="number" name="nbre_homme"></div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Femmes</label><input type="number" name="nbre_femme"></div>
                    <div class="field-group"><label>Latitude</label><input type="text" name="latitude"></div>
                </div>
                <div class="field-group"><label>Longitude</label><input type="text" name="longitude"></div>
                {{-- Message d'aide : la somme hommes + femmes ne doit pas dépasser la population --}}
                <div data-msg-somme style="display:none; padding:10px 12px; border-radius:8px; background:#fdecea; border-left:4px solid #c0392b; color:#c0392b; font-size:12px; font-weight:600; margin-bottom:12px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> La somme des hommes et des femmes (<span data-somme>0</span>) ne doit pas être supérieure à la population (<span data-pop>0</span>).
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-localite')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL SECTEUR ═══ --}}
<div id="modal-secteur" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-secteur')">
    <div class="modal-card" style="max-width:720px;">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-street-view" style="color:var(--primary); margin-right:8px;"></i> Ajouter un Secteur</h3>
            <button class="modal-close" onclick="closeFormModal('modal-secteur')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('donnees.storeSecteur') }}">
                @csrf
                <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div>

                {{-- ═══ INDICATEURS (critères de mesure) du secteur ═══
                     Liste dynamique : on peut ajouter/supprimer des lignes avant
                     l'envoi. Chaque ligne = un indicateur (nom, unité, description)
                     créé en même temps que le secteur. --}}
                <div style="margin-top:6px; padding-top:14px; border-top:1px solid var(--border);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                        <label style="font-size:12px; font-weight:700; color:var(--text);">Indicateurs du secteur <span style="font-weight:400; color:var(--text-muted);">(facultatif)</span></label>
                        <button type="button" onclick="ajouterLigneIndicateur()" style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; background:var(--primary); color:#fff; border:none; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer;">
                            <i class="fa-solid fa-plus"></i> Ajouter un indicateur
                        </button>
                    </div>
                    <div id="indicateurs-liste"></div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:8px;">
                        <i class="fa-solid fa-circle-info"></i> Les indicateurs définissent ce que chaque infrastructure de ce secteur devra mesurer.
                    </div>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-secteur')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL INFRASTRUCTURE ═══ --}}
<div id="modal-infrastructure" class="modal-overlay" onclick="if(event.target===this) closeFormModal('modal-infrastructure')">
    <div class="modal-card" style="max-width:740px;">
        <div class="modal-card-header">
            <h3><i class="fa-solid fa-landmark" style="color:var(--primary); margin-right:8px;"></i> Ajouter une Infrastructure</h3>
            <button class="modal-close" onclick="closeFormModal('modal-infrastructure')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <form method="POST" action="{{ route('donnees.storeInfrastructure') }}">
                @csrf
                <div class="field-row">
                    <div class="field-group"><label>Nom *</label><input type="text" name="nom" required></div>
                    <div class="field-group"><label>Type d'infrastructure</label><input type="text" name="type_infrastructure"></div>
                </div>
                <div class="field-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
                {{-- ═══ TERRITOIRE : région (filtre) → département OU commune OU localités ═══ --}}
                {{-- Filtre Région : sert uniquement à faciliter la recherche du département --}}
                <div class="field-group">
                    <label>Région <span style="font-weight:400; color:var(--text-muted);">(filtre de recherche, facultatif)</span></label>
                    <select name="region_filtre" id="infra-region" onchange="majDepartementsInfra()">
                        <option value="">— Toutes les régions —</option>
                        @foreach($allRegions as $rg)
                            <option value="{{ $rg->id }}">{{ $rg->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field-group">
                    <label>Département <span style="font-weight:400; color:var(--text-muted);">(ou rattachement direct)</span></label>
                    {{-- Liste remplie par le JS selon la région choisie --}}
                    <select name="departement_id" id="infra-departement" onchange="majCommunesInfra()">
                        <option value="">— Sélectionner un département —</option>
                    </select>
                </div>

                {{-- Commune : filtrée par le département choisi (JS).
                     Choisir une commune = rattachement à la commune → le département
                     est effacé (un seul niveau de rattachement à la fois). --}}
                <div class="field-group">
                    <label>Commune</label>
                    <select name="commune_id" id="infra-commune" onchange="choisirCommuneInfra()">
                        <option value="">— Sélectionner une commune —</option>
                    </select>
                </div>

                {{-- Localités couvertes : multi-select filtré par la commune choisie (JS).
                     La population couverte = somme automatique des populations des localités cochées. --}}
                <div class="field-group">
                    <label>Localités couvertes <span style="font-weight:400; color:var(--text-muted);">(maintenir Ctrl pour en choisir plusieurs)</span></label>
                    <select name="localites[]" id="infra-localites" multiple size="5" onchange="majPopulationCouverte()"></select>
                    {{-- Affichage automatique de la population couverte totale + personnalisation optionnelle --}}
                    <div style="margin-top:6px; font-size:12px; color:var(--text-dim); background:var(--surface2); border-radius:8px; padding:8px 12px;">
                        <i class="fa-solid fa-users" style="margin-right:4px; color:var(--primary);"></i>
                        Population couverte : <strong id="infra-pop-couverte">0</strong> habitants
                        <span style="color:var(--text-muted);">(somme des localités sélectionnées)</span>

                        {{-- Personnalisation : si l'utilisateur connaît la population réellement desservie,
                             il peut saisir une valeur différente ; elle sera répartie proportionnellement --}}
                        <label style="display:flex; align-items:center; gap:6px; margin-top:6px; cursor:pointer; font-weight:600;">
                            <input type="checkbox" onchange="togglePopOverrideInfra()"> Personnaliser cette valeur
                        </label>
                        <input type="number" name="population_couverte" id="infra-pop-override" min="0"
                            placeholder="Population réellement desservie"
                            style="display:none; width:100%; margin-top:6px; padding:6px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface); color:var(--text);">
                    </div>
                </div>

                <div class="field-group">
                    <label>Secteur *</label>
                    <select name="secteur_id" id="infra-secteur" required onchange="majValeursIndicateursInfra()">
                        <option value="">Sélectionner un secteur</option>
                        @foreach($allSecteurs as $s)
                            <option value="{{ $s->id }}">{{ $s->nom }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ═══ VALEURS DES INDICATEURS DU SECTEUR CHOISI ═══
                     Une fois le secteur sélectionné, le JS affiche ici un champ
                     "valeur" pour CHAQUE indicateur du secteur. Ces valeurs sont
                     enregistrées dans la table pivot indicateur_infrastructure. --}}
                <div id="infra-indicateurs" style="padding-top:6px;"></div>
                <div class="field-row">
                    <div class="field-group"><label>Date de création</label><input type="date" name="date_creation"></div>
                    <div class="field-group">
                        <label>État du lieu</label>
                        {{-- Énumération identique à la colonne etat_lieu (enum) :
                             Bon / Moyen / Mauvais / Hors_service --}}
                        <select name="etat_lieu" id="infra-etatl">
                            <option value="">— Sélectionner un état —</option>
                            @foreach(['Bon','Moyen','Mauvais','Hors_service'] as $etat)
                                <option value="{{ $etat }}">{{ str_replace('_',' ',$etat) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-group"><label>Latitude</label><input type="text" name="latitude"></div>
                    <div class="field-group"><label>Longitude</label><input type="text" name="longitude"></div>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-infrastructure')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL D'ÉDITION GÉNÉRIQUE ═══
     Une seule modal pour les 6 entités : le contenu des champs est généré
     par JavaScript selon le schéma de l'entité (EDIT_SCHEMAS) et prérempli
     avec les valeurs de la ligne (data-values du bouton Modifier). --}}
<div class="modal-overlay" id="modal-edit">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3 id="edit-title">Modifier</h3>
            <button class="modal-close" onclick="closeFormModal('modal-edit')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            {{-- Le formulaire soumet en PUT via le spoofing _method (HTML ne connaît que GET/POST) --}}
            <form id="edit-form" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div id="edit-fields"></div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn-cancel" onclick="closeFormModal('modal-edit')">Annuler</button>
                    <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL DE CONSULTATION (œil) ═══
     Ouverte par les boutons "œil" (btn-voir) : affiche en lecture seule
     tous les détails d'une ligne sous forme de paires "Libellé : valeur". --}}
<div class="modal-overlay" id="modal-consulter">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3 id="voir-titre">Consulter</h3>
            <button type="button" class="modal-close" onclick="closeFormModal('modal-consulter')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            <div id="voir-corps" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:12px 20px;"></div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:24px;">
                <button type="button" class="btn-cancel" onclick="closeFormModal('modal-consulter')">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL DE CONFIRMATION DE SUPPRESSION ═══
     Ouverte par le JS quand on clique sur un bouton "Supprimer" :
     elle interroge la route donnees.impact pour afficher la liste
     exacte de ce qui sera détruit (arbre territorial en cascade,
     infrastructures, actualités, documents...) avant validation. --}}
<div class="modal-overlay" id="modal-suppression">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3 id="del-titre">Confirmer la suppression</h3>
            <button type="button" class="modal-close" onclick="fermerModalSuppression()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-card-body">
            {{-- Phrase d'introduction avec le nom de l'élément visé --}}
            <p id="del-intro" style="margin:0 0 14px 0; font-size:15px;"></p>

            {{-- Liste remplie par le JS : "3 département(s) : D1, D2, D3…" --}}
            <ul id="del-liste" style="margin:0; padding-left:20px; line-height:2;"></ul>

            {{-- Message d'avertissement (ex. localité : les infrastructures restent) --}}
            <p id="del-note" style="display:none; margin-top:12px; padding:10px 12px; background:#fff8e1; border-left:4px solid #f0a500; border-radius:4px; font-size:13px;"></p>

            {{-- Message rouge + bouton désactivé si la suppression est impossible (secteur utilisé) --}}
            <p id="del-blocage" style="display:none; margin-top:12px; padding:10px 12px; background:#fdecea; border-left:4px solid #c0392b; border-radius:4px; font-size:13px; color:#c0392b; font-weight:600;"></p>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" class="btn-cancel" onclick="fermerModalSuppression()">Annuler</button>
                <button type="button" id="btn-confirmer-suppression" class="btn-submit" style="background:#c0392b;" onclick="validerSuppression()">
                    <i class="fa-solid fa-trash"></i> Oui, supprimer
                </button>
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

    {{-- ═══ CONFIRMATION DE SUPPRESSION AVEC ANALYSE D'IMPACT ═══
         1. Le clic sur "Supprimer" est intercepté (preventDefault)
         2. On ouvre la modal et on interroge la route donnees.impact
         3. On affiche la liste exacte de ce qui sera détruit
         4. "Oui, supprimer" soumet le formulaire mémorisé (PUT/DELETE) --}}
    let formulaireSuppressionEnCours = null;

    async function confirmerSuppression(evenement, type, id, nom) {
        // On bloque la soumission directe du formulaire : la décision
        // sera prise dans l'écran de confirmation, pas ici.
        evenement.preventDefault();
        formulaireSuppressionEnCours = evenement.target;

        const intro   = document.getElementById('del-intro');
        const liste   = document.getElementById('del-liste');
        const note    = document.getElementById('del-note');
        const blocage = document.getElementById('del-blocage');
        const btnOk   = document.getElementById('btn-confirmer-suppression');

        intro.textContent = `Vous êtes sur le point de supprimer « ${nom} ».`;
        liste.innerHTML = '<li>Calcul des conséquences en cours…</li>';
        note.style.display = 'none';
        blocage.style.display = 'none';
        btnOk.disabled = false;
        openFormModal('modal-suppression');

        try {
            // Appel au contrôleur qui calcule tout ce qui sera détruit en cascade
            const reponse = await fetch(`{{ url('Donnees/Impact') }}/${type}/${id}`);
            if (!reponse.ok) throw new Error('réponse non OK');
            const data = await reponse.json();

            liste.innerHTML = '';
            if (!data.elements.length) {
                // Aucune conséquence : suppression simple et sans danger
                liste.innerHTML = '<li>Aucun élément lié — cette suppression ne détruira rien d\'autre.</li>';
            }
            data.elements.forEach(el => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${el.nombre}</strong> ${escHtml(el.label)}` +
                    (el.exemples ? ` <span style="color:var(--text-muted); font-size:13px;">(${escHtml(el.exemples)})</span>` : '');
                liste.appendChild(li);
            });

            // Avertissement informatif (ex. localité : les infrastructures restent)
            if (data.message && !data.bloque) {
                note.textContent = data.message;
                note.style.display = 'block';
            }
            // Suppression impossible (ex. secteur encore utilisé par des infrastructures)
            if (data.bloque) {
                blocage.textContent = data.message;
                blocage.style.display = 'block';
                btnOk.disabled = true;
            }
        } catch {
            // Si l'analyse échoue on garde une confirmation minimale plutôt que de bloquer l'utilisateur
            liste.innerHTML = '<li>Détails indisponibles — cette suppression est définitive.</li>';
        }
    }

    function validerSuppression() {
        // .submit() natif ignore le gestionnaire onsubmit -> pas de nouvelle confirmation
        if (formulaireSuppressionEnCours) formulaireSuppressionEnCours.submit();
    }

    function fermerModalSuppression() {
        closeFormModal('modal-suppression');
        formulaireSuppressionEnCours = null;
    }

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

    {{-- ═══ DONNÉES SÉRIALISÉES POUR LES CASCADES JS ═══
         Régions, départements, communes, localités et secteurs avec leurs liens
         de parenté, pour alimenter les listes déroulantes sans recharger la page.
         Les tableaux sont précalculés dans un bloc PHP dédié car la sérialisation
         directe d'expressions complexes est tronquée par le compilateur. --}}
    @php
        $jsRegions = $allRegions->map(fn ($r) => [
            'id'  => $r->id,
            'nom' => $r->nom,
        ])->values()->all();

        $jsDepartements = $allDepartements->map(fn ($d) => [
            'id'        => $d->id,
            'nom'       => $d->nom,
            'region_id' => $d->region_id,
        ])->values()->all();

        $jsCommunes = $allCommunes->map(fn ($c) => [
            'id'             => $c->id,
            'nom'            => $c->nom,
            'departement_id' => $c->departement_id,
        ])->values()->all();

        $jsLocalites = $allLocalites->map(fn ($l) => [
            'id'         => $l->id,
            'nom'        => $l->nom,
            'commune_id' => $l->commune_id,
            'pop'        => (int) $l->taille_population,
        ])->values()->all();

        $jsSecteurs = $allSecteurs->map(fn ($s) => [
            'id'  => $s->id,
            'nom' => $s->nom,
        ])->values()->all();

        $jsIndicateurs = $allIndicateurs->map(fn ($ind) => [
            'id'              => $ind->id,
            'nom'             => $ind->nom_indicateur,
            'unites'          => $ind->unites,
            'description'     => $ind->description,
            'secteur_id'      => $ind->secteur_id,
        ])->values()->all();
    @endphp

    const DB_REGIONS      = @json($jsRegions);
    const DB_DEPARTEMENTS = @json($jsDepartements);
    const DB_COMMUNES     = @json($jsCommunes);
    const DB_LOCALITES    = @json($jsLocalites);
    const DB_SECTEURS     = @json($jsSecteurs);
    const DB_INDICATEURS  = @json($jsIndicateurs);

    // Valeurs possibles de l'énumération "État du lieu" d'une infrastructure
    // (identiques à la contrainte CHECK/enum de la colonne etat_lieu)
    const ETATS_LIEU = ['Bon', 'Moyen', 'Mauvais', 'Hors_service'];

    // Formatte un état du lieu pour l'affichage (remplace l'underscore par un espace)
    function formatEtatLieu(v) {
        return v === undefined || v === null || v === '' ? '' : String(v).replace(/_/g, ' ');
    }

    // ══════════════════════════════════════════════════════════
    //  OUTILS COMMUNS
    // ══════════════════════════════════════════════════════════

    // Échappe une valeur pour l'injecter en toute sécurité dans du HTML généré
    function escHtml(valeur) {
        return String(valeur ?? '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // ═══ MESSAGE SOMME HOMMES + FEMMES vs POPULATION ═══
    // Règle métier : la somme (nbre_homme + nbre_femme) ne doit jamais dépasser
    // taille_population. Affiche un bandeau d'avertissement dès que cela est le cas.
    function majMessageSommePop(scope) {
        // scope peut être le formulaire lui-même ou un champ à l'intérieur
        const form = scope && scope.tagName === 'FORM' ? scope : (scope ? scope.closest('form') : null);
        if (!form) return;
        const box = form.querySelector('[data-msg-somme]');
        if (!box) return;

        const valeur = (nom) => parseInt((form.querySelector('[name="' + nom + '"]') || {}).value || '0', 10) || 0;
        const pop   = valeur('taille_population');
        const somme = valeur('nbre_homme') + valeur('nbre_femme');
        const depasse = somme > pop;

        box.style.display = depasse ? 'block' : 'none';
        if (depasse) {
            box.querySelector('[data-somme]').textContent = somme.toLocaleString('fr-FR');
            box.querySelector('[data-pop]').textContent  = pop.toLocaleString('fr-FR');
        }
    }

    // Déclencheur global : à chaque saisie dans ces champs, on met à jour le message
    document.addEventListener('input', function(e) {
        const nom = e.target && e.target.name;
        if (nom === 'taille_population' || nom === 'nbre_homme' || nom === 'nbre_femme') {
            majMessageSommePop(e.target.closest('form'));
        }
    });

    // ══════════════════════════════════════════════════════════
    //  1. FORMULAIRE CRÉER INFRASTRUCTURE — cascade Région → Département → Commune → Localités
    // ══════════════════════════════════════════════════════════

    // Région changée → on reconstruit la liste des départements (filtrée)
    function majDepartementsInfra() {
        const regionId = document.getElementById('infra-region').value;
        const selDept = document.getElementById('infra-departement');

        // Si aucune région choisie, on affiche TOUS les départements
        const depts = regionId === ''
            ? DB_DEPARTEMENTS
            : DB_DEPARTEMENTS.filter(d => String(d.region_id) === regionId);

        selDept.innerHTML = '<option value="">— Sélectionner un département —</option>';
        depts.forEach(function(d) {
            selDept.insertAdjacentHTML('beforeend', `<option value="${d.id}">${escHtml(d.nom)}</option>`);
        });

        majCommunesInfra(); // vide aussi les communes/localités
    }

    // Département changé → on reconstruit la liste des communes (filtrée)
    function majCommunesInfra() {
        const deptId = document.getElementById('infra-departement').value;
        const selCommune = document.getElementById('infra-commune');

        selCommune.innerHTML = '<option value="">— Sélectionner une commune —</option>';
        DB_COMMUNES.filter(c => String(c.departement_id) === deptId).forEach(function(c) {
            selCommune.insertAdjacentHTML('beforeend', `<option value="${c.id}">${escHtml(c.nom)}</option>`);
        });

        majLocalitesInfra(); // vide aussi les localités et remet la population à zéro
    }

    // ═══ CHOIX D'UNE COMMUNE (rattachement à la commune) ═══
    // On efface le département (un seul niveau de rattachement) et les localités,
    // puis on reconstruit les localités de cette commune.
    function choisirCommuneInfra() {
        const selDepartement = document.getElementById('infra-departement');
        if (selDepartement) selDepartement.value = ''; // le département ne doit plus être retenu

        const selLoc = document.getElementById('infra-localites');
        if (selLoc) selLoc.value = []; // on repart de zéro sur les localités

        majLocalitesInfra();
    }

    // Commune changée → on reconstruit la liste des localités couvertes (filtrée)
    function majLocalitesInfra() {
        const communeId = document.getElementById('infra-commune').value;
        const selLoc = document.getElementById('infra-localites');

        selLoc.innerHTML = '';
        DB_LOCALITES.filter(l => String(l.commune_id) === communeId).forEach(function(l) {
            selLoc.insertAdjacentHTML('beforeend', `<option value="${l.id}">${escHtml(l.nom)}</option>`);
        });

        majPopulationCouverte(); // remise à zéro de l'affichage
    }

    // Sélection de localités changée → population couverte = SOMME des populations
    function majPopulationCouverte() {
        const selLoc = document.getElementById('infra-localites');
        let total = 0;

        Array.from(selLoc.selectedOptions).forEach(function(opt) {
            const loc = DB_LOCALITES.find(l => String(l.id) === opt.value);
            if (loc) total += Number(loc.pop || 0);
        });

        document.getElementById('infra-pop-couverte').textContent = total.toLocaleString('fr-FR');
    }

    // Case "Personnaliser cette valeur" → affiche/masque le champ de saisie manuelle.
    // À l'ouverture, le champ est pré-rempli avec la somme calculée automatiquement.
    function togglePopOverrideInfra() {
        const input = document.getElementById('infra-pop-override');
        const visible = input.style.display !== 'none';
        if (visible) {
            input.style.display = 'none';
            input.value = '';
        } else {
            // Pré-remplissage avec la somme automatique courante (sans espaces)
            input.value = document.getElementById('infra-pop-couverte').textContent.replace(/\s/g, '');
            input.style.display = 'block';
        }
    }

    // ══════════════════════════════════════════════════════════
    //  VALEURS DES INDICATEURS — formulaires Infrastructure
    // ══════════════════════════════════════════════════════════

    // Retourne les indicateurs d'un secteur donné (id)
    function indicateursDuSecteur(secteurId) {
        if (!secteurId) return [];
        return DB_INDICATEURS.filter(i => String(i.secteur_id) === String(secteurId));
    }

    // Construit une ligne "valeur" pour un indicateur dans le formulaire
    // d'une infrastructure (création). name sous la forme :
    //   indicateurs_valeurs[<id_indicateur>]
    function ligneValeurIndicateur(ind, valeur = '') {
        return `<div class="field-group" style="margin-bottom:10px;">
            <label>${escHtml(ind.nom)}${ind.unites ? ' <span style="font-weight:400; color:var(--text-muted);">(' + escHtml(ind.unites) + ')</span>' : ''}</label>
            <input type="number" step="any" min="0" name="indicateurs_valeurs[${ind.id}]"
                value="${valeur === '' ? '' : escHtml(valeur)}"
                placeholder="Valeur ${ind.unites ? '(' + escHtml(ind.unites) + ')' : ''}">
            ${ind.description ? `<div style="font-size:11px; color:var(--text-muted); margin-top:3px;">${escHtml(ind.description)}</div>` : ''}
        </div>`;
    }

    // Au changement de secteur (formulaire Ajouter une Infrastructure) :
    // affiche un champ "valeur" par indicateur du secteur choisi.
    function majValeursIndicateursInfra() {
        const sel = document.getElementById('infra-secteur');
        const cible = document.getElementById('infra-indicateurs');
        cible.innerHTML = ''; // vider les valeurs du secteur précédent

        const indicateurs = indicateursDuSecteur(sel.value);
        if (indicateurs.length === 0) return; // aucun indicateur : rien à afficher

        let html = '<div style="padding-top:6px; border-top:1px solid var(--border);">';
        html += '<label style="font-size:12px; font-weight:700; color:var(--text); margin-bottom:8px; display:block;">' +
            '<i class="fa-solid fa-clipboard-list" style="color:var(--primary); margin-right:5px;"></i>' +
            'Valeurs des indicateurs <span style="font-weight:400; color:var(--text-muted);">(facultatif)</span></label>';
        indicateurs.forEach(ind => { html += ligneValeurIndicateur(ind); });
        html += '</div>';
        cible.innerHTML = html;
    }

    // ══════════════════════════════════════════════════════════
    //  INDICATEURS — formulaire Ajouter un Secteur
    // ══════════════════════════════════════════════════════════

    // Ajoute une ligne d'indicateur (nom, unité, description) dans la liste
    // dynamique du formulaire de création d'un secteur.
    function ajouterLigneIndicateur() {
        const conteneur = document.getElementById('indicateurs-liste');
        const idx = conteneur.querySelectorAll('.ligne-indicateur').length;
        const div = document.createElement('div');
        div.className = 'ligne-indicateur';
        div.style.cssText = 'background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:12px; margin-bottom:10px;';
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Indicateur ${idx + 1}</span>
                <button type="button" onclick="supprimerLigneIndicateur(this)" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px;" title="Retirer"><i class="fa-solid fa-trash"></i></button>
            </div>
            <div class="field-row">
                <div class="field-group" style="margin-bottom:8px;"><label>Nom de l'indicateur *</label><input type="text" name="indicateurs[${idx}][nom_indicateur]"></div>
                <div class="field-group" style="margin-bottom:8px;"><label>Unité</label><input type="text" name="indicateurs[${idx}][unites]"></div>
            </div>
            <div class="field-group" style="margin-bottom:0;"><label>Description</label><textarea name="indicateurs[${idx}][description]" rows="1"></textarea></div>
        `;
        conteneur.appendChild(div);
    }

    // Retire une ligne d'indicateur du formulaire de création d'un secteur
    function supprimerLigneIndicateur(btn) {
        btn.closest('.ligne-indicateur').remove();
    }

    // ══════════════════════════════════════════════════════════
    //  2. FORMULAIRE AJOUTER LOCALITÉ — filtres Région → Département pour trouver la Commune
    // ══════════════════════════════════════════════════════════

    // Région changée (formulaire localité) → reconstruit la liste des départements
    function majDepartementsLoc() {
        const regionId = document.getElementById('loc-region') ? document.getElementById('loc-region').value : '';
        const selDept = document.getElementById('loc-departement');

        const depts = regionId === ''
            ? DB_DEPARTEMENTS
            : DB_DEPARTEMENTS.filter(d => String(d.region_id) === regionId);

        // Réinitialise le département ET la commune à chaque changement de région
        selDept.innerHTML = '<option value="">— Tous les départements —</option>';
        depts.forEach(function(d) {
            selDept.insertAdjacentHTML('beforeend', `<option value="${d.id}">${escHtml(d.nom)}</option>`);
        });

        majCommunesLoc(); // met à jour les communes en conséquence
    }

    // Département changé (formulaire localité) → reconstruit la liste des communes.
    // La commune dépend des DEUX filtres : elle n'affiche que les communes dont le
    // département correspond à la région ET au département sélectionnés.
    function majCommunesLoc() {
        const regionId = document.getElementById('loc-region').value;
        const deptId = document.getElementById('loc-departement').value;
        const selCommune = document.getElementById('loc-commune');

        // Ensemble des départements autorisés par le filtre Région (ou tous si non filtré)
        const deptsAutorises = regionId === ''
            ? DB_DEPARTEMENTS
            : DB_DEPARTEMENTS.filter(d => String(d.region_id) === regionId);
        const idsDepts = new Set(deptsAutorises.map(d => String(d.id)));

        // Les communes affichées respectent les deux filtres :
        // - le département précis s'il est choisi (et qu'il appartient à la région),
        // - sinon toutes les communes des départements de la région filtrée.
        let communes = DB_COMMUNES;
        if (deptId !== '' && idsDepts.has(deptId)) {
            communes = DB_COMMUNES.filter(c => String(c.departement_id) === deptId);
        } else if (regionId !== '') {
            communes = DB_COMMUNES.filter(c => idsDepts.has(String(c.departement_id)));
        }

        selCommune.innerHTML = '<option value="">Sélectionner une commune</option>';
        communes.forEach(function(c) {
            selCommune.insertAdjacentHTML('beforeend', `<option value="${c.id}">${escHtml(c.nom)}</option>`);
        });
    }

    // ═══ FORMULAIRE AJOUTER COMMUNE — filtre Région → Département ═══
    function majDepartementsCom() {
        const regionId = document.getElementById('com-region') ? document.getElementById('com-region').value : '';
        const selDept = document.getElementById('com-departement');

        const depts = regionId === ''
            ? DB_DEPARTEMENTS
            : DB_DEPARTEMENTS.filter(d => String(d.region_id) === regionId);

        selDept.innerHTML = '<option value="">Sélectionner un département</option>';
        depts.forEach(function(d) {
            selDept.insertAdjacentHTML('beforeend', `<option value="${d.id}">${escHtml(d.nom)}</option>`);
        });
    }

    // ══════════════════════════════════════════════════════════
    //  3. MODAL D'ÉDITION GÉNÉRIQUE
    // ══════════════════════════════════════════════════════════

    // Petits constructeurs de champs HTML pour la modal d'édition
    const F = {
        txt: (n, l, v = '', req = false) =>
            `<div class="field-group"><label>${escHtml(l)}</label><input type="text" name="${n}" value="${escHtml(v)}"${req ? ' required' : ''}></div>`,
        num: (n, l, v = '') =>
            `<div class="field-group"><label>${escHtml(l)}</label><input type="number" step="any" name="${n}" value="${escHtml(v)}"></div>`,
        date: (n, l, v = '') =>
            `<div class="field-group"><label>${escHtml(l)}</label><input type="date" name="${n}" value="${escHtml(v)}"></div>`,
        area: (n, l, v = '') =>
            `<div class="field-group"><label>${escHtml(l)}</label><textarea name="${n}" rows="2">${escHtml(v)}</textarea></div>`,
        sel: (n, l, options, v = '', extra = '') =>
            `<div class="field-group"><label>${escHtml(l)}</label><select name="${n}" ${extra}>` +
            options.map(o => `<option value="${o.v}" ${String(o.v) === String(v) ? 'selected' : ''}>${escHtml(o.l)}</option>`).join('') +
            `</select></div>`,
    };

    // Champs statistiques communs aux entités territoriales
    function champsStats(v) {
        return [
            F.num('superficie', 'Superficie (km²)', v.superficie),
            F.num('taille_population', 'Taille de la population', v.taille_population),
            F.num('nbre_menage', 'Nombre de ménages', v.nbre_menage),
            F.num('nbre_homme', "Nombre d'hommes", v.nbre_homme),
            F.num('nbre_femme', 'Nombre de femmes', v.nbre_femme),
            F.txt('latitude', 'Latitude', v.latitude),
            F.txt('longitude', 'Longitude', v.longitude),
            // Message d'aide : la somme hommes + femmes ne doit pas dépasser la population
            `<div data-msg-somme style="display:none; grid-column:1/-1; padding:10px 12px; border-radius:8px; background:#fdecea; border-left:4px solid #c0392b; color:#c0392b; font-size:12px; font-weight:600;">
                <i class="fa-solid fa-triangle-exclamation"></i> La somme des hommes et des femmes (<span data-somme>0</span>) ne doit pas être supérieure à la population (<span data-pop>0</span>).
            </div>`,
        ];
    }

    // ── Cascade spécifique à l'ÉDITION d'une infrastructure ──
    // (identiques aux fonctions de création mais ciblent les ids "ed-*")

    function majDepartementsInfraEd(valeurs) {
        const selDept = document.getElementById('ed-departement');
        const regionId = ''; // pas de filtre région en édition simple : tous les départements
        selDept.innerHTML = '<option value="">— Aucun département (si commune choisie) —</option>';
        DB_DEPARTEMENTS.forEach(function(d) {
            selDept.insertAdjacentHTML('beforeend', `<option value="${d.id}" ${String(d.id) === String(valeurs.departement_id) ? 'selected' : ''}>${escHtml(d.nom)}</option>`);
        });
        majCommunesInfraEd(valeurs);
    }

    function majCommunesInfraEd(valeurs) {
        const selDept = document.getElementById('ed-departement');
        const selCommune = document.getElementById('ed-commune');
        const deptChoisi = selDept.value;

        selCommune.innerHTML = '<option value="">— Aucune commune (si département choisi) —</option>';
        // Si un département est sélectionné, seules SES communes sont listées ;
        // sinon toutes les communes restent accessibles.
        DB_COMMUNES
            .filter(c => deptChoisi === '' || String(c.departement_id) === deptChoisi)
            .forEach(function(c) {
                selCommune.insertAdjacentHTML('beforeend', `<option value="${c.id}" ${String(c.id) === String(valeurs.commune_id) ? 'selected' : ''}>${escHtml(c.nom)}</option>`);
            });

        majLocalitesInfraEd(valeurs);
    }

    function majLocalitesInfraEd(valeurs) {
        const selLoc = document.getElementById('ed-localites');
        const communeId = document.getElementById('ed-commune').value;
        const idsCouvertes = (valeurs.localites || []).map(String);

        selLoc.innerHTML = '';
        DB_LOCALITES.filter(l => String(l.commune_id) === communeId).forEach(function(l) {
            const cochee = idsCouvertes.includes(String(l.id)) ? ' selected' : '';
            selLoc.insertAdjacentHTML('beforeend', `<option value="${l.id}"${cochee}>${escHtml(l.nom)}</option>`);
        });

        majPopulationCouverteEd();
    }

    function majPopulationCouverteEd() {
        const selLoc = document.getElementById('ed-localites');
        let total = 0;
        Array.from(selLoc.selectedOptions).forEach(function(opt) {
            const loc = DB_LOCALITES.find(l => String(l.id) === opt.value);
            if (loc) total += Number(loc.pop || 0);
        });
        const affichage = document.getElementById('ed-pop-couverte');
        if (affichage) affichage.textContent = total.toLocaleString('fr-FR');
    }

    // Choix d'une commune en édition : on efface le département (un seul niveau
    // de rattachement) puis on reconstruit les localités de cette commune.
    function choisirCommuneInfraEd() {
        const selDepartement = document.getElementById('ed-departement');
        if (selDepartement) selDepartement.value = '';
        const selLoc = document.getElementById('ed-localites');
        if (selLoc) selLoc.value = [];
        majLocalitesInfraEd(valeursEditCourantes);
    }

    function togglePopOverrideEd() {
        const input = document.getElementById('ed-pop-input');
        if (!input) return;
        if (input.style.display === 'none' || input.style.display === '') {
            input.value = document.getElementById('ed-pop-couverte').textContent.replace(/\s/g, '');
            input.style.display = 'block';
        } else {
            input.style.display = 'none';
            input.value = '';
        }
    }

    // Construction des champs du formulaire d'édition pour UNE infrastructure
    function champsInfrastructureEdit(v) {
        const popActuelle = Number(v.population_couverte || 0);

        return [
            F.txt('nom', 'Nom *', v.nom, true),
            F.txt('type_infrastructure', "Type d'infrastructure", v.type_infrastructure),
            F.area('description', 'Description', v.description),

            // Territoire : les deux listes restent vides par défaut ;
            // la règle métier impose département OU commune (jamais les deux).
            F.sel('departement_id', 'Département',
                [{ v: '', l: '— Aucun département —' }].concat(DB_DEPARTEMENTS.map(d => ({ v: d.id, l: d.nom }))),
                v.departement_id, 'id="ed-departement" onchange="majCommunesInfraEd(valeursEditCourantes)"'),
            F.sel('commune_id', 'Commune',
                [{ v: '', l: '— Aucune commune —' }].concat(DB_COMMUNES.filter(c => !v.departement_id || String(c.departement_id) === String(v.departement_id)).map(c => ({ v: c.id, l: c.nom }))),
                v.commune_id, 'id="ed-commune" onchange="choisirCommuneInfraEd()"'),

            // Localités couvertes : multi-select filtré par la commune choisie
            `<div class="field-group"><label>Localités couvertes <span style="font-weight:400; color:var(--text-muted);">(Ctrl+clic pour plusieurs)</span></label>
                <select name="localites[]" id="ed-localites" multiple size="5" onchange="majPopulationCouverteEd()">
                    ${DB_LOCALITES.filter(l => String(l.commune_id) === String(v.commune_id || ''))
                        .map(l => `<option value="${l.id}" ${(v.localites || []).map(String).includes(String(l.id)) ? 'selected' : ''}>${escHtml(l.nom)}</option>`).join('')}
                </select>
                <div style="margin-top:6px; font-size:12px; color:var(--text-dim); background:var(--surface2); border-radius:8px; padding:8px 12px;">
                    Population couverte : <strong id="ed-pop-couverte">${popActuelle.toLocaleString('fr-FR')}</strong> hab.
                    <label style="display:flex; align-items:center; gap:6px; margin-top:6px; cursor:pointer; font-weight:600;">
                        <input type="checkbox" onchange="togglePopOverrideEd()"> Personnaliser cette valeur
                    </label>
                    <input type="number" name="population_couverte" id="ed-pop-input" min="0" placeholder="Population réellement desservie"
                        style="display:none; width:100%; margin-top:6px; padding:6px 10px; font-size:12px; border:1px solid var(--border); border-radius:7px; background:var(--surface); color:var(--text);">
                </div>
            </div>`,

            F.sel('secteur_id', 'Secteur *',
                [{ v: '', l: '— Sélectionner un secteur —' }].concat(DB_SECTEURS.map(s => ({ v: s.id, l: s.nom }))),
                v.secteur_id, 'id="ed-secteur" onchange="majValeursIndicateursEdit()"'),
            `<div id="ed-indicateurs"></div>`,
            F.date('date_creation', 'Date de création', v.date_creation),
            F.sel('etat_lieu', 'État du lieu',
                [{ v: '', l: '— Sélectionner un état —' }].concat(ETATS_LIEU.map(e => ({ v: e, l: formatEtatLieu(e) }))),
                v.etat_lieu, ''),
            F.txt('latitude', 'Latitude', v.latitude),
            F.txt('longitude', 'Longitude', v.longitude),
        ].join('');
    }

    // ══════════════════════════════════════════════════════════
    //  ÉDITION D'UN SECTEUR — ses indicateurs
    // ══════════════════════════════════════════════════════════

    // Construit les champs d'édition d'un secteur : nom + liste des indicateurs
    // existants (renommage + case à cocher pour supprimer) + zone d'ajout.
    function champsSecteurEdit(v) {
        const indicateursExistant = (v.indicateurs || []).map(function(ind) {
            return `
                <div class="ligne-indicateur-edit" style="background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:12px; margin-bottom:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:11px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Indicateur existant</span>
                        <label style="display:flex; align-items:center; gap:5px; font-size:12px; color:var(--red); cursor:pointer; font-weight:600;">
                            <input type="checkbox" name="indicateurs_supprimer[]" value="${ind.id}"> Supprimer
                        </label>
                    </div>
                    <div class="field-row">
                        <div class="field-group" style="margin-bottom:8px;"><label>Nom de l'indicateur *</label><input type="text" name="indicateurs[${ind.id}][nom_indicateur]" value="${escHtml(ind.nom_indicateur || '')}"></div>
                        <div class="field-group" style="margin-bottom:8px;"><label>Unité</label><input type="text" name="indicateurs[${ind.id}][unites]" value="${escHtml(ind.unites || '')}"></div>
                    </div>
                    <div class="field-group" style="margin-bottom:0;"><label>Description</label><textarea name="indicateurs[${ind.id}][description]" rows="1">${escHtml(ind.description || '')}</textarea></div>
                </div>`;
        }).join('');

        return `
            ${F.txt('nom', 'Nom *', v.nom, true)}
            <div style="margin-top:6px; padding-top:14px; border-top:1px solid var(--border);">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                    <label style="font-size:12px; font-weight:700; color:var(--text);">Indicateurs du secteur <span style="font-weight:400; color:var(--text-muted);">(cochez pour supprimer)</span></label>
                    <button type="button" onclick="ajouterLigneIndicateurEdit()" style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; background:var(--primary); color:#fff; border:none; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer;">
                        <i class="fa-solid fa-plus"></i> Ajouter
                    </button>
                </div>
                <div id="indicateurs-liste-edit">${indicateursExistant}</div>
                <div style="font-size:11px; color:var(--text-muted); margin-top:8px;">
                    <i class="fa-solid fa-circle-info"></i> Les indicateurs supprimés entraînent la perte des valeurs saisies sur les infrastructures qui les utilisaient.
                </div>
            </div>`;
    }

    // Ajoute une ligne "nouvel indicateur" dans l'édition d'un secteur
    function ajouterLigneIndicateurEdit() {
        const conteneur = document.getElementById('indicateurs-liste-edit');
        const idx = conteneur.querySelectorAll('.ligne-indicateur-nouveau').length;
        const div = document.createElement('div');
        div.className = 'ligne-indicateur-nouveau';
        div.style.cssText = 'background:var(--primary-lt); border:1px dashed var(--primary); border-radius:10px; padding:12px; margin-bottom:10px;';
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase;">Nouvel indicateur</span>
                <button type="button" onclick="supprimerLigneIndicateurEdit(this)" style="background:none; border:none; color:var(--red); cursor:pointer; font-size:13px;" title="Retirer"><i class="fa-solid fa-trash"></i></button>
            </div>
            <div class="field-row">
                <div class="field-group" style="margin-bottom:8px;"><label>Nom de l'indicateur *</label><input type="text" name="indicateurs_nouveaux[${idx}][nom_indicateur]"></div>
                <div class="field-group" style="margin-bottom:8px;"><label>Unité</label><input type="text" name="indicateurs_nouveaux[${idx}][unites]"></div>
            </div>
            <div class="field-group" style="margin-bottom:0;"><label>Description</label><textarea name="indicateurs_nouveaux[${idx}][description]" rows="1"></textarea></div>
        `;
        conteneur.appendChild(div);
    }

    // Retire une ligne "nouvel indicateur" de l'édition d'un secteur
    function supprimerLigneIndicateurEdit(btn) {
        btn.closest('.ligne-indicateur-nouveau').remove();
    }

    // Au changement de secteur (édition d'une infrastructure) : affiche les champs
    // "valeur" des indicateurs du secteur, pré-remplis avec les valeurs existantes.
    function majValeursIndicateursEdit() {
        const sel = document.getElementById('ed-secteur');
        const cible = document.getElementById('ed-indicateurs');
        const valeurs = (valeursEditCourantes.indicateurs_valeurs) || {};
        cible.innerHTML = '';

        const indicateurs = indicateursDuSecteur(sel.value);
        if (indicateurs.length === 0) return;

        let html = '<div style="padding-top:6px; border-top:1px solid var(--border); margin-top:6px;">';
        html += '<label style="font-size:12px; font-weight:700; color:var(--text); margin-bottom:8px; display:block;">' +
            '<i class="fa-solid fa-clipboard-list" style="color:var(--primary); margin-right:5px;"></i>' +
            'Valeurs des indicateurs</label>';
        indicateurs.forEach(function(ind) {
            const val = valeurs[ind.id] !== undefined && valeurs[ind.id] !== null ? valeurs[ind.id] : '';
            html += ligneValeurIndicateur(ind, val);
        });
        html += '</div>';
        cible.innerHTML = html;
    }

    // Schéma d'édition de chaque entité : titre, URL de mise à jour et champs
    const EDIT_SCHEMAS = {
        region: {
            titre: 'Modifier la région',
            url: id => `/Donnees/Region/${id}`,
            champs: v => [
                F.txt('nom', 'Nom *', v.nom, true),
                F.num('nbre_infrastructure', "Nombre d'infrastructures", v.nbre_infrastructure),
                ...champsStats(v),
            ],
        },
        departement: {
            titre: 'Modifier le département',
            url: id => `/Donnees/Departement/${id}`,
            champs: v => [
                F.txt('nom', 'Nom *', v.nom, true),
                F.sel('region_id', 'Région *', DB_REGIONS.map(r => ({ v: r.id, l: r.nom })), v.region_id),
                ...champsStats(v),
            ],
        },
        commune: {
            titre: 'Modifier la commune',
            url: id => `/Donnees/Commune/${id}`,
            champs: v => [
                F.txt('nom', 'Nom *', v.nom, true),
                F.sel('departement_id', 'Département *', DB_DEPARTEMENTS.map(d => ({ v: d.id, l: d.nom })), v.departement_id),
                ...champsStats(v),
            ],
        },
        localite: {
            titre: 'Modifier la localité',
            url: id => `/Donnees/Localite/${id}`,
            champs: v => [
                F.txt('nom', 'Nom *', v.nom, true),
                F.sel('commune_id', 'Commune *', DB_COMMUNES.map(c => ({ v: c.id, l: c.nom })), v.commune_id),
                ...champsStats(v),
            ],
        },
        secteur: {
            titre: 'Modifier le secteur',
            url: id => `/Donnees/Secteur/${id}`,
            champs: v => [champsSecteurEdit(v)],
        },
        infrastructure: {
            titre: 'Modifier l\'infrastructure',
            url: id => `/Donnees/Infrastructure/${id}`,
            champs: v => [champsInfrastructureEdit(v)],
        },
    };

    // Valeurs de la ligne en cours d'édition : nécessaires aux cascades JS
    let valeursEditCourantes = {};

    // Ouvre la modal d'édition préremplie pour l'entité et l'id donnés
    function openEditModal(entity, id, valeurs) {
        const schema = EDIT_SCHEMAS[entity];
        if (!schema) return;

        valeursEditCourantes = valeurs || {};

        document.getElementById('edit-title').textContent = schema.titre;
        document.getElementById('edit-form').action = schema.url(id);
        document.getElementById('edit-fields').innerHTML = schema.champs(valeursEditCourantes);

        // Pour une infrastructure : pré-remplit les valeurs des indicateurs du
        // secteur sélectionné (les champs ont été générés ci-dessus).
        if (entity === 'infrastructure') {
            majValeursIndicateursEdit();
        }

        // Recalcule le message hommes+femmes vs population dès l'ouverture de l'édition
        majMessageSommePop(document.getElementById('edit-form'));

        openFormModal('modal-edit');
    }

    // Délégation de clic : tout bouton .btn-edit ouvre la modal avec ses data-*
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;

        let valeurs = {};
        try { valeurs = JSON.parse(btn.dataset.values || '{}'); } catch (err) { valeurs = {}; }

        openEditModal(btn.dataset.entity, btn.dataset.id, valeurs);
    });

    // ══════════════════════════════════════════════════════════
    //  4. CONSULTATION (œil) — affiche les détails en lecture seule
    // ══════════════════════════════════════════════════════════

    // Carte de présentation : pour chaque entité, la liste ordonnée des
    // champs à afficher [clé, libellé, résolveurOptionnel].
    const VUES_LECTURE = {
        region: [
            ['nom', 'Nom'],
            ['superficie', 'Superficie km²'],
            ['taille_population', 'Population'],
            ['nbre_menage', 'Ménages'],
            ['nbre_homme', 'Hommes'],
            ['nbre_femme', 'Femmes'],
            ['nbre_infrastructure', 'Infrastructures'],
            ['latitude', 'Latitude'],
            ['longitude', 'Longitude'],
        ],
        departement: [
            ['nom', 'Nom'],
            ['region_id', 'Région', (v) => nomParId(DB_REGIONS, v)],
            ['superficie', 'Superficie'],
            ['taille_population', 'Population'],
            ['nbre_menage', 'Ménages'],
            ['nbre_homme', 'Hommes'],
            ['nbre_femme', 'Femmes'],
            ['latitude', 'Latitude'],
            ['longitude', 'Longitude'],
        ],
        commune: [
            ['nom', 'Nom'],
            ['departement_id', 'Département', (v) => nomParId(DB_DEPARTEMENTS, v)],
            ['superficie', 'Superficie'],
            ['taille_population', 'Population'],
            ['nbre_menage', 'Ménages'],
            ['nbre_homme', 'Hommes'],
            ['nbre_femme', 'Femmes'],
            ['latitude', 'Latitude'],
            ['longitude', 'Longitude'],
        ],
        localite: [
            ['nom', 'Nom'],
            ['commune_id', 'Commune', (v) => nomParId(DB_COMMUNES, v)],
            ['superficie', 'Superficie'],
            ['taille_population', 'Population'],
            ['nbre_menage', 'Ménages'],
            ['nbre_homme', 'Hommes'],
            ['nbre_femme', 'Femmes'],
            ['latitude', 'Latitude'],
            ['longitude', 'Longitude'],
        ],
        secteur: [
            ['nom', 'Nom'],
        ],
        infrastructure: [
            ['nom', 'Nom'],
            ['type_infrastructure', 'Type'],
            ['description', 'Description'],
            ['departement_id', 'Département (inplantation)', (v) => nomParId(DB_DEPARTEMENTS, v) || '—'],
            ['commune_id', 'Commune (inplantation)', (v) => nomParId(DB_COMMUNES, v) || '—'],
            ['secteur_id', 'Secteur', (v) => nomParId(DB_SECTEURS, v) || '—'],
            ['etat_lieu', 'État des lieux', (v) => formatEtatLieu(v) || '—'],
            ['date_creation', 'Date de création'],
            ['localites', 'Localités couvertes', (arr) => (arr || []).map(id => nomParId(DB_LOCALITES, id)).filter(Boolean).join(', ') || '—'],
            ['population_couverte', 'Population couverte', (v) => v ? Number(v).toLocaleString('fr-FR') + ' hab.' : '—'],
            // Indicateurs du secteur et leur valeur mesurée pour cette infrastructure
            ['indicateurs_valeurs', 'Indicateurs / valeurs', (v) => {
                if (!v || typeof v !== 'object' || Object.keys(v).length === 0) return null;
                return Object.entries(v).map(function([id, val]) {
                    const ind = DB_INDICATEURS.find(i => String(i.id) === String(id));
                    const nom = ind ? ind.nom : '(indicateur supprimé)';
                    const unite = (ind && ind.unites) ? ' ' + ind.unites : '';
                    const valeur = (val === null || val === '' || val === undefined) ? '—' : val;
                    return nom + ' : ' + valeur + unite;
                }).join('  •  ');
            }],
            ['latitude', 'Latitude'],
            ['longitude', 'Longitude'],
        ],
    };

    // Retourne le nom d'un élément depuis une liste [ {id, nom} ], sinon vide
    function nomParId(liste, id) {
        if (!id && id !== 0) return '';
        const el = liste.find(x => String(x.id) === String(id));
        return el ? el.nom : '';
    }

    // Ouvre la modal de consultation en lecture seule
    function openConsulterModal(entity, valeurs) {
        const vue = VUES_LECTURE[entity];
        if (!vue) return;

        document.getElementById('voir-titre').textContent = 'Consulter ' + entity;
        const corps = document.getElementById('voir-corps');
        corps.innerHTML = '';

        vue.forEach(function([cle, libelle, resolveur]) {
            let valeur = valeurs !== undefined ? valeurs[cle] : undefined;
            // Valeur absente ou chaine vide -> on n'affiche pas la ligne
            if (valeur === undefined || valeur === null || valeur === '') return;

            // Résolveur optionnel : transforme un id/nombre en texte lisible
            if (typeof resolveur === 'function') valeur = resolveur(valeur) || '—';

            const bloc = document.createElement('div');
            bloc.innerHTML = `<div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">${escHtml(libelle)}</div>` +
                `<div style="font-size:14px; color:var(--text); font-weight:500; margin-top:2px;">${escHtml(String(valeur))}</div>`;
            corps.appendChild(bloc);
        });

        openFormModal('modal-consulter');
    }

    // Délégation de clic : bouton œil (.btn-voir) -> modal de consultation
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-voir');
        if (!btn) return;

        let valeurs = {};
        try { valeurs = JSON.parse(btn.dataset.values || '{}'); } catch (err) { valeurs = {}; }

        openConsulterModal(btn.dataset.entity, valeurs);
    });

    // Initialisation au chargement : remplissage initial des listes de création
    document.addEventListener('DOMContentLoaded', function() {
        // Formulaire infrastructure : liste des départements au premier rendu
        if (document.getElementById('infra-departement')) {
            majDepartementsInfra();
        }
        // Formulaire localité : liste des communes au premier rendu
        if (document.getElementById('loc-commune')) {
            majCommunesLoc();
        }
    });

</script>

@endsection
