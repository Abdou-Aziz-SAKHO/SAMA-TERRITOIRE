@extends('AppAdmi')
@section('content')

<div id="page-stats" class="page active">
    <div class="page-title">Vue générale</div>
    <div class="page-sub">Vue globale du territoire · filtrable par région, département, commune ou localité</div>

    {{-- ═══ Filtres (niveau territorial + entité) ═══ --}}
    <form method="GET" action="{{ url('/StatistiquesAdmi/VueGenerale') }}" id="filters-form" class="stats-filters">
        <div class="stats-filters-inner">

            <div class="filter-group" style="flex:1;">
                <label for="niveau">Niveau territorial</label>
                <select name="niveau" id="niveau" data-auto>
                    <option value="" {{ $niveau === null ? 'selected' : '' }}>— Tous niveaux confondus —</option>
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
                <button type="submit" class="btn-submit"><i class="fa-solid fa-filter"></i> Afficher</button>
            </div>

        </div>
    </form>

    {{-- ═══ Information générale — chiffres clés du niveau choisi ═══ --}}
    <div class="chart-card" style="margin-top:16px;">
        <div class="chart-title">
            <i class="fa-solid fa-circle-info" style="color:var(--primary,#267a47);"></i>
            Information générale — {{ $scopeLabel }}
        </div>
        <div class="chart-sub">
            {{ $nbRegions }} région(s) · {{ $nbDepts }} département(s) · {{ $nbCommunes }} commune(s) · {{ $nbLocalites }} localité(s)
        </div>

        <div class="stat-kpis" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr));">
            <div class="sk" style="--sk-color:var(--primary,#267a47)">
                <div class="sk-icon"><i class="fa-solid fa-users"></i></div>
                <div class="sk-body">
                    <div class="sk-num">{{ number_format($popTotale, 0, ',', ' ') }}</div>
                    <div class="sk-lbl">Population totale</div>
                </div>
            </div>
            <div class="sk" style="--sk-color:#0f5132">
                <div class="sk-icon"><i class="fa-solid fa-house"></i></div>
                <div class="sk-body">
                    <div class="sk-num">{{ number_format($menages, 0, ',', ' ') }}</div>
                    <div class="sk-lbl">Nbre de ménages</div>
                </div>
            </div>
            <div class="sk" style="--sk-color:#7b4fba">
                <div class="sk-icon"><i class="fa-solid fa-map-pin"></i></div>
                <div class="sk-body">
                    <div class="sk-num">{{ $nbLocalites }}</div>
                    <div class="sk-lbl">Nbre de localités</div>
                </div>
            </div>
            <div class="sk" style="--sk-color:var(--red,#c44030)">
                <div class="sk-icon"><i class="fa-solid fa-building"></i></div>
                <div class="sk-body">
                    <div class="sk-num">{{ number_format($totalInfra, 0, ',', ' ') }}</div>
                    <div class="sk-lbl">Infrastructures recensées</div>
                </div>
            </div>

            @foreach($cascade as $c)
            <div class="sk" style="--sk-color:#2e7fbb">
                <div class="sk-icon"><i class="fa-solid {{ $c['icon'] }}"></i></div>
                <div class="sk-body">
                    <div class="sk-num">{{ $c['value'] }}</div>
                    <div class="sk-lbl">{{ $c['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ Ligne 1 : répartition générale du périmètre ═══ --}}
    <div class="charts-grid" style="margin-top:16px;">

        @if($repartition->isNotEmpty())
        {{-- Répartition par sous-unité du niveau choisi (doughnut) --}}
        <div class="chart-card">
            <div class="chart-title">Répartition par {{ strtolower($repartitionLabel) }}</div>
            <div class="chart-sub">Distribution des infrastructures du périmètre</div>
            <div class="chart-box"><canvas id="ch-repartition"></canvas></div>
        </div>
        @endif

        {{-- Répartition par secteur (bar) --}}
        <div class="chart-card">
            <div class="chart-title">Répartition par secteur</div>
            <div class="chart-sub">Nombre d'infrastructures recensées dans le périmètre</div>
            <div class="chart-box"><canvas id="ch-sect"></canvas></div>
        </div>

        {{-- État des infrastructures (bar) --}}
        <div class="chart-card">
            <div class="chart-title">État des infrastructures</div>
            <div class="chart-sub">Répartition par état général</div>
            <div class="chart-box"><canvas id="ch-etat"></canvas></div>
        </div>

        @if($topUnits->isNotEmpty())
        {{-- Top sous-unités les plus équipées (bar horizontal) --}}
        <div class="chart-card">
            <div class="chart-title">Top {{ strtolower($repartitionLabel) }} équipés</div>
            <div class="chart-sub">Nombre d'infrastructures par {{ strtolower($repartitionLabel === 'Régions' ? 'région' : rtrim(strtolower($repartitionLabel), 's')) }}</div>
            <div class="chart-box"><canvas id="ch-top"></canvas></div>
        </div>
        @endif

    </div>

    {{-- ═══ Ligne 2 : types par secteur (un doughnut par secteur) ═══ --}}
    @if($secteursDetail->isNotEmpty())
    <div class="charts-grid" style="margin-top:16px;">
        @foreach($secteursDetail as $sect)
        <div class="chart-card">
            <div class="chart-title">
                <span class="sect-emoji js-sect-emoji" data-nom="{{ $sect['secteur'] }}">📍</span>
                {{ $sect['secteur'] }} — Types
            </div>
            <div class="chart-sub">
                {{ $sect['total'] }} infrastructure(s) recensée(s)
                <span class="js-sect-badge" style="display:none;"></span>
            </div>
            <div class="chart-box"><canvas id="ch-sec-{{ $loop->index }}"></canvas></div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ═══ Ligne 3 : population H/F + évolution ═══ --}}
    @if($populationHf->isNotEmpty())
    <div class="chart-card" style="margin-top:16px;">
        <div class="chart-title">Population hommes / femmes par {{ strtolower($repartitionLabel ?: 'niveau') }}</div>
        <div class="chart-sub">Données démographiques des {{ strtolower($repartitionLabel ?: 'niveaux') }} du périmètre</div>
        <div class="chart-box" style="height:260px;"><canvas id="ch-hf"></canvas></div>
    </div>
    @endif

    <div class="chart-card" style="margin-top:16px;">
        <div class="chart-title">Évolution des infrastructures par année</div>
        <div class="chart-sub">Nombre de créations dans le périmètre</div>
        <div class="chart-box" style="height:260px;"><canvas id="ch-evol"></canvas></div>
    </div>

</div>

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

    var PALETTE = ['#267a47', '#2e7fbb', '#c07b28', '#c44030', '#7b4fba', '#1a7abf', '#c06020', '#b54890'];
    var SECT_COLORS = {
        Education: '#2e7fbb', Agriculture: '#c07b28', Elevage: '#7b4fba',
        Hydraulique: '#1a7abf', Commerce: '#c06020', Artisanat: '#b54890',
        Energie: '#c8a800', Migration: '#6a8a70', Sante: '#c44030',
        Santé: '#c44030'
    };
    var SECT_ICONS = {
        Education: '\u{1F3EB}', Agriculture: '\u{1F33E}', Elevage: '\u{1F404}',
        Hydraulique: '\u{1F4A7}', Commerce: '\u{1F6D2}', Artisanat: '\u{1F528}',
        Energie: '\u26A1', Migration: '\u2708\uFE0F', Sante: '\u{1F3E5}',
        Santé: '\u{1F3E5}'
    };
    function norm(s) { if (!s) return s; return s.charAt(0).toUpperCase() + s.slice(1); }

    var parSecteur = @json($parSecteur);
    var repartition = @json($repartition);
    var topUnits = @json($topUnits);
    var populationHf = @json($populationHf);
    var parEtat = @json($parEtat);
    var evolution = @json($evolution);
    var secteursDetail = @json($secteursDetail);

    // ── Répartition par sous-unité du niveau choisi (doughnut) ──
    if (repartition.length > 0 && document.getElementById('ch-repartition')) {
        new Chart(document.getElementById('ch-repartition'), {
            type: 'doughnut',
            data: {
                labels: repartition.map(function(c) { return c.nom; }),
                datasets: [{ data: repartition.map(function(c) { return c.total; }), backgroundColor: PALETTE.slice(0, repartition.length), borderColor: '#fff', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: '#4a6555', font: { size: 11 } } }, tooltip: cfg.plugins.tooltip } }
        });
    }

    // ── Répartition par secteur (bar) ──
    if (parSecteur.length > 0 && document.getElementById('ch-sect')) {
        new Chart(document.getElementById('ch-sect'), {
            type: 'bar',
            data: {
                labels: parSecteur.map(function(s) { return (SECT_ICONS[s.nom] || SECT_ICONS[norm(s.nom)] || '\u{1F4CD}') + ' ' + s.nom; }),
                datasets: [{
                    data: parSecteur.map(function(s) { return s.total; }),
                    backgroundColor: parSecteur.map(function(s) { return (SECT_COLORS[s.nom] || SECT_COLORS[norm(s.nom)] || '#267a47') + '44'; }),
                    borderColor: parSecteur.map(function(s) { return SECT_COLORS[s.nom] || SECT_COLORS[norm(s.nom)] || '#267a47'; }),
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    }

    // ── État des infrastructures (bar) ──
    if (parEtat.length > 0 && document.getElementById('ch-etat')) {
        var etatColors = { 'Bon': '#267a47', 'Moyen': '#c07b28', 'Mauvais': '#c44030', 'Hors service': '#8aaa95' };
        new Chart(document.getElementById('ch-etat'), {
            type: 'bar',
            data: {
                labels: parEtat.map(function(e) { return e.nom; }),
                datasets: [{
                    data: parEtat.map(function(e) { return e.total; }),
                    backgroundColor: parEtat.map(function(e) { return (etatColors[norm(e.nom)] || '#8aaa95') + '44'; }),
                    borderColor: parEtat.map(function(e) { return etatColors[norm(e.nom)] || '#8aaa95'; }),
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    }

    // ── Top sous-unités les plus équipées (bar horizontal) ──
    if (topUnits.length > 0 && document.getElementById('ch-top')) {
        new Chart(document.getElementById('ch-top'), {
            type: 'bar',
            data: {
                labels: topUnits.map(function(v) { return v.nom; }),
                datasets: [{
                    data: topUnits.map(function(v) { return v.total; }),
                    backgroundColor: 'rgba(38,122,71,0.55)',
                    borderColor: '#267a47',
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { indexAxis: 'y', plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    }

    // ── Population hommes / femmes (bar empilé) ──
    if (populationHf.length > 0 && document.getElementById('ch-hf')) {
        new Chart(document.getElementById('ch-hf'), {
            type: 'bar',
            data: {
                labels: populationHf.map(function(r) { return r.nom; }),
                datasets: [
                    { label: 'Hommes', data: populationHf.map(function(r) { return r.hommes; }), backgroundColor: 'rgba(26,122,191,0.75)', borderColor: '#1a7abf', borderWidth: 1, borderRadius: 4 },
                    { label: 'Femmes', data: populationHf.map(function(r) { return r.femmes; }), backgroundColor: 'rgba(210,106,141,0.75)', borderColor: '#d26a8d', borderWidth: 1, borderRadius: 4 }
                ]
            },
            options: Object.assign({}, cfg, { scales: Object.assign({}, cfg.scales, { x: Object.assign({}, cfg.scales.x, { stacked: true }), y: Object.assign({}, cfg.scales.y, { stacked: true }) }) })
        });
    }

    // ── Doughnuts : un par secteur (types d'infrastructures) ──
    secteursDetail.forEach(function(sect, i) {
        var canvas = document.getElementById('ch-sec-' + i);
        if (!canvas || !sect.types || sect.types.length === 0) return;
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: sect.types.map(function(t) { return t.nom; }),
                datasets: [{ data: sect.types.map(function(t) { return t.total; }), backgroundColor: PALETTE.slice(0, sect.types.length), borderColor: '#fff', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: '#4a6555', font: { size: 11 } } }, tooltip: cfg.plugins.tooltip } }
        });
    });

    // ── Évolution par année (line cumulée) ──
    if (evolution.length > 0 && document.getElementById('ch-evol')) {
        var annees = evolution.map(function(e) { return e.annee; });
        var cumul = 0;
        var cumulData = evolution.map(function(e) { cumul += parseInt(e.total, 10); return cumul; });
        new Chart(document.getElementById('ch-evol'), {
            type: 'line',
            data: {
                labels: annees,
                datasets: [{
                    label: 'Cumul infrastructures',
                    data: cumulData,
                    borderColor: '#267a47',
                    backgroundColor: 'rgba(38,122,71,0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#267a47'
                }]
            },
            options: cfg
        });
    }

    // ── Redessin responsive des graphiques ──
    var charts = [].slice.call(document.querySelectorAll('canvas[id^="ch-"]'))
        .filter(function(c) { return Chart.getChart(c); })
        .map(function(c) { return Chart.getChart(c); });
    function redrawCharts() { charts.forEach(function(ch) { if (ch) ch.resize(); }); }
    window.addEventListener('resize', function() { requestAnimationFrame(redrawCharts); });
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function() { requestAnimationFrame(redrawCharts); });
    }
    requestAnimationFrame(redrawCharts);

    // ═══ Cascade des filtres (niveau → entité) ═══
    var regions = @json($regions);
    var depts = @json($regionDepts);
    var communes = @json($communes);
    var localites = @json($localites);

    function fillEntites() {
        var niv = document.getElementById('niveau').value;
        var sel = document.getElementById('entite');
        if (!sel) return;
        var current = sel.dataset.current || '';
        var list = niv === '' ? [] : (niv === 'region' ? regions : (niv === 'departement' ? depts : (niv === 'commune' ? communes : localites)));
        sel.disabled = list.length === 0;
        sel.innerHTML = '<option value="">— Toutes —</option>';
        list.forEach(function(e) {
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

<style>
    .stats-filters { margin-bottom:16px; }
    .stats-filters-inner { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; max-width:680px; margin:0 auto; padding:12px 16px; background:var(--surface,#fff); border:1px solid var(--border,#e8efe9); border-radius:12px; box-shadow:0 2px 10px rgba(26,45,34,.04); }
    .filter-group { display:flex; flex-direction:column; gap:4px; min-width:180px; }
    .filter-group label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--text-dim,#8aaa95); }
    .filter-group select { padding:8px 10px; font-size:12px; font-family:'DM Sans',sans-serif; border:1px solid var(--border,#e8efe9); border-radius:8px; background:var(--surface2,#f4f7f5); color:var(--text,#1a2d22); outline:none; width:100%; }
    .filter-group select:focus { border-color:var(--primary,#267a47); box-shadow:0 0 0 3px rgba(38,122,71,.12); }
</style>
@endsection