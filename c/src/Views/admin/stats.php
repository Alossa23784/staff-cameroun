<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Tableau de bord Admin</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --bg:       #0a0e1a;
  --surface:  #111827;
  --border:   #1e2d45;
  --accent:   #00d4aa;
  --accent2:  #ff6b35;
  --accent3:  #7c3aed;
  --text:     #e2e8f0;
  --muted:    #64748b;
  --success:  #10b981;
  --warning:  #f59e0b;
  --danger:   #ef4444;
}

* { margin:0; padding:0; box-sizing:border-box; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  display: flex;
}

/* ── SIDEBAR ── */
.sidebar {
  width: 260px;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  z-index: 100;
}

.sidebar-logo {
  padding: 28px 24px 20px;
  border-bottom: 1px solid var(--border);
}
.sidebar-logo h1 {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 1.6rem;
  letter-spacing: -0.04em;
  color: var(--accent);
}
.sidebar-logo span {
  font-size: 0.72rem;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.nav-section {
  padding: 20px 14px 8px;
  font-size: 0.68rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted);
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 20px;
  margin: 2px 8px;
  border-radius: 8px;
  text-decoration: none;
  color: var(--muted);
  font-size: 0.88rem;
  font-weight: 400;
  transition: all 0.2s;
}
.nav-item:hover, .nav-item.active {
  background: rgba(0,212,170,.1);
  color: var(--accent);
}
.nav-item .icon { font-size: 1.1rem; width: 20px; text-align: center; }

.sidebar-footer {
  margin-top: auto;
  padding: 20px;
  border-top: 1px solid var(--border);
  font-size: 0.78rem;
  color: var(--muted);
}

/* ── MAIN ── */
.main {
  margin-left: 260px;
  flex: 1;
  padding: 32px 36px;
  max-width: 1400px;
}

/* ── HEADER ── */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
}
.page-title {
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 1.7rem;
  letter-spacing: -0.03em;
}
.page-subtitle { color: var(--muted); font-size: 0.88rem; margin-top: 4px; }

/* ── PERIODE SWITCH ── */
.period-switch {
  display: flex;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 8px;
  overflow: hidden;
}
.period-btn {
  padding: 8px 18px;
  border: none;
  background: transparent;
  color: var(--muted);
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.83rem;
  font-weight: 500;
  transition: all 0.2s;
}
.period-btn.active {
  background: var(--accent);
  color: #0a0e1a;
}

/* ── KPI CARDS ── */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}

.kpi-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 20px;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, border-color 0.2s;
}
.kpi-card:hover {
  transform: translateY(-2px);
  border-color: rgba(0,212,170,.3);
}
.kpi-card::before {
  content: '';
  position: absolute;
  top: 0; right: 0;
  width: 80px; height: 80px;
  border-radius: 0 12px 0 100%;
  opacity: 0.08;
}
.kpi-card:nth-child(1)::before { background: var(--accent); }
.kpi-card:nth-child(2)::before { background: var(--accent2); }
.kpi-card:nth-child(3)::before { background: var(--accent3); }
.kpi-card:nth-child(4)::before { background: var(--success); }
.kpi-card:nth-child(5)::before { background: var(--warning); }

.kpi-icon {
  font-size: 1.4rem;
  margin-bottom: 12px;
}
.kpi-value {
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 1.9rem;
  letter-spacing: -0.04em;
  line-height: 1;
  margin-bottom: 6px;
}
.kpi-label {
  font-size: 0.78rem;
  color: var(--muted);
  font-weight: 400;
}
.kpi-badge {
  display: inline-block;
  font-size: 0.7rem;
  padding: 2px 8px;
  border-radius: 20px;
  margin-top: 8px;
  font-weight: 500;
}
.badge-green  { background: rgba(16,185,129,.15); color: var(--success); }
.badge-orange { background: rgba(255,107,53,.15);  color: var(--accent2); }
.badge-purple { background: rgba(124,58,237,.15);  color: var(--accent3); }

/* ── CHARTS GRID ── */
.charts-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}
.charts-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.chart-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 22px;
}
.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
}
.chart-title {
  font-family: 'Syne', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
}
.chart-tag {
  font-size: 0.7rem;
  padding: 3px 10px;
  border-radius: 20px;
  background: rgba(0,212,170,.1);
  color: var(--accent);
}
canvas { max-height: 220px; }

/* ── STATS BARS (répartition) ── */
.stat-bar-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 14px;
}
.stat-bar-label { font-size: 0.8rem; color: var(--muted); width: 110px; }
.stat-bar-track {
  flex: 1;
  height: 6px;
  background: var(--border);
  border-radius: 4px;
  overflow: hidden;
}
.stat-bar-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.8s ease;
}
.stat-bar-count { font-size: 0.82rem; font-weight: 500; min-width: 30px; text-align: right; }

/* ── TRAITEMENT DOUGHNUT ── */
.traitement-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
}
.traitement-legend {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
}
.legend-dot {
  width: 10px; height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

/* ── Responsive ── */
@media (max-width: 1200px) {
  .kpi-grid { grid-template-columns: repeat(3, 1fr); }
  .charts-grid { grid-template-columns: 1fr; }
  .charts-row { grid-template-columns: 1fr 1fr; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>STAFF</h1>
    <span>Administration</span>
  </div>

  <div class="nav-section">Tableau de bord</div>
  <a href="<?= BASE_URL ?>/admin/stats" class="nav-item active">
    <span class="icon">📊</span> Statistiques
  </a>

  <div class="nav-section">Gestion</div>
  <a href="<?= BASE_URL ?>/admin/users" class="nav-item">
    <span class="icon">👥</span> Utilisateurs
  </a>
  <a href="<?= BASE_URL ?>/admin/abonnements" class="nav-item">
    <span class="icon">💳</span> Abonnements
  </a>
  <a href="<?= BASE_URL ?>/admin/candidatures" class="nav-item">
    <span class="icon">📁</span> Candidatures
  </a>
  <a href="<?= BASE_URL ?>/admin/paiements" class="nav-item">
    <span class="icon">💰</span> Paiements
  </a>

  <div class="nav-section">Contenu</div>
  <a href="<?= BASE_URL ?>/admin/vitrine" class="nav-item">
    <span class="icon">🌐</span> Site vitrine
  </a>
  <a href="<?= BASE_URL ?>/admin/publicites" class="nav-item">
    <span class="icon">📢</span> Publicités
  </a>

  <div class="sidebar-footer">
    Connecté : <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?><br>
    <a href="<?= BASE_URL ?>/logout" style="color:var(--danger);text-decoration:none;font-size:0.8rem;">
      ⏻ Déconnexion
    </a>
  </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main">

  <!-- Header -->
  <div class="page-header">
    <div>
      <div class="page-title">Tableau de bord</div>
      <div class="page-subtitle">
        Vue d'ensemble — <?= date('d F Y') ?>
      </div>
    </div>
    <div class="period-switch">
      <button class="period-btn <?= $periode === 'week'  ? 'active' : '' ?>"
              onclick="switchPeriode('week')">Semaine</button>
      <button class="period-btn <?= $periode === 'month' ? 'active' : '' ?>"
              onclick="switchPeriode('month')">Mois</button>
      <button class="period-btn <?= $periode === 'year'  ? 'active' : '' ?>"
              onclick="switchPeriode('year')">Année</button>
    </div>
  </div>

  <!-- KPI CARDS -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-icon">👤</div>
      <div class="kpi-value"><?= number_format($total_users) ?></div>
      <div class="kpi-label">Utilisateurs inscrits</div>
      <span class="kpi-badge badge-green">Total</span>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon">🏢</div>
      <div class="kpi-value"><?= number_format((int)$repartition['entreprises']) ?></div>
      <div class="kpi-label">Entreprises</div>
      <span class="kpi-badge badge-orange">Employeurs</span>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon">🎓</div>
      <div class="kpi-value"><?= number_format((int)$repartition['abonnes']) ?></div>
      <div class="kpi-label">Abonnés actifs</div>
      <span class="kpi-badge badge-purple">Abonnements</span>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon">📨</div>
      <div class="kpi-value"><?= number_format($total_candidatures) ?></div>
      <div class="kpi-label">Candidatures envoyées</div>
      <span class="kpi-badge badge-green">Demandes</span>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon">💵</div>
      <div class="kpi-value"><?= number_format((int)$total_revenus) ?></div>
      <div class="kpi-label">Revenus FCFA</div>
      <span class="kpi-badge badge-orange">Paiements</span>
    </div>
  </div>

  <!-- CHARTS ROW 1: inscriptions + répartition users -->
  <div class="charts-grid">
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Inscriptions</div>
        <span class="chart-tag" id="tag-inscriptions"><?= ucfirst($periode) ?></span>
      </div>
      <canvas id="chartInscriptions"></canvas>
    </div>
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Répartition utilisateurs</div>
      </div>
      <?php
        $total   = max(1, $total_users);
        $pctEntr = round((int)$repartition['entreprises'] / $total * 100);
        $pctAbo  = round((int)$repartition['abonnes']     / $total * 100);
        $pctNAbo = round((int)$repartition['non_abonnes'] / $total * 100);
      ?>
      <div style="margin-top:8px">
        <div class="stat-bar-row">
          <div class="stat-bar-label">Entreprises</div>
          <div class="stat-bar-track">
            <div class="stat-bar-fill" style="width:<?= $pctEntr ?>%;background:var(--accent2)"></div>
          </div>
          <div class="stat-bar-count"><?= $repartition['entreprises'] ?></div>
        </div>
        <div class="stat-bar-row">
          <div class="stat-bar-label">Abonnés</div>
          <div class="stat-bar-track">
            <div class="stat-bar-fill" style="width:<?= $pctAbo ?>%;background:var(--accent)"></div>
          </div>
          <div class="stat-bar-count"><?= $repartition['abonnes'] ?></div>
        </div>
        <div class="stat-bar-row">
          <div class="stat-bar-label">Non abonnés</div>
          <div class="stat-bar-track">
            <div class="stat-bar-fill" style="width:<?= $pctNAbo ?>%;background:var(--muted)"></div>
          </div>
          <div class="stat-bar-count"><?= $repartition['non_abonnes'] ?></div>
        </div>
      </div>
      <canvas id="chartRepartition" style="margin-top:16px;max-height:140px"></canvas>
    </div>
  </div>

  <!-- CHARTS ROW 2 -->
  <div class="charts-row">
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Abonnements</div>
        <span class="chart-tag">Souscriptions</span>
      </div>
      <canvas id="chartAbonnements"></canvas>
    </div>
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Candidatures</div>
        <span class="chart-tag">Dépôts</span>
      </div>
      <canvas id="chartCandidatures"></canvas>
    </div>
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Traitement</div>
        <span class="chart-tag">Statuts</span>
      </div>
      <div class="traitement-wrap">
        <canvas id="chartTraitement" style="max-height:160px;max-width:160px"></canvas>
        <div class="traitement-legend">
          <div class="legend-item">
            <div class="legend-dot" style="background:var(--success)"></div>
            <span>Traitées : <strong><?= $traitement['traitees'] ?? 0 ?></strong></span>
          </div>
          <div class="legend-item">
            <div class="legend-dot" style="background:var(--warning)"></div>
            <span>En attente : <strong><?= $traitement['non_traitees'] ?? 0 ?></strong></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CHART REVENUS -->
  <div class="chart-card">
    <div class="chart-header">
      <div class="chart-title">Revenus (FCFA)</div>
      <span class="chart-tag" id="tag-revenus"><?= ucfirst($periode) ?></span>
    </div>
    <canvas id="chartRevenus" style="max-height:180px"></canvas>
  </div>

</main>

<script>
// ── Données PHP → JS ──────────────────────────────────────
const DATA = {
  periode:       '<?= $periode ?>',
  inscriptions:  <?= json_encode($inscriptions) ?>,
  abonnements:   <?= json_encode($abonnements_periode) ?>,
  candidatures:  <?= json_encode($candidatures_periode) ?>,
  traitement:    <?= json_encode($traitement) ?>,
  repartition:   <?= json_encode($repartition) ?>,
  revenus:       <?= json_encode($revenus_periode) ?>
};

// ── Helpers ───────────────────────────────────────────────
const labels = arr => arr.map(r => r.periode);
const vals   = (arr, key) => arr.map(r => r[key] ?? 0);

const CHART_DEFAULTS = {
  plugins: { legend: { display: false } },
  scales:  {
    x: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#64748b', font: { size: 11 } } },
    y: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#64748b', font: { size: 11 } }, beginAtZero: true }
  }
};

// ── Graphiques ────────────────────────────────────────────
let charts = {};

function makeGradient(ctx, color) {
  const g = ctx.createLinearGradient(0, 0, 0, 220);
  g.addColorStop(0, color + '55');
  g.addColorStop(1, color + '00');
  return g;
}

function initCharts(d) {
  Object.values(charts).forEach(c => c.destroy());

  // 1. Inscriptions
  const c1 = document.getElementById('chartInscriptions').getContext('2d');
  charts.inscriptions = new Chart(c1, {
    type: 'line',
    data: {
      labels: labels(d.inscriptions),
      datasets: [{
        data: vals(d.inscriptions, 'total'),
        borderColor: '#00d4aa', borderWidth: 2,
        backgroundColor: makeGradient(c1, '#00d4aa'),
        fill: true, tension: 0.4, pointRadius: 4,
        pointBackgroundColor: '#00d4aa'
      }]
    },
    options: { ...CHART_DEFAULTS, plugins: { legend: { display: false } } }
  });

  // 2. Répartition donut
  const c2 = document.getElementById('chartRepartition').getContext('2d');
  charts.repartition = new Chart(c2, {
    type: 'doughnut',
    data: {
      labels: ['Entreprises', 'Abonnés', 'Non abonnés'],
      datasets: [{
        data: [d.repartition.entreprises, d.repartition.abonnes, d.repartition.non_abonnes],
        backgroundColor: ['#ff6b35','#00d4aa','#1e2d45'],
        borderWidth: 0
      }]
    },
    options: {
      plugins: {
        legend: { display: true, position: 'bottom', labels: { color: '#64748b', font: { size: 11 } } }
      },
      cutout: '65%'
    }
  });

  // 3. Abonnements
  const c3 = document.getElementById('chartAbonnements').getContext('2d');
  charts.abonnements = new Chart(c3, {
    type: 'bar',
    data: {
      labels: labels(d.abonnements),
      datasets: [{
        data: vals(d.abonnements, 'total'),
        backgroundColor: '#7c3aed88',
        borderColor: '#7c3aed',
        borderWidth: 1, borderRadius: 4
      }]
    },
    options: { ...CHART_DEFAULTS }
  });

  // 4. Candidatures
  const c4 = document.getElementById('chartCandidatures').getContext('2d');
  charts.candidatures = new Chart(c4, {
    type: 'bar',
    data: {
      labels: labels(d.candidatures),
      datasets: [
        {
          label: 'Abonnement',
          data: vals(d.candidatures, 'via_abonnement'),
          backgroundColor: '#00d4aa88', borderColor: '#00d4aa',
          borderWidth: 1, borderRadius: 4
        },
        {
          label: 'Unitaire',
          data: vals(d.candidatures, 'unitaires'),
          backgroundColor: '#ff6b3588', borderColor: '#ff6b35',
          borderWidth: 1, borderRadius: 4
        }
      ]
    },
    options: {
      ...CHART_DEFAULTS,
      plugins: {
        legend: { display: true, labels: { color: '#64748b', font: { size: 10 } } }
      },
      scales: CHART_DEFAULTS.scales
    }
  });

  // 5. Traitement donut
  const c5 = document.getElementById('chartTraitement').getContext('2d');
  charts.traitement = new Chart(c5, {
    type: 'doughnut',
    data: {
      labels: ['Traitées', 'En attente'],
      datasets: [{
        data: [d.traitement.traitees ?? 0, d.traitement.non_traitees ?? 0],
        backgroundColor: ['#10b981','#f59e0b'],
        borderWidth: 0
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      cutout: '70%'
    }
  });

  // 6. Revenus
  const c6 = document.getElementById('chartRevenus').getContext('2d');
  charts.revenus = new Chart(c6, {
    type: 'line',
    data: {
      labels: labels(d.revenus),
      datasets: [{
        data: vals(d.revenus, 'revenus'),
        borderColor: '#f59e0b', borderWidth: 2,
        backgroundColor: makeGradient(c6, '#f59e0b'),
        fill: true, tension: 0.4, pointRadius: 4,
        pointBackgroundColor: '#f59e0b'
      }]
    },
    options: { ...CHART_DEFAULTS }
  });
}

// ── Switch période via AJAX ───────────────────────────────
function switchPeriode(p) {
  document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
  event.target.classList.add('active');

  const types = ['inscriptions','abonnements','candidatures','traitement','revenus'];
  const fetches = types.map(t =>
    fetch(`<?= BASE_URL ?>/admin/stats/ajax?periode=${p}&type=${t}`)
      .then(r => r.json())
      .then(j => ({ type: t, data: j.data }))
  );

  Promise.all(fetches).then(results => {
    results.forEach(r => { DATA[r.type] = r.data; });
    ['tag-inscriptions','tag-revenus'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = p.charAt(0).toUpperCase() + p.slice(1);
    });
    initCharts(DATA);
  });
}

// Init au chargement
initCharts(DATA);
</script>
</body>
</html>
