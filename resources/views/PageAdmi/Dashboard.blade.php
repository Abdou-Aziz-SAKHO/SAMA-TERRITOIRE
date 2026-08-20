@extends('AppAdmi')
@section('content')
    <!-- ════════════════════════════════════ STATISTIQUES ════════════════════════════════════ -->
    <div id="page-stats" class="page active">
        <div class="page-title">Tableau de bord statistique</div>
        <div class="page-sub">Commune de Dya — Département de Kaolack · Données terrain 2022</div>
        <div class="stat-kpis" id="stat-kpis"></div>
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">Répartition par secteur</div>
                <div class="chart-sub">Nombre d'infrastructures recensées</div>
                <div class="chart-box"><canvas id="ch-sect"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Éducation — Types</div>
                <div class="chart-sub">22 établissements scolaires</div>
                <div class="chart-box"><canvas id="ch-edu"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Hydraulique — Qualité de l'eau</div>
                <div class="chart-sub">État des points d'eau recensés</div>
                <div class="chart-box"><canvas id="ch-eau"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Top 10 villages équipés</div>
                <div class="chart-sub">Nombre d'infrastructures par village</div>
                <div class="chart-box"><canvas id="ch-vill"></canvas></div>
            </div>
        </div>
    </div>

    <script src="assets/data.js"></script>
    <script src="assets/script.js"></script>
    <script>
        function initStats() {
            const edu = DATA.filter(r => r.secteur === 'Education');
            const tot = DATA.length,
                vill = new Set(DATA.map(r => r.village)).size;
            const televes = edu.reduce((s, r) => s + (parseInt(r.effectif_global) || 0), 0);
            document.getElementById('stat-kpis').innerHTML =
                `
    <div class="sk" style="--sk-color:var(--primary)"><div class="sk-num">${tot}</div><div class="sk-lbl">Infrastructures totales</div></div>
    <div class="sk" style="--sk-color:var(--blue)"><div class="sk-num">${vill}</div><div class="sk-lbl">Villages couverts</div></div>
    <div class="sk" style="--sk-color:var(--accent)"><div class="sk-num">${edu.length}</div><div class="sk-lbl">Établissements scolaires</div></div>
    <div class="sk" style="--sk-color:var(--red)"><div class="sk-num">${televes.toLocaleString()}</div><div class="sk-lbl">Élèves recensés</div></div>`;

            const cfg = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#4a6555',
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#fff',
                        borderColor: '#d0ddd4',
                        borderWidth: 1,
                        titleColor: '#1a2d22',
                        bodyColor: '#4a6555'
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#8aaa95',
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            color: '#f0f4f2'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#8aaa95',
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            color: '#f0f4f2'
                        }
                    }
                }
            };

            // Secteur
            const sc = {};
            DATA.forEach(r => {
                sc[r.secteur] = (sc[r.secteur] || 0) + 1;
            });
            const sl = Object.keys(sc).sort((a, b) => sc[b] - sc[a]);
            new Chart(document.getElementById('ch-sect'), {
                type: 'bar',
                data: {
                    labels: sl.map(s => (SECT_ICONS[s] || '📍') + ' ' + s),
                    datasets: [{
                        data: sl.map(s => sc[s]),
                        backgroundColor: sl.map(s => (SECT_COLORS[s] || '#267a47') + '44'),
                        borderColor: sl.map(s => SECT_COLORS[s] || '#267a47'),
                        borderWidth: 1.5,
                        borderRadius: 4
                    }]
                },
                options: {
                    ...cfg,
                    plugins: {
                        ...cfg.plugins,
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Education types
            const etc = {};
            edu.forEach(r => {
                const t = r.type || 'Non précisé';
                etc[t] = (etc[t] || 0) + 1;
            });
            const etcols = ['#2e7fbb', '#c07b28', '#c44030', '#267a47', '#7b4fba'];
            new Chart(document.getElementById('ch-edu'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(etc),
                    datasets: [{
                        data: Object.values(etc),
                        backgroundColor: etcols,
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#4a6555',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            borderColor: '#d0ddd4',
                            borderWidth: 1,
                            titleColor: '#1a2d22',
                            bodyColor: '#4a6555'
                        }
                    }
                }
            });

            // Eau
            const hyd = DATA.filter(r => r.secteur === 'Hydraulique'),
                qc = {};
            hyd.forEach(r => {
                const q = r.qualite_eau?.trim() || 'Non renseigné';
                qc[q] = (qc[q] || 0) + 1;
            });
            const qcols = {
                'Bonne': '#267a47',
                'Mauvaise': '#c44030',
                'Non renseigné': '#8aaa95'
            };
            new Chart(document.getElementById('ch-eau'), {
                type: 'pie',
                data: {
                    labels: Object.keys(qc),
                    datasets: [{
                        data: Object.values(qc),
                        backgroundColor: Object.keys(qc).map(k => qcols[k] || '#8aaa95'),
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#4a6555',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            borderColor: '#d0ddd4',
                            borderWidth: 1,
                            titleColor: '#1a2d22',
                            bodyColor: '#4a6555'
                        }
                    }
                }
            });

            // Top villages
            const vc = {};
            DATA.forEach(r => {
                vc[r.village] = (vc[r.village] || 0) + 1;
            });
            const tv = Object.entries(vc).sort((a, b) => b[1] - a[1]).slice(0, 10);
            new Chart(document.getElementById('ch-vill'), {
                type: 'bar',
                data: {
                    labels: tv.map(v => v[0]),
                    datasets: [{
                        data: tv.map(v => v[1]),
                        backgroundColor: 'rgba(38,122,71,0.55)',
                        borderColor: '#267a47',
                        borderWidth: 1.5,
                        borderRadius: 4
                    }]
                },
                options: {
                    ...cfg,
                    indexAxis: 'y',
                    plugins: {
                        ...cfg.plugins,
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initStats);
    </script>
@endsection
