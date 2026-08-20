@extends('AppUser')

@section('content')
    <div id="page-carto" class="page">
        <div class="sidebar">
            <div class="sb-hdr">
                <div class="sb-title">🔍 Filtres géographiques</div>
                <div class="sb-sub">Navigation en cascade par secteur</div>
            </div>
            <div class="sb-body">
                <div>
                    <div class="f-label"><span class="f-step on" id="s1">1</span>Région</div>
                    <select id="sel-region" onchange="onRegion()">
                        <option value="kaolack">Kaolack</option>
                    </select>
                </div>
                <div>
                    <div class="f-label"><span class="f-step" id="s2">2</span>Département</div>
                    <select id="sel-dept" onchange="onDept()" disabled>
                        <option value="">— Sélectionner —</option>
                    </select>
                </div>
                <div id="g-commune" style="display:none">
                    <div class="f-label"><span class="f-step" id="s3">3</span>Commune</div>
                    <div class="commune-list" id="commune-list"></div>
                </div>
                <div id="g-village" style="display:none">
                    <div class="f-label"><span class="f-step" id="s4">4</span>Village</div>
                    <select id="sel-village" onchange="onVillage()">
                        <option value="">Tous les villages</option>
                    </select>
                </div>
                <div id="g-secteur" style="display:none">
                    <div class="f-label"><span class="f-step" id="s5">5</span>Secteur</div>
                    <div class="sect-grid" id="sect-grid"></div>
                </div>
                <div id="g-type" style="display:none">
                    <div class="f-label"><span class="f-step" id="s6">6</span>Type d'établissement</div>
                    <div class="type-list" id="type-list"></div>
                </div>
                <button class="btn-reset" onclick="resetF()">↺ Réinitialiser les filtres</button>
                <div class="res-count" id="res-count">Sélectionnez une région pour commencer</div>
            </div>
        </div>
        <div id="map-wrap">
            <div id="main-map"></div>
            <div class="map-info">
                <strong>Légende</strong>
                <div id="leg-content" style="font-size:11px;color:var(--text-muted)">Sélectionnez un secteur</div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Si l'app est servie en page dédiée, initialise la carte
        if (typeof initMap === 'function') {
            try { initMap(); } catch(e){ console.error('initMap error', e); }
        }
    });
</script>
@endsection
