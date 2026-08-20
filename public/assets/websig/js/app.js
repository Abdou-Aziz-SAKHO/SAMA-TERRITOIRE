// WebSIG Kaolack - Logique applicative (navigation, filtres, cartes, graphiques)
// Dépend de data-infrastructures.js et data-climate.js (à charger avant ce fichier)

const OCC_DATA = {
    2000: {
        pct: [42, 31, 8, 14, 5],
        title: "Situation 2000",
        src: "MODIS Terra Land Cover (MCD12Q1) · 500m",
        desc: "En 2000, la région présente un couvert végétal dense dominé par la savane arborée. L'agriculture occupe principalement les zones proches des cours d'eau. L'urbanisation reste limitée au chef-lieu de Kaolack.",
    },
    2010: {
        pct: [36, 35, 11, 14, 4],
        title: "Situation 2010",
        src: "MODIS Terra Land Cover · 500m",
        desc: "La décennie 2000–2010 révèle une expansion agricole au détriment de la végétation naturelle. La croissance urbaine s'accélère, reflétant l'essor démographique de la région.",
    },
    2020: {
        pct: [29, 37, 15, 16, 3],
        title: "Situation 2020",
        src: "Sentinel-2 / Google Earth Engine · 2020",
        desc: "En 2020, la dégradation du couvert végétal est notable. La déforestation et la pression agricole réduisent les espaces naturels, tandis que l'urbanisation gagne les communes périurbaines comme Dya.",
    },
    2030: {
        pct: [23, 36, 19, 19, 3],
        title: "Projection 2030 — RCP 4.5",
        src: "Projection CMIP6 · Incertitude ±3%",
        desc: "Sous un scénario d'émissions modérées, la végétation continue de reculer. L'expansion urbaine et la dégradation des sols progressent, menaçant les périmètres irrigués.",
    },
    2040: {
        pct: [17, 32, 23, 25, 3],
        title: "Projection 2040 — RCP 8.5",
        src: "Projection CMIP6 · Risque élevé",
        desc: "Sous un scénario d'émissions élevées, la région subit une aridification marquée. Les terres agricoles pluviales régressent significativement, aggravant les risques pour la sécurité alimentaire.",
    },
    2050: {
        pct: [12, 28, 28, 29, 3],
        title: "Projection 2050 — RCP 8.5",
        src: "⚠️ Projection critique — Action urgente",
        desc: "En 2050, sans action climatique majeure, la région pourrait perdre plus de 70% de son couvert végétal naturel par rapport à 2000. La désertification menace les conditions de vie des communautés rurales.",
    },
};
const LU_COLORS = ["#5cb87a", "#d4a843", "#d45040", "#c4a060", "#5aa8d8"];
const LU_NAMES = [
    "Végétation/Savane",
    "Agriculture",
    "Zone urbaine",
    "Sol nu/Dégradé",
    "Plans d'eau",
];

const SECT_COLORS = {
    Education: "#2e7fbb",
    Agriculture: "#c07b28",
    Elevage: "#7b4fba",
    Hydraulique: "#1a7abf",
    Commerce: "#c06020",
    Artisanat: "#b54890",
    Energie: "#c8a800",
    Migration: "#6a8a70",
    Santé: "#c44030",
};
const SECT_ICONS = {
    Education: "🏫",
    Agriculture: "🌾",
    Elevage: "🐄",
    Hydraulique: "💧",
    Commerce: "🛒",
    Artisanat: "🔨",
    Energie: "⚡",
    Migration: "✈️",
    Santé: "🏥",
};

// ── NAVIGATION ────────────────────────────────────────────
let mapMain = null,
    mapInited = false,
    statsInited = false,
    climatInited = false;

function showPage(pg) {
    document
        .querySelectorAll(".page")
        .forEach((p) => p.classList.remove("active"));
    document
        .querySelectorAll("nav button")
        .forEach((b) => b.classList.remove("active"));
    document.getElementById("page-" + pg).classList.add("active");
    const idx = { accueil: 0, carto: 1, stats: 2, climat: 3 };
    document.querySelectorAll("nav button")[idx[pg]].classList.add("active");
    if (pg === "carto" && !mapInited) {
        setTimeout(initMap, 50);
        mapInited = true;
    }
    if (pg === "stats" && !statsInited) {
        setTimeout(initStats, 50);
        statsInited = true;
    }
    if (pg === "climat" && !climatInited) {
        setTimeout(initClimat, 50);
        climatInited = true;
    }
}

// ── FILTER STATE ──────────────────────────────────────────
const F = { dept: "", commune: "", village: "", secteur: "", type: "" };

function uniq(field, filter = {}) {
    return [
        ...new Set(
            DATA.filter((r) =>
                Object.entries(filter).every(([k, v]) => !v || r[k] === v),
            )
                .map((r) => r[field])
                .filter(Boolean),
        ),
    ].sort();
}
function filtered() {
    return DATA.filter(
        (r) =>
            (!F.dept || r.dept === F.dept) &&
            (!F.commune || r.commune === F.commune) &&
            (!F.village || r.village === F.village) &&
            (!F.secteur || r.secteur === F.secteur) &&
            (!F.type || r.type === F.type),
    );
}

function onRegion() {
    F.dept = "";
    F.commune = "";
    F.village = "";
    F.secteur = "";
    F.type = "";
    const dSel = document.getElementById("sel-dept");
    dSel.innerHTML = '<option value="">— Sélectionner —</option>';
    uniq("dept").forEach((d) => {
        const o = document.createElement("option");
        o.value = d;
        o.textContent = d;
        dSel.appendChild(o);
    });
    dSel.disabled = false;
    document.getElementById("s2").classList.add("on");
    hide(["g-commune", "g-village", "g-secteur", "g-type"]);
    updateMap(filtered());
}
function onDept() {
    F.dept = document.getElementById("sel-dept").value;
    F.commune = "";
    F.village = "";
    F.secteur = "";
    F.type = "";
    if (!F.dept) return;
    document.getElementById("s3").classList.add("on");
    renderCommunes();
    document.getElementById("g-commune").style.display = "block";
    hide(["g-village", "g-secteur", "g-type"]);
    updateMap(filtered());
}
function renderCommunes() {
    const el = document.getElementById("commune-list");
    el.innerHTML = "";
    uniq("commune", { dept: F.dept }).forEach((c) => {
        const d = document.createElement("div");
        d.className = "c-item";
        d.textContent = c;
        if (c === F.commune) d.classList.add("sel");
        d.onclick = () => selCommune(c);
        el.appendChild(d);
    });
}
function selCommune(c) {
    F.commune = c;
    F.village = "";
    F.secteur = "";
    F.type = "";
    document
        .querySelectorAll(".c-item")
        .forEach((i) => i.classList.toggle("sel", i.textContent === c));
    document.getElementById("s4").classList.add("on");
    const vs = document.getElementById("sel-village");
    vs.innerHTML = '<option value="">Tous les villages</option>';
    uniq("village", { dept: F.dept, commune: c }).forEach((v) => {
        const o = document.createElement("option");
        o.value = v;
        o.textContent = v;
        vs.appendChild(o);
    });
    document.getElementById("g-village").style.display = "block";
    renderSecteurs();
    document.getElementById("g-secteur").style.display = "block";
    hide(["g-type"]);
    updateMap(filtered());
}
function onVillage() {
    F.village = document.getElementById("sel-village").value;
    F.secteur = "";
    F.type = "";
    document
        .querySelectorAll(".sect-btn")
        .forEach((b) => b.classList.remove("on"));
    hide(["g-type"]);
    updateMap(filtered());
}
function renderSecteurs() {
    const el = document.getElementById("sect-grid");
    el.innerHTML = "";
    uniq("secteur", { dept: F.dept, commune: F.commune }).forEach((s) => {
        const b = document.createElement("div");
        b.className = "sect-btn";
        b.innerHTML = `<span class="sect-icon">${SECT_ICONS[s] || "📍"}</span>${s}`;
        if (s === F.secteur) b.classList.add("on");
        b.onclick = () => selSecteur(s, b);
        el.appendChild(b);
    });
}
function selSecteur(s, btn) {
    if (F.secteur === s) {
        F.secteur = "";
        F.type = "";
        btn.classList.remove("on");
        hide(["g-type"]);
        updateMap(filtered());
        return;
    }
    F.secteur = s;
    F.type = "";
    document
        .querySelectorAll(".sect-btn")
        .forEach((b) => b.classList.remove("on"));
    btn.classList.add("on");
    document.getElementById("s5").classList.add("on");
    const types = uniq("type", {
        dept: F.dept,
        commune: F.commune,
        secteur: s,
    }).filter((t) => t);
    const el = document.getElementById("type-list");
    el.innerHTML = "";
    if (types.length > 0) {
        types.forEach((t) => {
            const d = document.createElement("div");
            d.className = "t-item";
            d.innerHTML = `<span class="t-dot"></span>${t}`;
            if (t === F.type) d.classList.add("sel");
            d.onclick = () => selType(t, d);
            el.appendChild(d);
        });
        document.getElementById("g-type").style.display = "block";
    } else {
        hide(["g-type"]);
    }
    updateLegend(s);
    updateMap(filtered());
}
function selType(t, item) {
    F.type = F.type === t ? "" : t;
    document
        .querySelectorAll(".t-item")
        .forEach((i) => i.classList.remove("sel"));
    if (F.type) item.classList.add("sel");
    document.getElementById("s6").classList.add("on");
    updateMap(filtered());
}
function updateLegend(s) {
    const c = SECT_COLORS[s] || "#267a47";
    document.getElementById("leg-content").innerHTML =
        `<div style="display:flex;align-items:center;gap:6px;margin-top:5px"><div style="width:11px;height:11px;border-radius:50%;background:${c}"></div><span>${s}</span></div>`;
}
function hide(ids) {
    ids.forEach((id) => (document.getElementById(id).style.display = "none"));
}
function resetF() {
    Object.assign(F, {
        dept: "",
        commune: "",
        village: "",
        secteur: "",
        type: "",
    });
    document.getElementById("sel-dept").value = "";
    document.getElementById("sel-dept").disabled = true;
    document.querySelectorAll(".f-step").forEach((s) => {
        if (s.id !== "s1") s.classList.remove("on");
    });
    hide(["g-commune", "g-village", "g-secteur", "g-type"]);
    if (mapMain) {
        clearMarkers();
        mapMain.setView([14.15, -16.07], 11);
    }
    document.getElementById("res-count").innerHTML =
        "Sélectionnez une région pour commencer";
    document.getElementById("leg-content").textContent =
        "Sélectionnez un secteur";
}

// ── MAIN MAP ──────────────────────────────────────────────
let markers = [];
function initMap() {
    mapMain = L.map("main-map", { center: [14.15, -16.07], zoom: 11 });
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
        maxZoom: 19,
        opacity: 0.85,
    }).addTo(mapMain);
    onRegion();
    document.getElementById("sel-dept").value = "Kaolack";
    onDept();
}
function clearMarkers() {
    markers.forEach((m) => m.remove());
    markers = [];
}
function updateMap(recs) {
    if (!mapMain) return;
    clearMarkers();
    const n = recs.length;
    document.getElementById("res-count").innerHTML =
        `<b>${n}</b> infrastructure${n > 1 ? "s" : ""} affichée${n > 1 ? "s" : ""}`;
    recs.forEach((r) => {
        const c = SECT_COLORS[r.secteur] || "#267a47";
        const icon = L.divIcon({
            html: `<div style="width:12px;height:12px;border-radius:50%;background:${c};border:2.5px solid white;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
            className: "",
            iconAnchor: [6, 6],
        });
        const m = L.marker([r.lat, r.lon], { icon });
        const nom = r.nom || r.village || "Infrastructure";
        const si = SECT_ICONS[r.secteur] || "📍";
        m.bindPopup(
            `<div style="min-width:170px;padding:4px">
      <div class="pop-sect" style="background:${c}18;color:${c}">${si} ${r.secteur}${r.type ? " · " + r.type : ""}</div>
      <div class="pop-nom">${nom}</div>
      <div class="pop-loc">📍 ${r.village} — ${r.commune}</div>
      <button class="pop-btn" onclick='openModal(${JSON.stringify(JSON.stringify(r))})'>ℹ Plus d'informations</button>
    </div>`,
            { maxWidth: 250 },
        );
        m.addTo(mapMain);
        markers.push(m);
    });
    if (n > 0) {
        const lats = recs.map((r) => r.lat),
            lons = recs.map((r) => r.lon);
        mapMain.fitBounds([
            [Math.min(...lats) - 0.02, Math.min(...lons) - 0.02],
            [Math.max(...lats) + 0.02, Math.max(...lons) + 0.02],
        ]);
    }
}

// ── MODAL ─────────────────────────────────────────────────
function openModal(rstr) {
    const r = JSON.parse(rstr);
    const c = SECT_COLORS[r.secteur] || "#267a47";
    const si = SECT_ICONS[r.secteur] || "📍";
    document.getElementById("m-badge").style.cssText =
        `background:${c}18;color:${c}`;
    document.getElementById("m-badge").textContent =
        si + " " + r.secteur + (r.type ? " · " + r.type : "");
    document.getElementById("m-nom").textContent =
        r.nom || r.village || "Infrastructure";
    document.getElementById("m-loc").textContent =
        `📍 ${r.village} · ${r.commune} · Dép. ${r.dept} · Kaolack`;
    let html = "";
    if (r.secteur === "Education") {
        const tot =
            r.effectif_global ||
            (r.effectif_filles && r.effectif_garcons
                ? parseInt(r.effectif_filles || 0) +
                  parseInt(r.effectif_garcons || 0)
                : null);
        html = `<div class="m-section-title">📊 Indicateurs scolaires</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Effectif total</div><div class="m-val">${tot || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Élèves filles</div><div class="m-val">${r.effectif_filles || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Élèves garçons</div><div class="m-val">${r.effectif_garcons || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Enseignants</div><div class="m-val">${r.nb_enseignants || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Salles de classe</div><div class="m-val">${r.nb_salles || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Statut</div><div class="m-val" style="font-size:13px">${r.statut || "–"}</div></div>
    </div>
    <div class="m-section-title">🏗 Équipements</div>
    <div class="m-tags">
      <span class="m-tag ${r.point_eau === "Oui" ? "yes" : "no"}">💧 Eau: ${r.point_eau || "–"}</span>
      <span class="m-tag ${r.electricite === "Oui" ? "yes" : "no"}">⚡ Élec.: ${r.electricite || "–"}</span>
      <span class="m-tag ${r.cantine === "Oui" ? "yes" : "no"}">🍽 Cantine: ${r.cantine || "–"}</span>
      <span class="m-tag ${r.bibliotheque === "Oui" ? "yes" : "no"}">📚 Biblio.: ${r.bibliotheque || "–"}</span>
    </div>`;
    } else if (r.secteur === "Hydraulique") {
        html = `<div class="m-section-title">💧 Infrastructure hydraulique</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Type</div><div class="m-val" style="font-size:13px">${r.infra_hydro || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Fonctionnalité</div><div class="m-val ${r.fonctionnalite === "Oui" ? "good" : "bad"}">${r.fonctionnalite || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Qualité eau</div><div class="m-val ${r.qualite_eau === "Bonne " || r.qualite_eau === "Bonne" ? "good" : "bad"}">${r.qualite_eau || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Débit (m³/h)</div><div class="m-val">${r.debit_forage || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Capacité château (m³)</div><div class="m-val">${r.capacite_chateau || "–"}</div></div>
    </div>`;
    } else if (r.secteur === "Agriculture") {
        html = `<div class="m-section-title">🌾 Infrastructure agricole</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Type</div><div class="m-val" style="font-size:13px">${r.infra_agri || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">État</div><div class="m-val">${r.etat_infra || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Superficie (ha)</div><div class="m-val">${r.superficie || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Spéculations</div><div class="m-val" style="font-size:13px">${r.speculations || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Exploitants H</div><div class="m-val">${r.nb_exploit_h || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Exploitantes F</div><div class="m-val">${r.nb_exploit_f || "–"}</div></div>
    </div>`;
    } else if (r.secteur === "Santé") {
        html = `<div class="m-section-title">🏥 Structure sanitaire</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Type</div><div class="m-val" style="font-size:13px">${r.type_structure || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Infirmiers</div><div class="m-val">${r.nb_infirmiers || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Sages-femmes</div><div class="m-val">${r.nb_sagesfemmes || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Salles hospit.</div><div class="m-val">${r.nb_salles_hospit || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Lits</div><div class="m-val">${r.nb_lits || "–"}</div></div>
    </div>`;
    } else if (r.secteur === "Elevage") {
        html = `<div class="m-section-title">🐄 Élevage</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Infrastructure</div><div class="m-val" style="font-size:13px">${r.infra_elevage || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">État</div><div class="m-val">${r.etat_elevage || "–"}</div></div>
    </div>`;
    } else if (r.secteur === "Energie") {
        html = `<div class="m-section-title">⚡ Énergie</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Électricité</div><div class="m-val ${r.has_elec === "Oui" ? "good" : "bad"}">${r.has_elec || "–"}</div></div>
      <div class="m-item"><div class="m-lbl">Éclairage</div><div class="m-val" style="font-size:13px">${r.moyen_eclairage || "–"}</div></div>
    </div>`;
    } else if (r.secteur === "Commerce") {
        html = `<div class="m-section-title">🛒 Commerce</div>
    <div class="m-grid">
      <div class="m-item"><div class="m-lbl">Boutiques</div><div class="m-val">${r.nb_boutiques || "0"}</div></div>
      <div class="m-item"><div class="m-lbl">Marchés</div><div class="m-val">${r.nb_marches || "0"}</div></div>
    </div>`;
    }
    html += `<div class="m-section-title">📡 Coordonnées GPS</div>
  <div class="m-grid">
    <div class="m-item"><div class="m-lbl">Latitude</div><div class="m-val" style="font-size:13px">${r.lat.toFixed(6)}</div></div>
    <div class="m-item"><div class="m-lbl">Longitude</div><div class="m-val" style="font-size:13px">${r.lon.toFixed(6)}</div></div>
  </div>`;
    if (r.commentaire)
        html += `<div class="m-section-title">💬 Commentaire</div><div style="background:var(--surface2);border-radius:9px;padding:12px;font-size:12px;color:var(--text-dim);line-height:1.6">${r.commentaire}</div>`;
    document.getElementById("m-body").innerHTML = html;
    document.getElementById("modal").classList.add("open");
}
function closeModal() {
    document.getElementById("modal").classList.remove("open");
}
document.getElementById("modal").addEventListener("click", (e) => {
    if (e.target === e.currentTarget) closeModal();
});

// ── STATISTICS ────────────────────────────────────────────
function initStats() {
    const edu = DATA.filter((r) => r.secteur === "Education");
    const tot = DATA.length,
        vill = new Set(DATA.map((r) => r.village)).size;
    const televes = edu.reduce(
        (s, r) => s + (parseInt(r.effectif_global) || 0),
        0,
    );
    document.getElementById("stat-kpis").innerHTML = `
    <div class="sk" style="--sk-color:var(--primary)"><div class="sk-num">${tot}</div><div class="sk-lbl">Infrastructures totales</div></div>
    <div class="sk" style="--sk-color:var(--blue)"><div class="sk-num">${vill}</div><div class="sk-lbl">Villages couverts</div></div>
    <div class="sk" style="--sk-color:var(--accent)"><div class="sk-num">${edu.length}</div><div class="sk-lbl">Établissements scolaires</div></div>
    <div class="sk" style="--sk-color:var(--red)"><div class="sk-num">${televes.toLocaleString()}</div><div class="sk-lbl">Élèves recensés</div></div>`;

    const cfg = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: "#4a6555", font: { size: 11 } } },
            tooltip: {
                backgroundColor: "#fff",
                borderColor: "#d0ddd4",
                borderWidth: 1,
                titleColor: "#1a2d22",
                bodyColor: "#4a6555",
            },
        },
        scales: {
            x: {
                ticks: { color: "#8aaa95", font: { size: 10 } },
                grid: { color: "#f0f4f2" },
            },
            y: {
                ticks: { color: "#8aaa95", font: { size: 10 } },
                grid: { color: "#f0f4f2" },
            },
        },
    };

    // Secteur
    const sc = {};
    DATA.forEach((r) => {
        sc[r.secteur] = (sc[r.secteur] || 0) + 1;
    });
    const sl = Object.keys(sc).sort((a, b) => sc[b] - sc[a]);
    new Chart(document.getElementById("ch-sect"), {
        type: "bar",
        data: {
            labels: sl.map((s) => (SECT_ICONS[s] || "📍") + " " + s),
            datasets: [
                {
                    data: sl.map((s) => sc[s]),
                    backgroundColor: sl.map(
                        (s) => (SECT_COLORS[s] || "#267a47") + "44",
                    ),
                    borderColor: sl.map((s) => SECT_COLORS[s] || "#267a47"),
                    borderWidth: 1.5,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            ...cfg,
            plugins: { ...cfg.plugins, legend: { display: false } },
        },
    });

    // Education types
    const etc = {};
    edu.forEach((r) => {
        const t = r.type || "Non précisé";
        etc[t] = (etc[t] || 0) + 1;
    });
    const etcols = ["#2e7fbb", "#c07b28", "#c44030", "#267a47", "#7b4fba"];
    new Chart(document.getElementById("ch-edu"), {
        type: "doughnut",
        data: {
            labels: Object.keys(etc),
            datasets: [
                {
                    data: Object.values(etc),
                    backgroundColor: etcols,
                    borderColor: "#fff",
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "right",
                    labels: { color: "#4a6555", font: { size: 11 } },
                },
                tooltip: {
                    backgroundColor: "#fff",
                    borderColor: "#d0ddd4",
                    borderWidth: 1,
                    titleColor: "#1a2d22",
                    bodyColor: "#4a6555",
                },
            },
        },
    });

    // Eau
    const hyd = DATA.filter((r) => r.secteur === "Hydraulique"),
        qc = {};
    hyd.forEach((r) => {
        const q = r.qualite_eau?.trim() || "Non renseigné";
        qc[q] = (qc[q] || 0) + 1;
    });
    const qcols = {
        Bonne: "#267a47",
        Mauvaise: "#c44030",
        "Non renseigné": "#8aaa95",
    };
    new Chart(document.getElementById("ch-eau"), {
        type: "pie",
        data: {
            labels: Object.keys(qc),
            datasets: [
                {
                    data: Object.values(qc),
                    backgroundColor: Object.keys(qc).map(
                        (k) => qcols[k] || "#8aaa95",
                    ),
                    borderColor: "#fff",
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "right",
                    labels: { color: "#4a6555", font: { size: 11 } },
                },
                tooltip: {
                    backgroundColor: "#fff",
                    borderColor: "#d0ddd4",
                    borderWidth: 1,
                    titleColor: "#1a2d22",
                    bodyColor: "#4a6555",
                },
            },
        },
    });

    // Top villages
    const vc = {};
    DATA.forEach((r) => {
        vc[r.village] = (vc[r.village] || 0) + 1;
    });
    const tv = Object.entries(vc)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 10);
    new Chart(document.getElementById("ch-vill"), {
        type: "bar",
        data: {
            labels: tv.map((v) => v[0]),
            datasets: [
                {
                    data: tv.map((v) => v[1]),
                    backgroundColor: "rgba(38,122,71,0.55)",
                    borderColor: "#267a47",
                    borderWidth: 1.5,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            ...cfg,
            indexAxis: "y",
            plugins: { ...cfg.plugins, legend: { display: false } },
        },
    });
}

// ── CLIMAT ────────────────────────────────────────────────
const luMaps = {},
    luCharts = {};
let floodMap = null,
    climatChartsInited = false;

let currentClimCommune = "Dya";

function onClimCommune() {
    currentClimCommune = document.getElementById("clim-commune-sel").value;
    // Reset all maps and charts so they reinit with new commune
    Object.keys(luMaps).forEach((k) => {
        luMaps[k].remove();
        delete luMaps[k];
    });
    Object.keys(luCharts).forEach((k) => {
        luCharts[k].destroy();
        delete luCharts[k];
    });
    if (floodMap) {
        floodMap.remove();
        floodMap = null;
    }
    if (climatChartsInited) {
        [
            "ch-temp",
            "ch-temp-m",
            "ch-precip",
            "ch-precip-m",
            "ch-defor",
            "ch-inond",
        ].forEach((id) => {
            const c = Chart.getChart(id);
            if (c) c.destroy();
        });
        climatChartsInited = false;
    }
    setupOccPanels();
    selYear(currentOccYear || "2000");
    // Re-init current non-occ section if visible
    const cur = document.querySelector(".clim-pg.on");
    if (cur && cur.id !== "cp-occ") {
        if (cur.id === "cp-inond") {
            setTimeout(initFloodMap, 80);
        } else {
            setTimeout(initClimatCharts, 80);
            climatChartsInited = true;
        }
    }
    updateClimHeader();
}

function updateClimHeader() {
    const lbl = currentClimCommune;
    document
        .querySelectorAll(".comm-lbl")
        .forEach((el) => (el.textContent = lbl));
}

function initClimat() {
    currentClimCommune = "Dya";
    setupOccPanels();
    selYear("2000");
}
function setupOccPanels() {
    const comm = currentClimCommune;
    const pcts = COMMUNE_CLIMATE.pct[comm];
    ["2000", "2010", "2020", "2030", "2040", "2050"].forEach((yr) => {
        const d = OCC_DATA[yr];
        const pct = pcts[yr];
        const el_t = document.getElementById("od-title-" + yr);
        const el_d = document.getElementById("od-desc-" + yr);
        const el_s = document.getElementById("od-src-" + yr);
        const el_l = document.getElementById("od-leg-" + yr);
        // Init home KPIs — safe guards (no error when elements absent)
        const elKTotal = document.getElementById("k-total");
        if (elKTotal) elKTotal.textContent = DATA.length;
        const elKVill = document.getElementById("k-villages");
        if (elKVill)
            elKVill.textContent = new Set(DATA.map((r) => r.village)).size;
        const elKSect = document.getElementById("k-secteurs");
        if (elKSect)
            elKSect.textContent = new Set(DATA.map((r) => r.secteur)).size;
        if (el_t) {
            el_t.textContent = d.title;
        }
        if (el_d) {
            el_d.textContent = d.description;
        }
        if (el_s) {
            el_s.textContent = d.source;
        }
        if (el_l) {
            el_l.innerHTML = "";
            Object.entries(d.legend).forEach(([k, v]) => {});
        }
    });
}

let currentOccYear = "2000";
function selYear(yr) {
    currentOccYear = yr;
    document
        .querySelectorAll(".yr-tab")
        .forEach((t) => t.classList.remove("on"));
    document
        .querySelectorAll(".cm-sub-item")
        .forEach((t) => t.classList.remove("on"));
    const yt = document.getElementById("yt-" + yr);
    if (yt) yt.classList.add("on");
    const ys = document.getElementById("ys-" + yr);
    if (ys) ys.classList.add("on");
    document
        .querySelectorAll(".occ-panel")
        .forEach((p) => p.classList.remove("on"));
    document.getElementById("occ-" + yr).classList.add("on");
    if (!luMaps[yr]) {
        setTimeout(() => initLuMap(yr), 80);
    }
    if (!luCharts[yr]) {
        setTimeout(() => initOccChart(yr), 100);
    }
}

function initLuMap(yr) {
    const el = document.getElementById("lm-" + yr);
    if (!el) return;
    const comm = currentClimCommune;
    const cfg = COMMUNE_CLIMATE.climate[comm];
    const B = cfg.bounds;
    const ctr = cfg.city_center;
    const pad = 0.04;
    const zoom = comm === "Dya" ? 11 : 11;

    const map = L.map(el, {
        center: [ctr[0], ctr[1]],
        zoom: zoom,
        zoomControl: true,
        attributionControl: false,
    });
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        opacity: 0.4,
        maxZoom: 16,
    }).addTo(map);

    const ROWS = 12,
        COLS = 16;
    const dlat = (B.n - B.s) / ROWS,
        dlon = (B.e - B.w) / COLS;
    const grid = COMMUNE_CLIMATE.grids[comm][yr];

    grid.forEach((t, idx) => {
        const r = Math.floor(idx / COLS),
            c = idx % COLS;
        const s = B.n - (r + 1) * dlat,
            n = s + dlat,
            w = B.w + c * dlon,
            e = w + dlon;
        L.rectangle(
            [
                [s, w],
                [n, e],
            ],
            {
                color: "none",
                fillColor: LU_COLORS[t],
                fillOpacity: 0.6,
                weight: 0,
            },
        ).addTo(map);
    });

    // Commune centre marker
    L.circleMarker([ctr[0], ctr[1]], {
        radius: 7,
        color: "#333",
        fillColor: "#555",
        fillOpacity: 1,
        weight: 2,
    })
        .bindTooltip(comm, {
            permanent: true,
            direction: "right",
            offset: [8, 0],
        })
        .addTo(map);

    // Bounding box
    L.rectangle(
        [
            [B.s, B.w],
            [B.n, B.e],
        ],
        {
            color: "#267a47",
            weight: 1.5,
            fill: false,
            dashArray: "5,4",
            opacity: 0.6,
        },
    ).addTo(map);

    // Infra points from DATA
    const pts = DATA.filter((d) => d.commune === comm);
    const seen = new Set();
    pts.forEach((d) => {
        const key = d.lat.toFixed(4) + "," + d.lon.toFixed(4);
        if (seen.has(key)) return;
        seen.add(key);
        const color = SECT_COLORS[d.secteur] || "#267a47";
        L.circleMarker([d.lat, d.lon], {
            radius: 3,
            color: color,
            fillColor: color,
            fillOpacity: 0.7,
            weight: 0,
        })
            .bindTooltip(d.village || d.commune, { direction: "top" })
            .addTo(map);
    });

    map.fitBounds([
        [B.s - pad, B.w - pad],
        [B.n + pad, B.e + pad],
    ]);
    luMaps[yr] = map;
    setTimeout(() => map.invalidateSize(), 200);
}

function initOccChart(yr) {
    const ctx = document.getElementById("oc-" + yr);
    if (!ctx) return;
    const pct = COMMUNE_CLIMATE.pct[currentClimCommune][yr];
    luCharts[yr] = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: LU_NAMES,
            datasets: [
                {
                    data: pct,
                    backgroundColor: LU_COLORS,
                    borderColor: "#fff",
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: (c) => ` ${c.label}: ${c.raw}%` },
                    backgroundColor: "#fff",
                    borderColor: "#ddd",
                    borderWidth: 1,
                    titleColor: "#1a2d22",
                    bodyColor: "#4a6555",
                },
            },
        },
    });
}

function showClimat(sec) {
    document
        .querySelectorAll(".clim-pg")
        .forEach((p) => p.classList.remove("on"));
    document
        .querySelectorAll(".cm-item")
        .forEach((b) => b.classList.remove("on"));
    document.getElementById("cp-" + sec).classList.add("on");
    document.getElementById("cm-" + sec).classList.add("on");
    document.getElementById("yr-sub").style.display =
        sec === "occ" ? "block" : "none";
    if (sec === "inond" && !floodMap) setTimeout(initFloodMap, 80);
    if (
        !climatChartsInited &&
        (sec === "temp" || sec === "precip" || sec === "defor")
    ) {
        setTimeout(initClimatCharts, 80);
        climatChartsInited = true;
    }
}

function initFloodMap() {
    const el = document.getElementById("flood-map");
    const comm = currentClimCommune;
    const cfg = COMMUNE_CLIMATE.climate[comm];
    const ctr = cfg.city_center;
    floodMap = L.map(el, {
        center: [ctr[0], ctr[1]],
        zoom: 11,
        attributionControl: false,
    });
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        opacity: 0.6,
        maxZoom: 15,
    }).addTo(floodMap);

    // Flood zones dynamically positioned per commune
    const B = cfg.bounds;
    const la = ctr[0],
        lo = ctr[1];
    const dh = 0.025,
        dw = 0.035;
    const zonesVHigh = [
        [
            [la - dh, lo - dw],
            [la + dh, lo - dw],
            [la + dh, lo - dw + 0.02],
            [la - dh, lo - dw + 0.02],
        ],
        [
            [la - dh * 1.5, lo - dh],
            [la + dh * 0.5, lo - dh],
            [la + dh * 0.5, lo - dh + 0.025],
            [la - dh * 1.5, lo - dh + 0.025],
        ],
    ];
    const zonesHigh = [
        [
            [la - dh * 2, lo - dw - 0.02],
            [la + dh * 1.5, lo - dw - 0.02],
            [la + dh * 1.5, lo - dw + 0.04],
            [la - dh * 2, lo - dw + 0.04],
        ],
        [
            [la - dh, lo - 0.015],
            [la + dh * 2, lo - 0.015],
            [la + dh * 2, lo + 0.04],
            [la - dh, lo + 0.04],
        ],
    ];
    const zonesMod = [
        [
            [B.s + 0.01, B.w + 0.01],
            [B.s + 0.07, B.w + 0.01],
            [B.s + 0.07, B.e - 0.01],
            [B.s + 0.01, B.e - 0.01],
        ],
        [
            [la - dh * 3, lo - dw * 1.5],
            [la + dh * 2, lo - dw * 1.5],
            [la + dh * 2, lo + dw],
            [la - dh * 3, lo + dw],
        ],
    ];

    zonesMod.forEach((z) =>
        L.polygon(z, {
            color: "#e8a010",
            fillColor: "#e8b020",
            fillOpacity: 0.3,
            weight: 1,
            dashArray: "4,3",
        }).addTo(floodMap),
    );
    zonesHigh.forEach((z) =>
        L.polygon(z, {
            color: "#c05010",
            fillColor: "#e07030",
            fillOpacity: 0.45,
            weight: 1.5,
        }).addTo(floodMap),
    );
    zonesVHigh.forEach((z) =>
        L.polygon(z, {
            color: "#a01010",
            fillColor: "#d44040",
            fillOpacity: 0.6,
            weight: 2,
        }).addTo(floodMap),
    );

    // Saloum river
    const riv = [
        [14.2, -16.65],
        [14.15, -16.5],
        [14.1, -16.35],
        [14.08, -16.2],
        [14.1, -16.05],
        [14.15, -15.95],
    ];
    L.polyline(riv, { color: "#4a9eca", weight: 4, opacity: 0.85 }).addTo(
        floodMap,
    );

    // Infrastructure points in this commune
    const commPts = DATA.filter((r) => r.commune === comm);
    const seenF = new Set();
    commPts.slice(0, 20).forEach((r) => {
        const k = r.lat.toFixed(3) + "," + r.lon.toFixed(3);
        if (seenF.has(k)) return;
        seenF.add(k);
        L.circleMarker([r.lat, r.lon], {
            radius: 4,
            color: "#c44030",
            fillColor: "#e06050",
            fillOpacity: 0.8,
            weight: 1.5,
        })
            .bindTooltip(r.village, { permanent: false })
            .addTo(floodMap);
    });
    L.circleMarker([ctr[0], ctr[1]], {
        radius: 7,
        color: "#333",
        fillColor: "#555",
        fillOpacity: 1,
        weight: 2,
    })
        .bindTooltip(comm, {
            permanent: true,
            direction: "right",
            offset: [8, 0],
        })
        .addTo(floodMap);
    setTimeout(() => floodMap.invalidateSize(), 200);
}

function initClimatCharts() {
    const clim = COMMUNE_CLIMATE.climate[currentClimCommune];
    const yrs = clim.years;
    const th = clim.temp_hist;
    const tp = clim.temp_proj;
    const ph = clim.precip_hist;
    const pp = clim.precip_proj;
    const lopt = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: "#4a6555", font: { size: 11 } } },
            tooltip: {
                backgroundColor: "#fff",
                borderColor: "#d0ddd4",
                borderWidth: 1,
                titleColor: "#1a2d22",
                bodyColor: "#4a6555",
            },
        },
        scales: {
            x: {
                ticks: {
                    color: "#8aaa95",
                    font: { size: 9 },
                    maxTicksLimit: 15,
                },
                grid: { color: "#f0f4f2" },
            },
            y: {
                ticks: { color: "#8aaa95", font: { size: 10 } },
                grid: { color: "#f0f4f2" },
            },
        },
    };

    new Chart(document.getElementById("ch-temp"), {
        type: "line",
        data: {
            labels: yrs,
            datasets: [
                {
                    label: "Historique (°C)",
                    data: th,
                    borderColor: "#2e7fbb",
                    backgroundColor: "rgba(46,127,187,.08)",
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: "Proj. RCP 8.5 (°C)",
                    data: tp,
                    borderColor: "#c44030",
                    backgroundColor: "rgba(196,64,48,.08)",
                    borderWidth: 2,
                    borderDash: [5, 4],
                    pointRadius: 0,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: { ...lopt },
    });

    const mo = [
        "Jan",
        "Fév",
        "Mar",
        "Avr",
        "Mai",
        "Jun",
        "Jul",
        "Aoû",
        "Sep",
        "Oct",
        "Nov",
        "Déc",
    ];
    // Temp max/min (slight offset per commune)
    const tMaxBase = [32, 35, 38, 40, 40, 39, 35, 32, 33, 35, 34, 31];
    const tMinBase = [18, 20, 22, 24, 26, 27, 25, 24, 23, 22, 19, 17];
    const tOff = currentClimCommune === "Ndramé Escale" ? -0.5 : 0;
    const tMax = tMaxBase.map((v) => +(v + tOff).toFixed(1));
    const tMin = tMinBase.map((v) => +(v + tOff).toFixed(1));

    new Chart(document.getElementById("ch-temp-m"), {
        type: "bar",
        data: {
            labels: mo,
            datasets: [
                {
                    label: "Temp. max (°C)",
                    data: tMax,
                    backgroundColor: "rgba(196,64,48,.7)",
                    borderColor: "#c44030",
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: "Temp. min (°C)",
                    data: tMin,
                    backgroundColor: "rgba(46,127,187,.7)",
                    borderColor: "#2e7fbb",
                    borderWidth: 1,
                    borderRadius: 4,
                },
            ],
        },
        options: { ...lopt },
    });

    new Chart(document.getElementById("ch-precip"), {
        type: "line",
        data: {
            labels: yrs,
            datasets: [
                {
                    label: "Historique (mm)",
                    data: ph,
                    borderColor: "#2e7fbb",
                    backgroundColor: "rgba(46,127,187,.08)",
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: "Proj. RCP 8.5 (mm)",
                    data: pp,
                    borderColor: "#c07b28",
                    backgroundColor: "rgba(192,123,40,.08)",
                    borderWidth: 2,
                    borderDash: [5, 4],
                    pointRadius: 0,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: { ...lopt },
    });

    const p2000 = clim.precip_2000,
        p2023 = clim.precip_2023,
        p2050 = clim.precip_2050;
    new Chart(document.getElementById("ch-precip-m"), {
        type: "bar",
        data: {
            labels: mo,
            datasets: [
                {
                    label: "2000",
                    data: p2000,
                    backgroundColor: "rgba(46,127,187,.75)",
                    borderRadius: 3,
                },
                {
                    label: "2023",
                    data: p2023,
                    backgroundColor: "rgba(192,123,40,.75)",
                    borderRadius: 3,
                },
                {
                    label: "Proj. 2050",
                    data: p2050,
                    backgroundColor: "rgba(196,64,48,.75)",
                    borderRadius: 3,
                },
            ],
        },
        options: { ...lopt },
    });

    // Update commune KPIs
    const kT1 = document.getElementById("kpi-t1"),
        kT2 = document.getElementById("kpi-t2");
    const kP1 = document.getElementById("kpi-p1"),
        kP2 = document.getElementById("kpi-p2");
    if (kT1) kT1.textContent = clim.temp_base_1980_2000 + "°C";
    if (kT2) kT2.textContent = clim.temp_base_2001_2023 + "°C";
    if (kP1) kP1.textContent = clim.precip_avg_1980 + "mm";
    if (kP2) kP2.textContent = clim.precip_avg_2023 + "mm";

    const dy = [1990, 2000, 2010, 2020, 2030, 2040, 2050],
        dc = [52, 42, 34, 27, 21, 16, 12];
    new Chart(document.getElementById("ch-defor"), {
        type: "line",
        data: {
            labels: dy,
            datasets: [
                {
                    label: "Couvert forestier (%)",
                    data: dc,
                    borderColor: "#267a47",
                    backgroundColor: "rgba(38,122,71,.12)",
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: "#267a47",
                },
                {
                    label: "Objectif reboisement",
                    data: [null, null, null, 27, 27, 27, 30],
                    borderColor: "#c07b28",
                    borderDash: [4, 4],
                    pointRadius: 0,
                    borderWidth: 1.5,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: "#4a6555", font: { size: 11 } } },
                tooltip: {
                    backgroundColor: "#fff",
                    borderColor: "#d0ddd4",
                    borderWidth: 1,
                    titleColor: "#1a2d22",
                    bodyColor: "#4a6555",
                },
            },
            scales: {
                x: { ticks: { color: "#8aaa95" }, grid: { color: "#f0f4f2" } },
                y: { ticks: { color: "#8aaa95" }, grid: { color: "#f0f4f2" } },
            },
        },
    });

    new Chart(document.getElementById("ch-inond"), {
        type: "bar",
        data: {
            labels: [
                "1980–89",
                "1990–99",
                "2000–09",
                "2010–19",
                "2020–29",
                "2030–39*",
                "2040–49*",
            ],
            datasets: [
                {
                    label: "Événements d'inondation",
                    data: [4, 6, 9, 14, 18, 22, 28],
                    backgroundColor: [
                        "#2e7fbb",
                        "#2e7fbb",
                        "#c07b28",
                        "#c07b28",
                        "#c44030",
                        "rgba(196,64,48,.6)",
                        "rgba(196,64,48,.4)",
                    ],
                    borderColor: [
                        "#2e7fbb",
                        "#2e7fbb",
                        "#c07b28",
                        "#c07b28",
                        "#c44030",
                        "#c44030",
                        "#c44030",
                    ],
                    borderWidth: 1.5,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#fff",
                    borderColor: "#d0ddd4",
                    borderWidth: 1,
                    titleColor: "#1a2d22",
                    bodyColor: "#4a6555",
                },
            },
            scales: {
                x: { ticks: { color: "#8aaa95" }, grid: { color: "#f0f4f2" } },
                y: { ticks: { color: "#8aaa95" }, grid: { color: "#f0f4f2" } },
            },
        },
    });
}

// ── INIT HOME KPIS ────────────────────────────────────────
document.getElementById("k-total").textContent = DATA.length;
document.getElementById("k-villages").textContent = new Set(
    DATA.map((r) => r.village),
).size;
document.getElementById("k-secteurs").textContent = new Set(
    DATA.map((r) => r.secteur),
).size;

// Pre-trigger region on carto load
setTimeout(() => {
    onRegion();
    document.getElementById("sel-dept").value = "Kaolack";
    onDept();
}, 200);

// ── DROPDOWN MENU HANDLER ──────────────────────────────────────────
document.querySelectorAll(".nav-dropdown-toggle").forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
        const dropdown = toggle.closest(".nav-dropdown");
        const isOpen = dropdown.classList.contains("open");

        // Fermer tous les autres dropdowns
        document.querySelectorAll(".nav-dropdown.open").forEach((d) => {
            if (d !== dropdown) d.classList.remove("open");
        });

        // Basculer le dropdown actuel
        if (isOpen) {
            dropdown.classList.remove("open");
        } else {
            dropdown.classList.add("open");
        }
    });
});

// Fermer les dropdowns quand on clique sur un élément
document.querySelectorAll(".nav-dropdown-item").forEach((item) => {
    item.addEventListener("click", (e) => {
        const dropdown = item.closest(".nav-dropdown");
        dropdown.classList.remove("open");
        const href = item.getAttribute("data-href");
        if (href) {
            window.location.href = href;
        }
    });
});

// Fermer les dropdowns quand on clique ailleurs
document.addEventListener("click", (e) => {
    if (!e.target.closest("nav")) {
        document.querySelectorAll(".nav-dropdown.open").forEach((d) => {
            d.classList.remove("open");
        });
    }
});
