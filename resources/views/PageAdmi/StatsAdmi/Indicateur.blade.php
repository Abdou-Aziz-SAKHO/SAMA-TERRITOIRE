@extends('AppAdmi')
@section('content')

@php
    $niveauLabels = ['region' => 'Région', 'departement' => 'Département', 'commune' => 'Commune', 'localite' => 'Localité'];
    $fmt = fn ($v) => is_numeric($v) ? number_format((float) $v, ((float) $v == (int) $v ? 0 : 1), ',', ' ') : '—';
@endphp

<div id="page-stats" class="page active">
    <div class="page-title">Secteur — Indicateurs</div>
    <div class="page-sub">Analyse détaillée d'un secteur précis · Données en temps réel</div>

    {{-- ═══ Filtres ═══ --}}
    <form method="GET" action="{{ url('/StatistiquesAdmi/Indicateur') }}" id="filters-form" class="stats-filters">
        <div class="stats-filters-inner">

            <div class="filter-group" style="flex:1.2;">
                <label for="secteur">Secteur</label>
                <select name="secteur" id="secteur" data-auto>
                    <option value="">— Choisir un secteur —</option>
                    @foreach($secteurs as $s)
                        <option value="{{ $s->id }}" {{ $secteurId == $s->id ? 'selected' : '' }}>
                            {{ $s->nom }} ({{ $s->infrastructures_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group" style="flex:1;">
                <label for="region">Région</label>
                <select name="region" id="region" data-auto>
                    <option value="">— Toutes —</option>
                    @foreach($regions as $r)
                        <option value="{{ $r->id }}" {{ $regionId == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="niveau">Niveau territorial</label>
                <select name="niveau" id="niveau" data-auto>
                    <option value="region" {{ $niveau === 'region' ? 'selected' : '' }}>Région</option>
                    <option value="departement" {{ $niveau === 'departement' ? 'selected' : '' }}>Département</option>
                    <option value="commune" {{ $niveau === 'commune' ? 'selected' : '' }}>Commune</option>
                    <option value="localite" {{ $niveau === 'localite' ? 'selected' : '' }}>Localité</option>
                </select>
            </div>

            <div class="filter-group" style="flex:1.2;">
                <label for="entite">Entité</label>
                <select name="entite" id="entite" data-current="{{ $entiteId }}">
                    <option value="">— Toutes —</option>
                </select>
            </div>

            <div class="filter-group" style="align-self:flex-end;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-filter"></i> Filtrer</button>
            </div>

        </div>
    </form>

    {{-- ═══ État vide : aucun secteur sélectionné ═══ --}}
    @if(!$secteur || !$stats)
    <div class="chart-card" style="margin-top:16px; text-align:center; padding:40px;">
        <i class="fa-solid fa-chart-bar" style="font-size:36px; color:var(--text-dim,#8aaa95); margin-bottom:12px; display:block;"></i>
        <div style="font-size:14px; font-weight:600; color:var(--text,#1a2d22); margin-bottom:4px;">Sélectionnez un secteur</div>
        <div style="font-size:12px; color:var(--text-dim,#8aaa95);">
            Choisissez un secteur pour afficher son analyse globale (tout le territoire). Vous pouvez ensuite affiner le périmètre avec la région ou le niveau territorial (région, département, commune ou localité).
        </div>
    </div>
    @endif

    {{-- ═══ Contenu du secteur ═══ --}}
    @if($secteur && $stats)

    @php
        $kpis = [
            ['nb' => $stats['totalInfra'],    'lbl' => 'Infrastructures',    'icon' => 'fa-building',      'color' => 'var(--primary,#267a47)'],
            ['nb' => $stats['totalIndicateurs'], 'lbl' => 'Indicateurs',     'icon' => 'fa-ruler-combined','color' => '#0f5132'],
            ['nb' => $stats['totalMesures'],  'lbl' => 'Mesures saisies',    'icon' => 'fa-database',      'color' => '#2e7fbb'],
            ['nb' => $stats['populationCouverte'], 'lbl' => 'Population couverte', 'icon' => 'fa-users', 'color' => '#c07b28'],
            ['nb' => $stats['totalLocalites'], 'lbl' => 'Localités desservies', 'icon' => 'fa-map-pin',    'color' => '#7b4fba'],
        ];
        $totRow = ['nb' => 0, 'vals' => []];
        foreach ($stats['matrice'] as $r) {
            $totRow['nb'] += $r['nb'];
            foreach ($r['vals'] as $k => $v) { $totRow['vals'][$k] = ($totRow['vals'][$k] ?? 0) + $v; }
        }
        $totTypes = ['nb' => 0, 'vals' => []];
        foreach ($stats['parTypes'] as $r) {
            $totTypes['nb'] += $r['nb'];
            foreach ($r['vals'] as $k => $v) { $totTypes['vals'][$k] = ($totTypes['vals'][$k] ?? 0) + $v; }
        }
        $perimetre = 'Ensemble du territoire';
        if ($regionNom) {
            $perimetre = 'Région : '.$regionNom.($niveau !== 'region' ? ' · Niveau « '.$niveauLabels[$niveau].' »' : '');
        }
        if ($entiteNom && $niveau !== 'region') {
            $perimetre = 'Niveau « '.$niveauLabels[$niveau].' » · Entité : '.$entiteNom;
        }
    @endphp

    {{-- ═══ En-tête secteur ═══ --}}
    <div class="chart-card" style="margin-top:16px; padding:14px 18px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <span id="sect-emoji" class="js-sect-emoji" data-nom="{{ $secteur->nom }}" style="font-size:26px;">📍</span>
        <div>
            <div class="chart-title" style="margin:0;">Secteur « {{ $secteur->nom }} »</div>
            <div class="chart-sub" style="margin:0;">Périmètre : {{ $perimetre }}</div>
        </div>
    </div>

    {{-- ═══ KPIs ═══ --}}
    <div class="stat-kpis" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));">
        @foreach($kpis as $kpi)
        <div class="sk" style="--sk-color:{{ $kpi['color'] }}">
            <div class="sk-icon"><i class="fa-solid {{ $kpi['icon'] }}"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ number_format($kpi['nb'], 0, ',', ' ') }}</div>
                <div class="sk-lbl">{{ $kpi['lbl'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══ Grand tableau croisé : entités × indicateurs ═══ --}}
    <div class="chart-card" style="margin-top:16px;">
        <div class="chart-title">Valeurs du secteur par {{ $niveauLabels[$niveau] }}</div>
        <div class="chart-sub">Somme des valeurs enregistrées pour chaque indicateur, regroupée par entité du niveau choisi</div>
        @if(count($stats['matrice']) > 0)
        <div class="table-wrap">
            <table class="table-stats">
                <thead>
                    <tr>
                        <th>{{ $niveauLabels[$niveau] }}</th>
                        <th class="num">Infras</th>
                        @foreach($secteur->indicateurs as $ind)
                            <th class="num">{{ $ind->nom_indicateur }}@if($ind->unites)<small>{{ $ind->unites }}</small>@endif</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['matrice'] as $row)
                    <tr>
                        <td>{{ $row['nom'] }}</td>
                        <td class="num">{{ $row['nb'] }}</td>
                        @foreach($secteur->indicateurs as $ind)
                            <td class="num">{{ isset($row['vals'][$ind->id]) ? $fmt($row['vals'][$ind->id]) : '<span class="cell-empty">—</span>' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
                @if(count($stats['matrice']) > 1)
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="num">{{ $totRow['nb'] }}</td>
                        @foreach($secteur->indicateurs as $ind)
                            <td class="num">{{ isset($totRow['vals'][$ind->id]) ? $fmt($totRow['vals'][$ind->id]) : '<span class="cell-empty">—</span>' }}</td>
                        @endforeach
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @else
        <div class="empty-box">Aucune infrastructure pour ce secteur et ce périmètre.</div>
        @endif
    </div>

    {{-- ═══ Tableau croisé par type d'infrastructure ═══ --}}
    <div class="chart-card" style="margin-top:16px;">
        <div class="chart-title">Valeurs par type d'infrastructure</div>
        <div class="chart-sub">Regroupement selon le type recensé (mêmes indicateurs que le secteur)</div>
        @if(count($stats['parTypes']) > 0)
        <div class="table-wrap">
            <table class="table-stats">
                <thead>
                    <tr>
                        <th>Type d'infrastructure</th>
                        <th class="num">Infras</th>
                        @foreach($secteur->indicateurs as $ind)
                            <th class="num">{{ $ind->nom_indicateur }}@if($ind->unites)<small>{{ $ind->unites }}</small>@endif</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['parTypes'] as $row)
                    <tr>
                        <td>{{ $row['nom'] }}</td>
                        <td class="num">{{ $row['nb'] }}</td>
                        @foreach($secteur->indicateurs as $ind)
                            <td class="num">{{ isset($row['vals'][$ind->id]) ? $fmt($row['vals'][$ind->id]) : '<span class="cell-empty">—</span>' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
                @if(count($stats['parTypes']) > 1)
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="num">{{ $totTypes['nb'] }}</td>
                        @foreach($secteur->indicateurs as $ind)
                            <td class="num">{{ isset($totTypes['vals'][$ind->id]) ? $fmt($totTypes['vals'][$ind->id]) : '<span class="cell-empty">—</span>' }}</td>
                        @endforeach
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @else
        <div class="empty-box">Aucun type recensé pour ce secteur.</div>
        @endif
    </div>

    {{-- ═══ Valeur par localité ═══ --}}
    @if(count($stats['localitesTable']) > 0)
    <div class="chart-card" style="margin-top:16px;">
        <div class="chart-title">Valeur par localité</div>
        <div class="chart-sub">Localités couvertes par les infrastructures du secteur : population couverte, nombre d'infrastructures et population H/F</div>
        <div class="table-wrap">
            <table class="table-stats">
                <thead>
                    <tr>
                        <th>Localité</th>
                        <th>Commune</th>
                        <th class="num">Infras</th>
                        <th class="num">Population couverte</th>
                        <th class="num">Hommes</th>
                        <th class="num">Femmes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['localitesTable'] as $l)
                    <tr>
                        <td>{{ $l['nom'] }}</td>
                        <td>{{ $l['commune'] }}</td>
                        <td class="num">{{ $l['nbInfra'] }}</td>
                        <td class="num">{{ number_format($l['pop'], 0, ',', ' ') }}</td>
                        <td class="num">{{ number_format($l['hommes'], 0, ',', ' ') }}</td>
                        <td class="num">{{ number_format($l['femmes'], 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══ Graphiques (types, état, population) ═══ --}}
    @php
        $showCharts = count($stats['typesChart']) + count($stats['etatChart'])
                     + count($stats['hfChart']) + count($stats['popChart']) > 0;
    @endphp
    @if($showCharts)
    <div class="charts-grid" style="margin-top:16px;">

        @if(count($stats['typesChart']) > 0)
        <div class="chart-card">
            <div class="chart-title">Types d'infrastructures</div>
            <div class="chart-sub">Répartition des infrastructures du secteur</div>
            <div class="chart-box"><canvas id="ch-type"></canvas></div>
        </div>
        @endif

        @if(count($stats['etatChart']) > 0)
        <div class="chart-card">
            <div class="chart-title">État des infrastructures</div>
            <div class="chart-sub">Répartition par état général</div>
            <div class="chart-box"><canvas id="ch-etat"></canvas></div>
        </div>
        @endif

        @if(count($stats['hfChart']) > 0)
        <div class="chart-card">
            <div class="chart-title">Population des localités (H/F)</div>
            <div class="chart-sub">Hommes et femmes par localité desservie</div>
            <div class="chart-box"><canvas id="ch-hf"></canvas></div>
        </div>
        @endif

        @if(count($stats['popChart']) > 0)
        <div class="chart-card">
            <div class="chart-title">Population couverte par localité</div>
            <div class="chart-sub">Somme des populations couvertes</div>
            <div class="chart-box"><canvas id="ch-pop"></canvas></div>
        </div>
        @endif

    </div>
    @endif

    {{-- ═══ Graphiques par indicateur ═══ --}}
    @if(count($stats['indicateurCharts']) > 0)
    <div class="charts-grid" style="margin-top:16px;">
        @foreach($stats['indicateurCharts'] as $indChart)
        <div class="chart-card">
            <div class="chart-title">{{ $indChart['nom'] }}@if($indChart['unites']) <small style="color:var(--text-dim,#8aaa95);font-weight:500;">{{ $indChart['unites'] }}</small>@endif</div>
            <div class="chart-sub">Valeur par {{ $niveauLabels[$niveau] }}</div>
            <div class="chart-box"><canvas id="ch-ind-{{ $loop->index }}"></canvas></div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ═══ Comparateur d'indicateurs (ex. garçons / filles) ═══ --}}
    @if(count($stats['comparateur']['totaux']) >= 2)
    <div style="margin-top:16px; background:#fff; border:1px solid #e5efe9; border-radius:14px; padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
            <div>
                <div class="chart-title">Comparer les indicateurs</div>
                <div class="chart-sub">Cochez 2 indicateurs au minimum pour comparer leurs valeurs</div>
            </div>
            <div style="display:flex; gap:6px;">
                <button type="button" id="cmp-mode-totaux" onclick="basculerModeCompare('totaux')" style="padding:5px 12px; border-radius:7px; border:1px solid #e5efe9; background:#267a47; color:#fff; font-size:11px; font-weight:600; cursor:pointer;">Totaux du secteur</button>
                <button type="button" id="cmp-mode-zone" onclick="basculerModeCompare('zone')" style="padding:5px 12px; border-radius:7px; border:1px solid #e5efe9; background:#f3f7f5; color:#1a2d22; font-size:11px; font-weight:600; cursor:pointer;">Par {{ $niveauLabels[$niveau] }}</button>
            </div>
        </div>
        <div id="cmp-checkboxes" style="display:flex; flex-wrap:wrap; gap:8px 16px; margin-bottom:14px;"></div>
        <div style="position:relative; height:300px;">
            <canvas id="ch-indicateurs-compare"></canvas>
        </div>
        <div id="cmp-aucune" style="display:none; padding:24px; text-align:center; color:#8aaa95; font-size:12px;">
            Sélectionnez au moins 2 indicateurs pour afficher la comparaison.
        </div>
    </div>
    @endif

    @endif
</div>

@if($secteur && $stats)
<script>
document.addEventListener('DOMContentLoaded', function() {

    var cfg = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: '#4a6555', font: { size: 11 } } },
            tooltip: { backgroundColor: '#fff', borderColor: '#d0ddd4', borderWidth: 1, titleColor: '#1a2d22', bodyColor: '#4a6555' }
        },
        scales: {
            x: { ticks: { color: '#8aaa95', font: { size: 10 } }, grid: { color: '#f0f4f2' } },
            y: { ticks: { color: '#8aaa95', font: { size: 10 } }, grid: { color: '#f0f4f2' } }
        }
    };
    var PALETTE = ['#267a47', '#2e7fbb', '#c07b28', '#c44030', '#7b4fba', '#1a7abf', '#c06020', '#b54890', '#6a8a70', '#c8a800'];
    var SECT_COLORS = {
        Education: '#2e7fbb', Agriculture: '#c07b28', Elevage: '#7b4fba',
        Hydraulique: '#1a7abf', Commerce: '#c06020', Artisanat: '#b54890',
        Energie: '#c8a800', Migration: '#6a8a70', Sante: '#c44030', Santé: '#c44030'
    };
    var SECT_ICONS = {
        Education: '\u{1F3EB}', Agriculture: '\u{1F33E}', Elevage: '\u{1F404}',
        Hydraulique: '\u{1F4A7}', Commerce: '\u{1F6D2}', Artisanat: '\u{1F528}',
        Energie: '\u26A1', Migration: '\u2708\uFE0F', Sante: '\u{1F3E5}', Santé: '\u{1F3E5}'
    };
    function norm(s) { if (!s) return s; return s.charAt(0).toUpperCase() + s.slice(1); }

    // ── Emoji du secteur (coherence avec la cartographie) ──
    var emojiEl = document.getElementById('sect-emoji');
    if (emojiEl) emojiEl.textContent = SECT_ICONS[norm(emojiEl.dataset.nom)] || '\u{1F4CD}';

    var stats = @json($stats);

    // ── Types (doughnut) ──
    if (stats.typesChart.length && document.getElementById('ch-type')) {
        new Chart(document.getElementById('ch-type'), {
            type: 'doughnut',
            data: {
                labels: stats.typesChart.map(function(t) { return t.nom; }),
                datasets: [{ data: stats.typesChart.map(function(t) { return t.total; }), backgroundColor: PALETTE.slice(0, stats.typesChart.length), borderColor: '#fff', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: '#4a6555', font: { size: 11 } } }, tooltip: cfg.plugins.tooltip } }
        });
    }

    // ── État (doughnut) ──
    var etatColorsMap = { 'Bon': '#267a47', 'Moyen': '#c07b28', 'Mauvais': '#c44030', 'Hors service': '#8aaa95' };
    if (stats.etatChart.length && document.getElementById('ch-etat')) {
        new Chart(document.getElementById('ch-etat'), {
            type: 'doughnut',
            data: {
                labels: stats.etatChart.map(function(e) { return e.nom; }),
                datasets: [{ data: stats.etatChart.map(function(e) { return e.total; }), backgroundColor: stats.etatChart.map(function(e) { return etatColorsMap[norm(e.nom)] || '#8aaa95'; }), borderColor: '#fff', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: '#4a6555', font: { size: 11 } } }, tooltip: cfg.plugins.tooltip } }
        });
    }

    // ── Population H/F (bar groupée) ──
    if (stats.hfChart.length && document.getElementById('ch-hf')) {
        new Chart(document.getElementById('ch-hf'), {
            type: 'bar',
            data: {
                labels: stats.hfChart.map(function(h) { return h.nom; }),
                datasets: [
                    { label: 'Hommes', data: stats.hfChart.map(function(h) { return h.hommes; }), backgroundColor: '#1a7abf55', borderColor: '#1a7abf', borderWidth: 1.5, borderRadius: 4 },
                    { label: 'Femmes', data: stats.hfChart.map(function(h) { return h.femmes; }), backgroundColor: '#d26a8d55', borderColor: '#d26a8d', borderWidth: 1.5, borderRadius: 4 }
                ]
            },
            options: cfg
        });
    }

    // ── Population couverte par localité (bar) ──
    if (stats.popChart.length && document.getElementById('ch-pop')) {
        new Chart(document.getElementById('ch-pop'), {
            type: 'bar',
            data: {
                labels: stats.popChart.map(function(p) { return p.nom; }),
                datasets: [{
                    data: stats.popChart.map(function(p) { return p.pop; }),
                    backgroundColor: '#c07b2844', borderColor: '#c07b28', borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    }

    // ── Un bar chart par indicateur (valeur par entité du niveau choisi) ──
    stats.indicateurCharts.forEach(function(ic, i) {
        var canvas = document.getElementById('ch-ind-' + i);
        if (!canvas) return;
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: ic.labels,
                datasets: [{
                    data: ic.data,
                    backgroundColor: (PALETTE[i % PALETTE.length]) + '44',
                    borderColor: PALETTE[i % PALETTE.length],
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    });

    // ── Comparateur d'indicateurs (choisir 2+ indicateurs, ex. garçons/filles) ──
    if (stats.comparateur && stats.comparateur.totaux.length >= 2) {
        var cmpMode  = 'totaux';
        var cmpChart = null;

        function afficherMessageComparateur(msg) {
            var c = document.getElementById('ch-indicateurs-compare');
            if (cmpChart) { cmpChart.destroy(); cmpChart = null; }
            c.style.display = 'none';
            document.getElementById('cmp-aucune').style.display = 'block';
            document.getElementById('cmp-aucune').textContent = msg;
        }

        function renderComparateur() {
            var c = document.getElementById('ch-indicateurs-compare');
            if (cmpChart) { cmpChart.destroy(); cmpChart = null; }

            var sel = [].slice.call(document.querySelectorAll('#cmp-checkboxes input:checked'))
                .map(function(cb) { return cb.value; });
            var des = stats.comparateur.totaux.filter(function(t) { return sel.indexOf(String(t.id)) !== -1; });
            if (des.length < 2) {
                afficherMessageComparateur('Sélectionnez au moins 2 indicateurs pour afficher la comparaison.');
                return;
            }

            c.style.display = 'block';
            document.getElementById('cmp-aucune').style.display = 'none';

            if (cmpMode === 'totaux') {
                cmpChart = new Chart(c, {
                    type: 'bar',
                    data: {
                        labels: des.map(function(t) { return t.nom; }),
                        datasets: [{
                            data: des.map(function(t) { return t.total; }),
                            backgroundColor: des.map(function(_, i) { return PALETTE[i % PALETTE.length]; }),
                            borderRadius: 6
                        }]
                    },
                    options: Object.assign({}, cfg, { plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
                });
            } else {
                var series = stats.comparateur.parZone.series.filter(function(s) { return sel.indexOf(String(s.id)) !== -1; });
                cmpChart = new Chart(c, {
                    type: 'bar',
                    data: {
                        labels: stats.comparateur.parZone.labels,
                        datasets: series.map(function(s, i) {
                            return {
                                label: s.nom,
                                data: s.data,
                                backgroundColor: PALETTE[i % PALETTE.length] + '55',
                                borderColor: PALETTE[i % PALETTE.length],
                                borderWidth: 1.5, borderRadius: 4
                            };
                        })
                    },
                    options: cfg
                });
            }
        }

        // Cases à cocher (indicateurs mesurés du secteur)
        document.getElementById('cmp-checkboxes').innerHTML = '';
        stats.comparateur.totaux.forEach(function(t) {
            var lab = document.createElement('label');
            lab.style.cssText = 'display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#1a2d22; cursor:pointer;';
            var cb = document.createElement('input');
            cb.type = 'checkbox'; cb.value = String(t.id); cb.checked = true;
            lab.appendChild(cb);
            lab.appendChild(document.createTextNode(t.nom + (t.unites ? ' (' + t.unites + ')' : '')));
            document.getElementById('cmp-checkboxes').appendChild(lab);
        });
        document.getElementById('cmp-checkboxes').addEventListener('change', renderComparateur);

        window.basculerModeCompare = function(mode) {
            cmpMode = mode;
            var base = 'padding:5px 12px; border-radius:7px; border:1px solid #e5efe9; font-size:11px; font-weight:600; cursor:pointer;';
            var actif   = 'background:#267a47; color:#fff;';
            var inactif = 'background:#f3f7f5; color:#1a2d22;';
            document.getElementById('cmp-mode-totaux').setAttribute('style', base + (mode === 'totaux' ? actif : inactif));
            document.getElementById('cmp-mode-zone').setAttribute('style', base + (mode === 'zone' ? actif : inactif));
            renderComparateur();
        };

        renderComparateur();
    }

    // ── Redessin responsive ──
    var charts = [].slice.call(document.querySelectorAll('canvas[id^="ch-"]'))
        .filter(function(c) { return Chart.getChart(c); })
        .map(function(c) { return Chart.getChart(c); });
    function redrawCharts() { charts.forEach(function(ch) { if (ch) ch.resize(); }); }
    window.addEventListener('resize', function() { requestAnimationFrame(redrawCharts); });
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function() { requestAnimationFrame(redrawCharts); });
    }
    requestAnimationFrame(redrawCharts);

    // ═══ Cascade des filtres (région → entité) ═══
    var regions = @json($regions);
    var depts = @json($regionDepts);
    var communes = @json($communes);
    var localites = @json($localites);

    function fillEntites() {
        var regionId = document.getElementById('region').value;
        var niv = document.getElementById('niveau').value;
        var sel = document.getElementById('entite');
        if (!sel) return;
        var current = sel.dataset.current || '';

        // Niveau « Région » : la région se choisit via le filtre « Région »
        if (niv === 'region') {
            sel.disabled = true;
            sel.innerHTML = '<option value="">— via le filtre « Région » —</option>';
            return;
        }
        sel.disabled = false;
        sel.innerHTML = '<option value="">— Toutes —</option>';
        var list = niv === 'departement' ? depts : (niv === 'commune' ? communes : localites);
        list.forEach(function(e) {
            if (regionId && String(e.region_id) !== String(regionId)) return;
            var o = document.createElement('option');
            o.value = e.id;
            o.textContent = e.nom;
            if (String(e.id) === String(current)) o.selected = true;
            sel.appendChild(o);
        });
    }

    document.querySelectorAll('#filters-form select[data-auto]').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var entite = document.getElementById('entite');
            if (entite) entite.value = '';
            document.getElementById('filters-form').submit();
        });
    });
    var entiteSel = document.getElementById('entite');
    if (entiteSel) {
        entiteSel.addEventListener('change', function() { this.form.submit(); });
        entiteSel.addEventListener('change', fillEntites);
    }
    document.querySelectorAll('#filters-form select').forEach(function(sel) {
        sel.addEventListener('change', fillEntites);
    });
    fillEntites();

});
</script>
@endif

<style>
    .stats-filters { margin-bottom:16px; }
    .stats-filters-inner { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; max-width:880px; margin:0 auto; padding:12px 16px; background:var(--surface,#fff); border:1px solid var(--border,#e8efe9); border-radius:12px; box-shadow:0 2px 10px rgba(26,45,34,.04); }
    .filter-group { display:flex; flex-direction:column; gap:4px; min-width:140px; }
    .filter-group label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--text-dim,#8aaa95); }
    .filter-group select { padding:8px 10px; font-size:12px; font-family:'DM Sans',sans-serif; border:1px solid var(--border,#e8efe9); border-radius:8px; background:var(--surface2,#f4f7f5); color:var(--text,#1a2d22); outline:none; width:100%; }
    .filter-group select:focus { border-color:var(--primary,#267a47); box-shadow:0 0 0 3px rgba(38,122,71,.12); }

    .table-wrap { overflow-x:auto; margin-top:10px; }
    .table-stats { width:100%; border-collapse:collapse; font-size:12px; }
    .table-stats thead th { text-align:left; padding:8px 10px; color:var(--text-dim,#8aaa95); font-weight:600; border-bottom:2px solid var(--border,#e8efe9); white-space:nowrap; }
    .table-stats th.num, .table-stats td.num { text-align:right; }
    .table-stats tbody td { padding:8px 10px; border-bottom:1px solid var(--border,#e8efe9); color:var(--text,#1a2d22); font-variant-numeric:tabular-nums; }
    .table-stats tbody tr:hover { background:var(--surface2,#f4f7f5); }
    .table-stats tfoot td { padding:8px 10px; border-top:2px solid var(--border,#e8efe9); color:var(--primary,#267a47); font-weight:700; font-variant-numeric:tabular-nums; }
    .table-stats .cell-empty { color:var(--text-dim,#c3d2ca); }
    .table-stats small { display:block; color:var(--text-dim,#8aaa95); font-weight:500; }

    .empty-box { padding:22px; text-align:center; color:var(--text-dim,#8aaa95); font-size:12px; }
</style>
@endsection