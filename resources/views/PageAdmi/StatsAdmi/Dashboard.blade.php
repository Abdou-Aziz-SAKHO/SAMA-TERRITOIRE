@extends('AppAdmi')
@section('content')

<div id="page-stats" class="page active">
    <div class="page-title">Tableau de bord</div>
    <div class="page-sub">Résumé de la situation du territoire · Données en temps réel</div>

    {{-- ═══ KPIs — Ligne 1 : Structure territoriale (essentiels) ═══ --}}
    <div class="stat-kpis">
        <div class="sk" style="--sk-color:var(--primary)">
            <div class="sk-icon"><i class="fa-solid fa-earth-africa"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ $totalRegions }}</div>
                <div class="sk-lbl">Régions</div>
            </div>
        </div>
        <div class="sk" style="--sk-color:var(--blue)">
            <div class="sk-icon"><i class="fa-solid fa-landmark"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ $totalDepartements }}</div>
                <div class="sk-lbl">Départements</div>
            </div>
        </div>
        <div class="sk" style="--sk-color:var(--accent)">
            <div class="sk-icon"><i class="fa-solid fa-city"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ $totalCommunes }}</div>
                <div class="sk-lbl">Communes</div>
            </div>
        </div>
        <div class="sk" style="--sk-color:#7b4fba">
            <div class="sk-icon"><i class="fa-solid fa-map-pin"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ $totalLocalites }}</div>
                <div class="sk-lbl">Localités</div>
            </div>
        </div>
    </div>

    {{-- ═══ KPIs — Ligne 2 : Données mesurées ═══ --}}
    <div class="stat-kpis" style="margin-top:0;">
        <div class="sk" style="--sk-color:var(--red)">
            <div class="sk-icon"><i class="fa-solid fa-building"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ number_format($totalInfra, 0, ',', ' ') }}</div>
                <div class="sk-lbl">Infrastructures</div>
            </div>
        </div>
        <div class="sk" style="--sk-color:#0f5132">
            <div class="sk-icon"><i class="fa-solid fa-tags"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ $totalSecteurs }}</div>
                <div class="sk-lbl">Secteurs</div>
            </div>
        </div>
        <div class="sk" style="--sk-color:#1a7abf">
            <div class="sk-icon"><i class="fa-solid fa-users"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ number_format($populationCouverte, 0, ',', ' ') }}</div>
                <div class="sk-lbl">Population couverte</div>
            </div>
        </div>
        <div class="sk" style="--sk-color:#c07b28">
            <div class="sk-icon"><i class="fa-solid fa-comments"></i></div>
            <div class="sk-body">
                <div class="sk-num">{{ $commentairesEnAttente }}</div>
                <div class="sk-lbl">Retours en attente</div>
            </div>
        </div>
    </div>

    {{-- ═══ Ligne 2 : Charts (gauche) + Alertes & Documents (droite) ═══ --}}
    <div class="charts-grid">

        {{-- Colonne gauche : Répartition secteur + Top 10 villages --}}
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="chart-card">
                <div class="chart-title">Répartition par secteur</div>
                <div class="chart-sub">Nombre d'infrastructures recensées</div>
                <div class="chart-box"><canvas id="ch-sect"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Top 10 communes équipées</div>
                <div class="chart-sub">Nombre d'infrastructures par commune</div>
                <div class="chart-box"><canvas id="ch-vill"></canvas></div>
            </div>
        </div>

        {{-- Colonne droite : Alertes + Documents + Commentaires --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Alertes --}}
            <div class="chart-card" style="min-height:160px;">
                <div class="chart-title" style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color:var(--red,#c44030);"></i> Alertes
                    @if($alertes->count() > 0)
                        <span style="background:var(--red,#c44030); color:#fff; font-size:10px; padding:2px 7px; border-radius:10px; font-weight:700;">{{ $alertes->count() }}</span>
                    @endif
                </div>
                <div class="chart-sub">Infrastructures nécessitant une attention</div>
                <div style="max-height:180px; overflow-y:auto; margin-top:8px;">
                    @forelse($alertes as $alerte)
                        <a href="{{ url('/Donnees?tab=infrastructures') }}"
                           style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--border,#e8efe9); text-decoration:none; color:inherit; cursor:pointer;">
                            <span style="width:8px; height:8px; border-radius:50%; flex-shrink:0;
                                background:{{ $alerte->etat_lieu === 'Mauvais' ? '#c44030' : ($alerte->etat_lieu === 'Hors_service' ? '#888' : ($alerte->etat_lieu === 'Moyen' ? '#c07b28' : '#8aaa95')) }};"></span>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:12px; font-weight:600; color:var(--text,#1a2d22); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $alerte->nom ?: 'Infrastructure #' . $alerte->id }}
                                </div>
                                <div style="font-size:11px; color:var(--text-dim,#8aaa95);">
                                    {{ $alerte->secteur?->nom ?? '—' }} · {{ $alerte->etat_lieu ?: 'État inconnu' }}
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-right" style="font-size:10px; color:var(--text-dim,#8aaa95); flex-shrink:0;"></i>
                        </a>
                    @empty
                        <div style="text-align:center; padding:16px; font-size:12px; color:var(--text-dim,#8aaa95);">
                            <i class="fa-solid fa-circle-check" style="color:#267a47; margin-bottom:4px; display:block; font-size:18px;"></i>
                            Aucune alerte en cours
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Derniers documents --}}
            <div class="chart-card" style="min-height:140px;">
                <div class="chart-title" style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-file-lines" style="color:var(--blue,#2e7fbb);"></i> Derniers documents
                </div>
                <div class="chart-sub">Fichiers récemment importés</div>
                <div style="max-height:160px; overflow-y:auto; margin-top:8px;">
                    @forelse($derniersDocs as $doc)
                        <a href="{{ url('/Documents/' . $doc->id . '/apercu') }}" target="_blank"
                           style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--border,#e8efe9); text-decoration:none; color:inherit; cursor:pointer;">
                            <i class="fa-solid fa-file-{{ $doc->extension === 'pdf' ? 'pdf' : ($doc->extension === 'xlsx' || $doc->extension === 'xls' ? 'excel' : 'word') }}"
                               style="color:{{ $doc->extension === 'pdf' ? '#c44030' : ($doc->extension === 'xlsx' || $doc->extension === 'xls' ? '#267a47' : '#2e7fbb') }}; font-size:16px; flex-shrink:0;"></i>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:12px; font-weight:600; color:var(--text,#1a2d22); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $doc->titre }}
                                </div>
                                <div style="font-size:11px; color:var(--text-dim,#8aaa95);">
                                    {{ $doc->commune?->nom ?? $doc->departement?->nom ?? '—' }} · {{ $doc->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px; color:var(--text-dim,#8aaa95); flex-shrink:0;"></i>
                        </a>
                    @empty
                        <div style="text-align:center; padding:16px; font-size:12px; color:var(--text-dim,#8aaa95);">
                            <i class="fa-solid fa-inbox" style="margin-bottom:4px; display:block; font-size:18px;"></i>
                            Aucun document importé
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Derniers retours / commentaires --}}
            <div class="chart-card" style="min-height:120px;">
                <div class="chart-title" style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-comments" style="color:#c07b28;"></i> Derniers retours
                    @if($commentairesEnAttente > 0)
                        <span style="background:#c07b28; color:#fff; font-size:10px; padding:2px 7px; border-radius:10px; font-weight:700;">{{ $commentairesEnAttente }}</span>
                    @endif
                </div>
                <div class="chart-sub">Commentaires, suggestions et plaintes</div>
                <div style="max-height:140px; overflow-y:auto; margin-top:8px;">
                    @forelse($derniersCommentaires as $c)
                        <a href="{{ url('/CommentairesAdmi') }}"
                           style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--border,#e8efe9); text-decoration:none; color:inherit; cursor:pointer;">
                            <span style="width:8px; height:8px; border-radius:50%; flex-shrink:0;
                                background:{{ $c->type === 'plainte' ? '#c44030' : ($c->type === 'suggestion' ? '#267a47' : '#2e7fbb') }};"></span>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:12px; font-weight:600; color:var(--text,#1a2d22); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $c->nom ?: $c->email }} — {{ Str::limit($c->message, 50) }}
                                </div>
                                <div style="font-size:11px; color:var(--text-dim,#8aaa95);">
                                    {{ ucfirst($c->type) }} · {{ $c->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-right" style="font-size:10px; color:var(--text-dim,#8aaa95); flex-shrink:0;"></i>
                        </a>
                    @empty
                        <div style="text-align:center; padding:16px; font-size:12px; color:var(--text-dim,#8aaa95);">
                            <i class="fa-solid fa-inbox" style="margin-bottom:4px; display:block; font-size:18px;"></i>
                            Aucun retours pour le moment
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

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

    var sectData = @json($parSecteur);
    var villData = @json($topVillages);
    var sectColors = {
        Education: '#2e7fbb', Agriculture: '#c07b28', Elevage: '#7b4fba',
        Hydraulique: '#1a7abf', Commerce: '#c06020', Artisanat: '#b54890',
        Energie: '#c8a800', Migration: '#6a8a70', Sante: '#c44030'
    };
    var sectIcons = {
        Education: '\u{1F3EB}', Agriculture: '\u{1F33E}', Elevage: '\u{1F404}',
        Hydraulique: '\u{1F4A7}', Commerce: '\u{1F6D2}', Artisanat: '\u{1F528}',
        Energie: '\u26A1', Migration: '\u2708\uFE0F', Sante: '\u{1F3E5}'
    };

    // ── Secteur (bar) ──
    if (sectData.length > 0 && document.getElementById('ch-sect')) {
        new Chart(document.getElementById('ch-sect'), {
            type: 'bar',
            data: {
                labels: sectData.map(function(s) { return (sectIcons[s.nom] || '\u{1F4CD}') + ' ' + s.nom; }),
                datasets: [{
                    data: sectData.map(function(s) { return s.total; }),
                    backgroundColor: sectData.map(function(s) { return (sectColors[s.nom] || '#267a47') + '44'; }),
                    borderColor: sectData.map(function(s) { return sectColors[s.nom] || '#267a47'; }),
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    }

    // ── Top villages (bar horizontal) ──
    if (villData.length > 0 && document.getElementById('ch-vill')) {
        new Chart(document.getElementById('ch-vill'), {
            type: 'bar',
            data: {
                labels: villData.map(function(v) { return v.village; }),
                datasets: [{
                    data: villData.map(function(v) { return v.total; }),
                    backgroundColor: 'rgba(38,122,71,0.55)',
                    borderColor: '#267a47',
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: Object.assign({}, cfg, { indexAxis: 'y', plugins: Object.assign({}, cfg.plugins, { legend: { display: false } }) })
        });
    }

    // Forcer un redessin des graphiques une fois les dispositions
    // terminées (fontes, media-queries) et à chaque redimensionnement,
    // pour un rendu parfaitement responsive sur mobile/tablette.
    var charts = [].slice.call(document.querySelectorAll('#ch-sect, #ch-vill'))
        .filter(function(c) { return Chart.getChart(c); })
        .map(function(c) { return Chart.getChart(c); });

    function redrawCharts() {
        charts.forEach(function(ch) { if (ch) ch.resize(); });
    }
    window.addEventListener('resize', function() { requestAnimationFrame(redrawCharts); });
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function() { requestAnimationFrame(redrawCharts); });
    }
    requestAnimationFrame(redrawCharts);

});
</script>
@endsection
