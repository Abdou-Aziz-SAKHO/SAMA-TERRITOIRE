@extends('AppUser')


@section('content')
<div id="page-accueil" class="page active">
  <div class="hero">
    <div class="hero-chip"><div class="pulse"></div>Plateforme WebSIG — Kaolack</div>
    <h1>Système d'Information<br>Géographique de <em>Kaolack</em></h1>
    <p>Plateforme interactive pour la gestion des infrastructures et l'analyse environnementale de la région de Kaolack, Sénégal — données de terrain 2022, projections climatiques 2050.</p>
    <div class="hero-kpis">
      <div class="kpi-item"><div class="kpi-num" id="k-total">–</div><div class="kpi-lbl">Infrastructures</div></div>
      <div class="kpi-item"><div class="kpi-num" id="k-villages">–</div><div class="kpi-lbl">Villages couverts</div></div>
      <div class="kpi-item"><div class="kpi-num" id="k-secteurs">–</div><div class="kpi-lbl">Secteurs d'activité</div></div>
      <div class="kpi-item"><div class="kpi-num">2022</div><div class="kpi-lbl">Année de collecte</div></div>
    </div>
    <div class="modules">
      <div class="mod" style="--mod-color:var(--primary)" data-href="{{ url('/cartographie') }}">
        <div class="mod-icon">🗺</div>
        <div class="mod-title">Cartographie interactive</div>
        <div class="mod-desc">Filtres en cascade Région→Département→Commune→Village→Secteur→Type. Fiches détaillées par infrastructure.</div>
        <div class="mod-arrow">→</div>
      </div>
      <div class="mod" style="--mod-color:var(--blue)" data-href="{{ url('/statistiques') }}">
        <div class="mod-icon">📊</div>
        <div class="mod-title">Tableau de bord statistique</div>
        <div class="mod-desc">Indicateurs clés, répartitions sectorielles, analyses de l'éducation, hydraulique et santé.</div>
        <div class="mod-arrow">→</div>
      </div>
      <div class="mod" style="--mod-color:var(--accent)" data-href="{{ url('/climat') }}">
        <div class="mod-icon">🌍</div>
        <div class="mod-title">Changements Climatiques</div>
        <div class="mod-desc">Cartes d'occupation du sol 2000–2050, températures, précipitations, déforestation, zones inondables.</div>
        <div class="mod-arrow">→</div>
      </div>
      <div class="mod" style="--mod-color:var(--purple)">
        <div class="mod-icon">📡</div>
        <div class="mod-title">Sources & Méthodes</div>
        <div class="mod-desc">KoboToolbox · OSM · MODIS · WorldClim · CHIRPS · IPCC AR6 · Données terrain par commune.</div>
        <div class="mod-arrow">→</div>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.mod[data-href]').forEach(el=>{
      el.style.cursor = 'pointer';
      el.addEventListener('click', ()=>{
        const h = el.getAttribute('data-href'); if(h) window.location.href = h;
      });
    });
  });
</script>
@endsection
