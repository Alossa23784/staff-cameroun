<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>STAFF Admin — <?= $pageTitle ?? 'Panel' ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap');
:root{
  --bg:#0a0e1a;--surface:#111827;--surface2:#0f1623;--border:#1e2d45;
  --accent:#00d4aa;--accent2:#ff6b35;--accent3:#7c3aed;
  --text:#e2e8f0;--muted:#64748b;--success:#10b981;--warning:#f59e0b;--danger:#ef4444;
  --sidebar-w:240px;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;}

/* ── SIDEBAR ── */
.sidebar{
  width:var(--sidebar-w);min-height:100vh;background:var(--surface);
  border-right:1px solid var(--border);display:flex;flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;z-index:50;transition:transform 0.3s ease;
}
.sidebar-logo{padding:22px 20px;border-bottom:1px solid var(--border);}
.sidebar-logo a{font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:var(--accent);text-decoration:none;letter-spacing:-0.04em;}
.sidebar-logo span{display:block;font-size:0.68rem;color:var(--muted);font-weight:400;margin-top:2px;letter-spacing:0.05em;text-transform:uppercase;}
.sidebar-nav{flex:1;padding:16px 0;overflow-y:auto;}
.nav-section{padding:6px 16px 4px;font-size:0.65rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;font-weight:600;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 18px;color:var(--muted);text-decoration:none;font-size:0.85rem;font-weight:500;transition:all 0.2s;border-left:3px solid transparent;margin:1px 0;}
.nav-item:hover{color:var(--text);background:rgba(255,255,255,.04);}
.nav-item.active{color:var(--accent);background:rgba(0,212,170,.08);border-left-color:var(--accent);}
.nav-icon{font-size:1rem;width:20px;text-align:center;flex-shrink:0;}
.nav-badge{margin-left:auto;background:var(--accent);color:#0a0e1a;font-size:0.65rem;font-weight:700;padding:2px 7px;border-radius:10px;}
.sidebar-footer{padding:16px;border-top:1px solid var(--border);}
.sidebar-user{display:flex;align-items:center;gap:10px;}
.user-avatar{width:34px;height:34px;border-radius:50%;background:rgba(0,212,170,.15);display:flex;align-items:center;justify-content:center;color:var(--accent);font-weight:700;font-size:0.85rem;flex-shrink:0;}
.user-info{flex:1;min-width:0;}
.user-name{font-size:0.82rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.user-role{font-size:0.68rem;color:var(--muted);}
.btn-logout{color:var(--muted);text-decoration:none;font-size:0.75rem;transition:color 0.2s;}
.btn-logout:hover{color:var(--danger);}

/* ── MAIN ── */
.main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* ── TOPBAR ── */
.topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:40;}
.topbar-left{display:flex;align-items:center;gap:14px;}
.btn-menu{display:none;background:none;border:none;color:var(--text);font-size:1.3rem;cursor:pointer;padding:6px;}
.page-breadcrumb{font-size:0.82rem;color:var(--muted);}
.page-breadcrumb span{color:var(--text);font-weight:500;}
.topbar-right{display:flex;align-items:center;gap:12px;}
.topbar-date{font-size:0.78rem;color:var(--muted);}

/* ── CONTENU ── */
.content{padding:28px;flex:1;}

/* ── COMPOSANTS COMMUNS ── */
.page-header{margin-bottom:24px;}
.page-title{font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;letter-spacing:-0.04em;}
.page-sub{color:var(--muted);font-size:0.85rem;margin-top:4px;}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
.kpi-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;}
.kpi-val{font-family:'Syne',sans-serif;font-weight:700;font-size:2rem;letter-spacing:-0.04em;}
.kpi-lbl{font-size:0.75rem;color:var(--muted);margin-top:4px;}
.kpi-trend{font-size:0.72rem;margin-top:8px;}
.trend-up{color:var(--success);}
.trend-down{color:var(--danger);}

.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px;}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;}
.card-body{padding:20px;}

/* Table */
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th{padding:10px 14px;text-align:left;font-size:0.7rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.07em;border-bottom:1px solid var(--border);white-space:nowrap;}
td{padding:12px 14px;font-size:0.83rem;border-bottom:1px solid rgba(30,45,69,.5);vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:rgba(255,255,255,.02);}

/* Badges */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;white-space:nowrap;}
.badge-success{background:rgba(16,185,129,.15);color:var(--success);}
.badge-warning{background:rgba(245,158,11,.15);color:var(--warning);}
.badge-danger{background:rgba(239,68,68,.15);color:var(--danger);}
.badge-accent{background:rgba(0,212,170,.15);color:var(--accent);}
.badge-muted{background:rgba(100,116,139,.15);color:var(--muted);}

/* Boutons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;text-decoration:none;font-family:'DM Sans',sans-serif;}
.btn-primary{background:var(--accent);color:#0a0e1a;}
.btn-primary:hover{opacity:0.88;}
.btn-danger{background:rgba(239,68,68,.15);color:var(--danger);border:1px solid rgba(239,68,68,.3);}
.btn-danger:hover{background:rgba(239,68,68,.25);}
.btn-warning{background:rgba(245,158,11,.15);color:var(--warning);border:1px solid rgba(245,158,11,.3);}
.btn-warning:hover{background:rgba(245,158,11,.25);}
.btn-ghost{background:rgba(255,255,255,.06);color:var(--text);border:1px solid var(--border);}
.btn-ghost:hover{background:rgba(255,255,255,.1);}
.btn-sm{padding:5px 10px;font-size:0.73rem;}

/* Filtres / recherche */
.filters{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center;}
.search-input{background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:8px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.83rem;outline:none;transition:border-color 0.2s;min-width:220px;}
.search-input:focus{border-color:var(--accent);}
.filter-select{background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:8px 12px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.83rem;outline:none;cursor:pointer;}

/* Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:200;align-items:center;justify-content:center;padding:16px;}
.modal-overlay.open{display:flex;}
.modal{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:28px;width:100%;max-width:440px;position:relative;}
.modal-title{font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;margin-bottom:16px;}
.modal-close{position:absolute;top:16px;right:16px;background:none;border:none;color:var(--muted);font-size:1.2rem;cursor:pointer;}
.form-group{margin-bottom:14px;}
.form-label{display:block;font-size:0.75rem;color:var(--muted);margin-bottom:5px;}
.form-input{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:9px 12px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.88rem;outline:none;transition:border-color 0.2s;}
.form-input:focus{border-color:var(--accent);}
.form-select{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:9px 12px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.88rem;outline:none;cursor:pointer;}

/* Toast */
.toast{position:fixed;bottom:24px;right:24px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 18px;font-size:0.83rem;z-index:300;display:none;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);}
.toast.show{display:flex;}
.toast-ok{border-color:rgba(16,185,129,.4);color:var(--success);}
.toast-err{border-color:rgba(239,68,68,.4);color:var(--danger);}

/* Overlay sidebar mobile */
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:49;}

/* ── RESPONSIVE ── */
@media(max-width:1200px){
  .kpi-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}
  .sidebar-overlay.open{display:block;}
  .main{margin-left:0;}
  .btn-menu{display:flex;}
  .content{padding:16px;}
  .topbar{padding:0 16px;}
}
@media(max-width:600px){
  .kpi-grid{grid-template-columns:1fr 1fr;}
  .filters{flex-direction:column;align-items:stretch;}
  .search-input{min-width:unset;width:100%;}
  .topbar-date{display:none;}
  th,td{padding:10px 10px;}
}
@media(max-width:400px){
  .kpi-grid{grid-template-columns:1fr;}
  .page-title{font-size:1.3rem;}
}
</style>
</head>
<body>

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ── SIDEBAR ── -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <a href="<?= BASE_URL ?>/admin/stats">STAFF</a>
    <span>Panel Administration</span>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Tableau de bord</div>
    <a href="<?= BASE_URL ?>/admin/stats" class="nav-item <?= ($activePage??'')=='stats'?'active':'' ?>">
      <span class="nav-icon">📊</span> Statistiques
    </a>

    <div class="nav-section" style="margin-top:8px">Gestion</div>
    <a href="<?= BASE_URL ?>/admin/users" class="nav-item <?= ($activePage??'')=='users'?'active':'' ?>">
      <span class="nav-icon">👥</span> Utilisateurs
    </a>
    <a href="<?= BASE_URL ?>/admin/abonnements" class="nav-item <?= ($activePage??'')=='abonnements'?'active':'' ?>">
      <span class="nav-icon">💳</span> Abonnements
    </a>
    <a href="<?= BASE_URL ?>/admin/parrainages" class="nav-item <?= ($activePage??'')=='parrainages'?'active':'' ?>">
      <span class="nav-icon">🔗</span> Parrainages
    </a>
    <a href="<?= BASE_URL ?>/admin/publicites" class="nav-item <?= ($activePage??'')=='publicites'?'active':'' ?>">
      <span class="nav-icon">📢</span> Publicités
    </a>

    <div class="nav-section" style="margin-top:8px">Site</div>
    <a href="<?= BASE_URL ?>/" target="_blank" class="nav-item">
      <span class="nav-icon">🌐</span> Voir le site
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar">A</div>
      <div class="user-info">
        <div class="user-name">Administrateur</div>
        <div class="user-role">Super Admin</div>
      </div>
      <a href="<?= BASE_URL ?>/logout" class="btn-logout" title="Déconnexion">⎋</a>
    </div>
  </div>
</aside>

<!-- ── MAIN ── -->
<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <button class="btn-menu" onclick="toggleSidebar()">☰</button>
      <div class="page-breadcrumb">Admin / <span><?= $pageTitle ?? '' ?></span></div>
    </div>
    <div class="topbar-right">
      <div class="topbar-date"><?= date('d/m/Y H:i') ?></div>
    </div>
  </div>

  <div class="content">
    <?= $content ?? '' ?>
  </div>
</div>

<!-- Toast notification -->
<div class="toast" id="toast"></div>

<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('open');
}
function showToast(msg, type='ok'){
  const t = document.getElementById('toast');
  t.textContent = (type==='ok' ? '✅ ' : '❌ ') + msg;
  t.className = 'toast show toast-' + type;
  setTimeout(() => t.className = 'toast', 3000);
}
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }

// Fermer modal avec Escape
document.addEventListener('keydown', e => {
  if(e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
