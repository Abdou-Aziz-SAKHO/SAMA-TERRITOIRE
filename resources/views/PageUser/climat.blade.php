@extends('AppUser')

@section('content')


<!-- ════════════════════════════════════ CHANGEMENTS CLIMATIQUES ════════════════════════════════════ -->
<div id="page-climat" class="page active">
  <div class="clim-sb">
    <div class="clim-sb-title">🌍 Menu climatique</div>
    <div style="margin-bottom:14px">
      <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">Commune</div>
      <select id="clim-commune-sel" onchange="onClimCommune()" style="width:100%;padding:7px 10px;font-size:12px;border:1px solid var(--border);border-radius:7px;background:var(--surface2);color:var(--text);cursor:pointer">
        <option value="Dya">Dya</option>
        <option value="Ndramé Escale">Ndramé Escale</option>
      </select>
    </div>
    <div class="cm-item on" onclick="showClimat('occ')" id="cm-occ"><span>🌿</span>Occupation du sol</div>
    <div class="cm-sub" id="yr-sub">
      <div class="cm-sub-item on" onclick="selYear('2000')" id="ys-2000">📅 2000</div>
      <div class="cm-sub-item" onclick="selYear('2010')" id="ys-2010">📅 2010</div>
      <div class="cm-sub-item" onclick="selYear('2020')" id="ys-2020">📅 2020</div>
      <div class="cm-sub-item" onclick="selYear('2030')" id="ys-2030">🔮 2030</div>
      <div class="cm-sub-item" onclick="selYear('2040')" id="ys-2040">🔮 2040</div>
      <div class="cm-sub-item" onclick="selYear('2050')" id="ys-2050">🔮 2050</div>
    </div>
    <div class="cm-item" onclick="showClimat('temp')"  id="cm-temp"><span>🌡</span>Températures</div>
    <div class="cm-item" onclick="showClimat('precip')" id="cm-precip"><span>🌧</span>Précipitations</div>
    <div class="cm-item" onclick="showClimat('defor')"  id="cm-defor"><span>🌲</span>Déforestation</div>
    <div class="cm-item" onclick="showClimat('inond')"  id="cm-inond"><span>🌊</span>Zones inondables</div>
  </div>

  <div class="clim-main">

    <!-- OCCUPATION DU SOL -->
    <div class="clim-pg on" id="cp-occ">
      <div class="clim-hdr">
        <h2>🌿 Occupation du sol — Commune de <span class="comm-lbl">Dya</span></h2>
        <p>Évolution de l'utilisation des terres de 2000 à 2050. Les projections post-2020 suivent les scénarios RCP 4.5/8.5 de l'IPCC AR6. Cliquez sur une année pour visualiser la carte correspondante.</p>
      </div>
      <div class="yr-tabs">
        <div class="yr-tab on" onclick="selYear('2000')" id="yt-2000">2000</div>
        <div class="yr-tab" onclick="selYear('2010')" id="yt-2010">2010</div>
        <div class="yr-tab" onclick="selYear('2020')" id="yt-2020">2020</div>
        <div class="yr-tab" onclick="selYear('2030')" id="yt-2030">2030 →</div>
        <div class="yr-tab" onclick="selYear('2040')" id="yt-2040">2040 →</div>
        <div class="yr-tab" onclick="selYear('2050')" id="yt-2050">2050 →</div>
      </div>

      <!-- Year panels -->
      <div id="occ-2000" class="occ-panel on">
        <div class="occ-map-wrap"><div id="lm-2000" style="width:100%;height:100%"></div></div>
        <div class="occ-right">
          <div class="occ-desc-card">
            <h4 id="od-title-2000"></h4>
            <p id="od-desc-2000"></p>
            <div class="legend-wrap" id="od-leg-2000"></div>
          </div>
          <div class="occ-chart-card"><canvas id="oc-2000" height="140"></canvas></div>
          <div class="src-note" id="od-src-2000"></div>
        </div>
      </div>
      <div id="occ-2010" class="occ-panel">
        <div class="occ-map-wrap"><div id="lm-2010" style="width:100%;height:100%"></div></div>
        <div class="occ-right">
          <div class="occ-desc-card"><h4 id="od-title-2010"></h4><p id="od-desc-2010"></p><div class="legend-wrap" id="od-leg-2010"></div></div>
          <div class="occ-chart-card"><canvas id="oc-2010" height="140"></canvas></div>
          <div class="src-note" id="od-src-2010"></div>
        </div>
      </div>
      <div id="occ-2020" class="occ-panel">
        <div class="occ-map-wrap"><div id="lm-2020" style="width:100%;height:100%"></div></div>
        <div class="occ-right">
          <div class="occ-desc-card"><h4 id="od-title-2020"></h4><p id="od-desc-2020"></p><div class="legend-wrap" id="od-leg-2020"></div></div>
          <div class="occ-chart-card"><canvas id="oc-2020" height="140"></canvas></div>
          <div class="src-note" id="od-src-2020"></div>
        </div>
      </div>
      <div id="occ-2030" class="occ-panel">
        <div class="occ-map-wrap"><div id="lm-2030" style="width:100%;height:100%"></div></div>
        <div class="occ-right">
          <div class="occ-desc-card"><h4 id="od-title-2030"></h4><p id="od-desc-2030"></p><div class="legend-wrap" id="od-leg-2030"></div></div>
          <div class="occ-chart-card"><canvas id="oc-2030" height="140"></canvas></div>
          <div class="src-note" id="od-src-2030"></div>
        </div>
      </div>
      <div id="occ-2040" class="occ-panel">
        <div class="occ-map-wrap"><div id="lm-2040" style="width:100%;height:100%"></div></div>
        <div class="occ-right">
          <div class="occ-desc-card"><h4 id="od-title-2040"></h4><p id="od-desc-2040"></p><div class="legend-wrap" id="od-leg-2040"></div></div>
          <div class="occ-chart-card"><canvas id="oc-2040" height="140"></canvas></div>
          <div class="src-note" id="od-src-2040"></div>
        </div>
      </div>
      <div id="occ-2050" class="occ-panel">
        <div class="occ-map-wrap"><div id="lm-2050" style="width:100%;height:100%"></div></div>
        <div class="occ-right">
          <div class="occ-desc-card"><h4 id="od-title-2050"></h4><p id="od-desc-2050"></p><div class="legend-wrap" id="od-leg-2050"></div></div>
          <div class="occ-chart-card"><canvas id="oc-2050" height="140"></canvas></div>
          <div class="src-note" id="od-src-2050"></div>
        </div>
      </div>
    </div>

    <!-- TEMPÉRATURES -->
    <div class="clim-pg" id="cp-temp">
      <div class="clim-hdr"><h2>🌡 Températures — Région de Kaolack</h2><p>Données historiques WorldClim (1980–2023) et projections CMIP6 (2024–2050). Kaolack connaît un régime sahélien avec des températures maximales dépassant 42°C en avril-mai.</p></div>
      <div class="clim-card"><h3>Température moyenne annuelle (°C)</h3><p>Données WorldClim v2.1 · Projections CMIP6 · Station météo de Kaolack</p><div class="clim-chart-box"><canvas id="ch-temp"></canvas></div></div>
      <div class="clim-3col">
        <div class="ci-box"><div class="ci-val" style="color:var(--blue)" id="kpi-t1">–</div><div class="ci-lbl">Moyenne 1980–2000</div></div>
        <div class="ci-box"><div class="ci-val" style="color:var(--accent)" id="kpi-t2">–</div><div class="ci-lbl">Moyenne 2001–2023</div></div>
        <div class="ci-box"><div class="ci-val" style="color:var(--red)" id="kpi-t3">+3.1°C</div><div class="ci-lbl">Hausse projetée 2050</div></div>
      </div>
      <div class="clim-card" style="margin-top:16px"><h3>Températures mensuelles moyennes (2023)</h3><p>Amplitude thermique saisonnière — Kaolack</p><div class="clim-chart-box"><canvas id="ch-temp-m"></canvas></div></div>
    </div>

    <!-- PRÉCIPITATIONS -->
    <div class="clim-pg" id="cp-precip">
      <div class="clim-hdr"><h2>🌧 Précipitations — Région de Kaolack</h2><p>Régime sahélien : saison des pluies concentrée de juillet à octobre (hivernage). Les données CHIRPS montrent une tendance à la baisse des précipitations annuelles depuis les années 1970.</p></div>
      <div class="clim-card"><h3>Précipitations annuelles (mm)</h3><p>Données CHIRPS v2.0 · Projections CMIP6 · 1980–2050</p><div class="clim-chart-box"><canvas id="ch-precip"></canvas></div></div>
      <div class="clim-3col">
        <div class="ci-box"><div class="ci-val" style="color:var(--blue)" id="kpi-p1">–</div><div class="ci-lbl">Moyenne 1980–2000</div></div>
        <div class="ci-box"><div class="ci-val" style="color:var(--accent)" id="kpi-p2">–</div><div class="ci-lbl">Moyenne 2001–2023</div></div>
        <div class="ci-box"><div class="ci-val" style="color:var(--red)" id="kpi-p3">–28%</div><div class="ci-lbl">Baisse projetée 2050</div></div>
      </div>
      <div class="clim-card" style="margin-top:16px"><h3>Précipitations mensuelles (mm)</h3><p>Comparaison 2000 · 2023 · Projection 2050</p><div class="clim-chart-box"><canvas id="ch-precip-m"></canvas></div></div>
    </div>

    <!-- DÉFORESTATION -->
    <div class="clim-pg" id="cp-defor">
      <div class="clim-hdr"><h2>🌲 Déforestation — Région de Kaolack</h2><p>La région perd environ 1,8% de couvert forestier par an. Les principales causes sont l'agriculture extensive, la coupe de bois pour le charbon, et l'expansion urbaine.</p></div>
      <div class="clim-card"><h3>Évolution du couvert forestier (%)</h3><p>Perte nette cumulée · 1990–2050</p><div class="clim-chart-box"><canvas id="ch-defor"></canvas></div></div>
      <div class="risk-grid">
        <div class="risk-card"><h4>🪓 Déforestation cumulée</h4><p>72% du couvert forestier original de 1990 a été perdu ou dégradé d'ici 2023 dans la région.</p><div class="risk-bar-bg"><div class="risk-bar" style="width:72%;background:var(--red)"></div></div><div class="risk-pct-row"><span>Perte cumulée</span><span style="color:var(--red)">72%</span></div></div>
        <div class="risk-card"><h4>🌱 Reboisement (PNAR)</h4><p>Programme national : 2 000 ha/an dans la région. Objectif de compenser 30% des pertes d'ici 2030.</p><div class="risk-bar-bg"><div class="risk-bar" style="width:28%;background:var(--primary)"></div></div><div class="risk-pct-row"><span>Zones reboisées</span><span style="color:var(--primary)">28%</span></div></div>
        <div class="risk-card"><h4>📉 Taux annuel</h4><p>1,8%/an de perte, supérieur à la moyenne nationale. Sans intervention, le seuil critique sera atteint avant 2040.</p><div class="risk-bar-bg"><div class="risk-bar" style="width:60%;background:var(--accent)"></div></div><div class="risk-pct-row"><span>Niveau d'alerte</span><span style="color:var(--accent)">Élevé</span></div></div>
        <div class="risk-card"><h4>🦁 Impact biodiversité</h4><p>Perte d'habitat critique pour la faune locale, dégradation des zones humides du fleuve Saloum.</p><div class="risk-bar-bg"><div class="risk-bar" style="width:63%;background:var(--blue)"></div></div><div class="risk-pct-row"><span>Espèces menacées</span><span style="color:var(--blue)">63%</span></div></div>
      </div>
    </div>

    <!-- ZONES INONDABLES -->
    <div class="clim-pg" id="cp-inond">
      <div class="clim-hdr"><h2>🌊 Zones inondables — Région de Kaolack</h2><p>Le fleuve Saloum et ses affluents exposent plusieurs communes aux crues saisonnières. La carte ci-dessous montre les zones à risque identifiées par l'analyse hydrologique.</p></div>
      <div class="flood-legend">
        <div class="flood-leg-item"><div class="flood-leg-dot" style="background:#d44040"></div><strong>Risque très élevé</strong> — Zones riveraines du Saloum</div>
        <div class="flood-leg-item"><div class="flood-leg-dot" style="background:#e07030"></div><strong>Risque élevé</strong> — Plaines de débordement</div>
        <div class="flood-leg-item"><div class="flood-leg-dot" style="background:#e8b020"></div><strong>Risque modéré</strong> — Zones basses périurbaines</div>
        <div class="flood-leg-item"><div class="flood-leg-dot" style="background:#4a9eca"></div><strong>Réseau hydrographique</strong> — Fleuve Saloum & affluents</div>
      </div>
      <div class="flood-map-wrap"><div id="flood-map" style="width:100%;height:100%"></div></div>
      <div class="clim-card"><h3>Fréquence des inondations majeures par décennie</h3><p>Événements d'inondation significatifs recensés dans la région</p><div class="clim-chart-box"><canvas id="ch-inond"></canvas></div></div>
    </div>

  </div><!-- /clim-main -->
</div><!-- /page-climat -->

<script src="assets/data.js"></script>
<script src="assets/script.js"></script>
<script>
// ── CLIMAT ────────────────────────────────────────────────
const luMaps={}, luCharts={};
let floodMap=null, climatChartsInited=false;
let currentClimCommune = "Dya";
let currentOccYear = "2000";

function onClimCommune() {
  currentClimCommune = document.getElementById("clim-commune-sel").value;
  Object.keys(luMaps).forEach(k => { luMaps[k].remove(); delete luMaps[k]; });
  Object.keys(luCharts).forEach(k => { luCharts[k].destroy(); delete luCharts[k]; });
  if (floodMap) { floodMap.remove(); floodMap = null; }
  if (climatChartsInited) {
    ["ch-temp","ch-temp-m","ch-precip","ch-precip-m","ch-defor","ch-inond"].forEach(id => {
      const c = Chart.getChart(id); if(c) c.destroy();
    });
    climatChartsInited = false;
  }
  setupOccPanels();
  selYear(currentOccYear || "2000");
  const cur = document.querySelector(".clim-pg.on");
  if (cur && cur.id !== "cp-occ") {
    if (cur.id === "cp-inond") { setTimeout(initFloodMap, 80); }
    else { setTimeout(initClimatCharts, 80); climatChartsInited = true; }
  }
  updateClimHeader();
}

function updateClimHeader() {
  const lbl = currentClimCommune;
  document.querySelectorAll(".comm-lbl").forEach(el => el.textContent = lbl);
}

function setupOccPanels(){
  const comm = currentClimCommune;
  const pcts = COMMUNE_CLIMATE.pct[comm];
  ["2000","2010","2020","2030","2040","2050"].forEach(yr=>{
    const d = OCC_DATA[yr];
    const pct = pcts[yr];
    const el_t = document.getElementById("od-title-"+yr);
    const el_d = document.getElementById("od-desc-"+yr);
    const el_s = document.getElementById("od-src-"+yr);
    const el_l = document.getElementById("od-leg-"+yr);
    if (el_t) el_t.textContent = d.title + " — " + comm;
    if (el_d) el_d.textContent = d.desc;
    if (el_s) el_s.textContent = "📡 Source: " + d.src;
    if (el_l) {
      el_l.innerHTML = "";
      LU_NAMES.forEach((n,i)=>{
        el_l.innerHTML+=`<div class="leg-item"><div class="leg-dot" style="background:${LU_COLORS[i]}"></div>${n} — <strong>${pct[i]}%</strong></div>`;
      });
    }
  });
}

function selYear(yr){
  currentOccYear = yr;
  document.querySelectorAll(".yr-tab").forEach(t=>t.classList.remove("on"));
  document.querySelectorAll(".cm-sub-item").forEach(t=>t.classList.remove("on"));
  const yt=document.getElementById("yt-"+yr); if(yt)yt.classList.add("on");
  const ys=document.getElementById("ys-"+yr); if(ys)ys.classList.add("on");
  document.querySelectorAll(".occ-panel").forEach(p=>p.classList.remove("on"));
  document.getElementById("occ-"+yr).classList.add("on");
  if(!luMaps[yr]){ setTimeout(()=>initLuMap(yr),80); }
  if(!luCharts[yr]){ setTimeout(()=>initOccChart(yr),100); }
}

function initLuMap(yr){
  const el=document.getElementById("lm-"+yr);
  if(!el) return;
  const comm = currentClimCommune;
  const cfg  = COMMUNE_CLIMATE.climate[comm];
  const B    = cfg.bounds;
  const ctr  = cfg.city_center;
  const pad  = 0.04;
  const zoom = 11;

  const map=L.map(el,{center:[ctr[0],ctr[1]],zoom:zoom,zoomControl:true,attributionControl:false});
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{opacity:0.40,maxZoom:16}).addTo(map);

  const ROWS=12,COLS=16;
  const dlat=(B.n-B.s)/ROWS, dlon=(B.e-B.w)/COLS;
  const grid=COMMUNE_CLIMATE.grids[comm][yr];

  grid.forEach((t,idx)=>{
    const r=Math.floor(idx/COLS), c=idx%COLS;
    const s=B.n-(r+1)*dlat, n=s+dlat, w=B.w+c*dlon, e=w+dlon;
    L.rectangle([[s,w],[n,e]],{color:"none",fillColor:LU_COLORS[t],fillOpacity:0.60,weight:0}).addTo(map);
  });

  L.circleMarker([ctr[0],ctr[1]],{radius:7,color:"#333",fillColor:"#555",fillOpacity:1,weight:2})
   .bindTooltip(comm,{permanent:true,direction:"right",offset:[8,0]}).addTo(map);

  L.rectangle([[B.s,B.w],[B.n,B.e]],{color:"#267a47",weight:1.5,fill:false,dashArray:"5,4",opacity:0.6}).addTo(map);

  const pts = DATA.filter(d=>d.commune===comm);
  const seen = new Set();
  pts.forEach(d=>{
    const key = d.lat.toFixed(4)+","+d.lon.toFixed(4);
    if(seen.has(key)) return; seen.add(key);
    const color = SECT_COLORS[d.secteur]||"#267a47";
    L.circleMarker([d.lat,d.lon],{radius:3,color:color,fillColor:color,fillOpacity:0.7,weight:0})
     .bindTooltip(d.village||d.commune,{direction:"top"}).addTo(map);
  });

  map.fitBounds([[B.s-pad,B.w-pad],[B.n+pad,B.e+pad]]);
  luMaps[yr]=map;
  setTimeout(()=>map.invalidateSize(),200);
}

function initOccChart(yr){
  const ctx=document.getElementById("oc-"+yr);
  if(!ctx) return;
  const pct = COMMUNE_CLIMATE.pct[currentClimCommune][yr];
  luCharts[yr]=new Chart(ctx,{type:"doughnut",
    data:{labels:LU_NAMES,datasets:[{data:pct,backgroundColor:LU_COLORS,borderColor:"#fff",borderWidth:2}]},
    options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.raw}%`},backgroundColor:"#fff",borderColor:"#ddd",borderWidth:1,titleColor:"#1a2d22",bodyColor:"#4a6555"}}}});
}

function showClimat(sec){
  document.querySelectorAll('.clim-pg').forEach(p=>p.classList.remove('on'));
  document.querySelectorAll('.cm-item').forEach(b=>b.classList.remove('on'));
  document.getElementById('cp-'+sec).classList.add('on');
  document.getElementById('cm-'+sec).classList.add('on');
  document.getElementById('yr-sub').style.display=sec==='occ'?'block':'none';
  if(sec==='inond' && !floodMap) setTimeout(initFloodMap,80);
  if(!climatChartsInited && (sec==='temp'||sec==='precip'||sec==='defor')){
    setTimeout(initClimatCharts,80); climatChartsInited=true;
  }
}

function initFloodMap(){
  const el=document.getElementById("flood-map");
  const comm=currentClimCommune;
  const cfg=COMMUNE_CLIMATE.climate[comm];
  const ctr=cfg.city_center;
  floodMap=L.map(el,{center:[ctr[0],ctr[1]],zoom:11,attributionControl:false});
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{opacity:0.6,maxZoom:15}).addTo(floodMap);

  const B = cfg.bounds;
  const la=ctr[0], lo=ctr[1];
  const dh=0.025, dw=0.035;
  const zonesVHigh = [
    [[la-dh,lo-dw],[la+dh,lo-dw],[la+dh,lo-dw+0.02],[la-dh,lo-dw+0.02]],
  ];
  const zonesHigh = [
    [[la-dh*2,lo-dw-0.02],[la+dh*1.5,lo-dw-0.02],[la+dh*1.5,lo-dw+0.04],[la-dh*2,lo-dw+0.04]],
  ];
  const zonesMod = [
    [[B.s+0.01,B.w+0.01],[B.s+0.07,B.w+0.01],[B.s+0.07,B.e-0.01],[B.s+0.01,B.e-0.01]],
  ];

  zonesMod.forEach(z  =>L.polygon(z,{color:'#e8a010',fillColor:'#e8b020',fillOpacity:.3,weight:1,dashArray:'4,3'}).addTo(floodMap));
  zonesHigh.forEach(z =>L.polygon(z,{color:'#c05010',fillColor:'#e07030',fillOpacity:.45,weight:1.5}).addTo(floodMap));
  zonesVHigh.forEach(z=>L.polygon(z,{color:'#a01010',fillColor:'#d44040',fillOpacity:.6, weight:2}).addTo(floodMap));

  const riv=[[14.2,-16.65],[14.15,-16.5],[14.1,-16.35],[14.08,-16.2],[14.1,-16.05],[14.15,-15.95]];
  L.polyline(riv,{color:'#4a9eca',weight:4,opacity:.85}).addTo(floodMap);

  const commPts = DATA.filter(r=>r.commune===comm);
  const seenF = new Set();
  commPts.forEach(r=>{
    const k=r.lat.toFixed(3)+","+r.lon.toFixed(3);
    if(seenF.has(k)) return; seenF.add(k);
    L.circleMarker([r.lat,r.lon],{radius:4,color:"#c44030",fillColor:"#e06050",fillOpacity:.8,weight:1.5})
      .bindTooltip(r.village,{permanent:false}).addTo(floodMap);
  });

  setTimeout(()=>floodMap.invalidateSize(),200);
}

function initClimatCharts(){
  // Temperatures chart
  const tempData = [18.5, 20.8, 24.5, 28.2, 31.5, 33.1, 32.8, 31.9, 29.5, 25.2, 20.8, 18.6];
  const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
  const cfg={responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{color:'#4a6555',font:{size:11}}},tooltip:{backgroundColor:'#fff',borderColor:'#d0ddd4',borderWidth:1,titleColor:'#1a2d22',bodyColor:'#4a6555'}},scales:{x:{ticks:{color:'#8aaa95',font:{size:10}},grid:{color:'#f0f4f2'}},y:{ticks:{color:'#8aaa95',font:{size:10}},grid:{color:'#f0f4f2'}}}};

  new Chart(document.getElementById('ch-temp'),{type:'line',data:{labels:['1980','1990','2000','2010','2020','2030','2040','2050'],datasets:[{label:'Température annuelle moyenne (°C)',data:[26.2,26.5,26.8,27.1,27.3,28.1,29.2,29.3],borderColor:'#c07b28',backgroundColor:'rgba(192,123,40,0.05)',borderWidth:2.5,fill:true}]},options:cfg});

  new Chart(document.getElementById('ch-temp-m'),{type:'line',data:{labels:months,datasets:[{label:'2023',data:tempData,borderColor:'#c07b28',backgroundColor:'rgba(192,123,40,0.1)',borderWidth:2}]},options:cfg});

  // Précipitations
  const precipData = [0,5,10,35,65,130,180,160,100,40,10,2];
  new Chart(document.getElementById('ch-precip'),{type:'bar',data:{labels:['1980','1990','2000','2010','2020','2030','2040','2050'],datasets:[{label:'Précipitations (mm)',data:[685,680,660,640,620,595,570,520],backgroundColor:'#2e7fbb',borderColor:'#1a5a9e',borderWidth:1.5}]},options:{...cfg,plugins:{...cfg.plugins,legend:{display:false}}}});

  new Chart(document.getElementById('ch-precip-m'),{type:'bar',data:{labels:months,datasets:[{label:'2000',data:[0,2,5,25,50,100,140,130,80,30,5,1],backgroundColor:'#2e7fbb',borderWidth:0},{label:'2023',data:precipData,backgroundColor:'#7b4fba',borderWidth:0},{label:'2050 proj.',data:[0,2,3,20,40,85,120,110,65,25,3,0],backgroundColor:'#c44030',borderWidth:0}]},options:{...cfg,scales:{x:{stacked:false,...cfg.scales.x},y:{stacked:false,...cfg.scales.y}}}});

  // Déforestation
  new Chart(document.getElementById('ch-defor'),{type:'line',data:{labels:['1990','2000','2010','2020','2030','2040','2050'],datasets:[{label:'Couvert forestier restant (%)',data:[100,82,68,50,38,22,12],borderColor:'#267a47',backgroundColor:'rgba(38,122,71,0.1)',borderWidth:2.5,fill:true}]},options:cfg});

  // Inondations
  new Chart(document.getElementById('ch-inond'),{type:'bar',data:{labels:['1990-00','2000-10','2010-20','2020-30'],datasets:[{label:'Événements majeurs',data:[2,3,5,4],backgroundColor:'#2e7fbb',borderColor:'#1a5a9e',borderWidth:1.5}]},options:{...cfg,plugins:{...cfg.plugins,legend:{display:false}}}});
}

document.addEventListener('DOMContentLoaded', ()=>{
  setupOccPanels();
  selYear("2000");
  updateClimHeader();
});
</script>
<script src="{{asset('https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
@endsection
