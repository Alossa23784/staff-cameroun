<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Mon Espace</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --bg:#0a0e1a; --surface:#111827; --border:#1e2d45;
  --accent:#00d4aa; --accent2:#ff6b35; --accent3:#7c3aed;
  --text:#e2e8f0; --muted:#64748b;
  --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;}

/* SIDEBAR */
.sidebar{width:240px;background:var(--surface);border-right:1px solid var(--border);position:fixed;top:0;left:0;bottom:0;display:flex;flex-direction:column;}
.sidebar-logo{padding:24px 20px 18px;border-bottom:1px solid var(--border);}
.sidebar-logo h1{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:var(--accent);letter-spacing:-0.04em;}
.sidebar-logo span{font-size:0.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 16px;margin:2px 8px;border-radius:8px;text-decoration:none;color:var(--muted);font-size:0.85rem;transition:all 0.2s;cursor:pointer;border:none;background:none;width:calc(100% - 16px);}
.nav-item:hover,.nav-item.active{background:rgba(0,212,170,.1);color:var(--accent);}
.sidebar-footer{margin-top:auto;padding:16px;border-top:1px solid var(--border);font-size:0.75rem;color:var(--muted);}

/* MAIN */
.main{margin-left:240px;flex:1;padding:28px 32px;}
.page-title{font-family:'Syne',sans-serif;font-weight:700;font-size:1.5rem;letter-spacing:-0.03em;margin-bottom:4px;}
.page-sub{color:var(--muted);font-size:0.85rem;margin-bottom:24px;}

/* ABONNEMENT BANNER */
.abo-banner{border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.abo-active{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);}
.abo-inactive{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);}
.abo-text{font-size:0.85rem;}
.abo-label{font-weight:600;margin-bottom:2px;}
.abo-sub{color:var(--muted);font-size:0.78rem;}
.btn-abo{padding:8px 18px;border-radius:8px;border:none;background:var(--accent);color:#0a0e1a;font-family:'Syne',sans-serif;font-weight:700;font-size:0.82rem;cursor:pointer;text-decoration:none;white-space:nowrap;}

/* KPI */
.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:28px;}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px;}
.kpi-val{font-family:'Syne',sans-serif;font-weight:700;font-size:1.6rem;letter-spacing:-0.04em;}
.kpi-lbl{font-size:0.75rem;color:var(--muted);margin-top:4px;}

/* TABS */
.tabs{display:flex;gap:4px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:4px;margin-bottom:20px;width:fit-content;}
.tab{padding:8px 16px;border-radius:7px;border:none;background:none;color:var(--muted);cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.83rem;font-weight:500;transition:all 0.2s;}
.tab.active{background:var(--accent);color:#0a0e1a;font-weight:600;}

/* PANELS */
.panel{display:none;}
.panel.active{display:block;}

/* TABLE */
.table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
th{padding:12px 16px;text-align:left;font-size:0.75rem;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:0.06em;border-bottom:1px solid var(--border);}
td{padding:14px 16px;font-size:0.85rem;border-bottom:1px solid rgba(30,45,69,.5);vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:rgba(255,255,255,.02);}

/* BADGES */
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:500;}
.badge-wait{background:rgba(245,158,11,.15);color:var(--warning);}
.badge-ok{background:rgba(16,185,129,.15);color:var(--success);}
.badge-no{background:rgba(239,68,68,.15);color:var(--danger);}
.badge-stage{background:rgba(124,58,237,.15);color:var(--accent3);}

/* ENTREPRISES GRID */
.ent-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;}
.ent-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px;transition:all 0.2s;}
.ent-card:hover{border-color:rgba(0,212,170,.3);transform:translateY(-2px);}
.ent-logo{width:44px;height:44px;border-radius:10px;background:var(--accent3);display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:10px;}
.ent-nom{font-weight:600;font-size:0.9rem;margin-bottom:4px;}
.ent-secteur{font-size:0.75rem;color:var(--muted);}
.btn-postuler{margin-top:12px;width:100%;padding:8px;border-radius:7px;border:none;background:rgba(0,212,170,.1);color:var(--accent);font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:500;cursor:pointer;transition:all 0.2s;}
.btn-postuler:hover{background:var(--accent);color:#0a0e1a;}

.empty{text-align:center;padding:40px;color:var(--muted);font-size:0.88rem;}

/* NOTE ÉTOILES */
.stars{color:var(--warning);font-size:0.85rem;}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>STAFF</h1>
    <span>Espace Chercheur</span>
  </div>
  <div style="padding:12px 8px">
    <button class="nav-item active" onclick="showTab('candidatures')">📁 Mes candidatures</button>
    <button class="nav-item" onclick="showTab('entreprises')">🏢 Entreprises</button>
    <?php if ($abonne): ?>
    <a href="<?= BASE_URL ?>/abonnement" class="nav-item">💳 Mon abonnement</a>
    <?php else: ?>
    <a href="<?= BASE_URL ?>/abonnement" class="nav-item" style="color:var(--warning)">⭐ S'abonner</a>
    <?php endif; ?>
  </div>
  <div class="sidebar-footer">
    <?= htmlspecialchars(($profil['prenom'] ?? '') . ' ' . ($profil['nom'] ?? '')) ?><br>
    <?php if ($profil['note_moyenne'] > 0): ?>
      <span style="color:var(--warning)">★ <?= $profil['note_moyenne'] ?>/5</span><br>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/logout" style="color:var(--danger);text-decoration:none;font-size:0.78rem">⏻ Déconnexion</a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="page-title">Bonjour, <?= htmlspecialchars($profil['prenom'] ?? 'Chercheur') ?> 👋</div>
  <div class="page-sub"><?= date('d F Y') ?></div>

  <!-- Bannière abonnement -->
  <?php if ($abonne): ?>
  <div class="abo-banner abo-active">
    <div class="abo-text">
      <div class="abo-label" style="color:var(--success)">✅ Abonnement actif</div>
      <div class="abo-sub">Expire le <?= $dateFin?->format('d/m/Y') ?></div>
    </div>
    <a href="<?= BASE_URL ?>/abonnement" class="btn-abo">Renouveler</a>
  </div>
  <?php else: ?>
  <div class="abo-banner abo-inactive">
    <div class="abo-text">
      <div class="abo-label" style="color:var(--warning)">⚠️ Pas d'abonnement actif</div>
      <div class="abo-sub">Abonnez-vous pour postuler — 3 500 FCFA/an</div>
    </div>
    <a href="<?= BASE_URL ?>/abonnement" class="btn-abo">S'abonner →</a>
  </div>
  <?php endif; ?>

  <!-- KPI -->
  
  <?php if (!empty($flash)): ?>
    <div style="padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:.875rem;font-weight:500;
         background:<?= $flash['type']==='success' ? 'rgba(16,185,129,.1)' : 'rgba(239,68,68,.1)' ?>;
         border:1px solid <?= $flash['type']==='success' ? 'rgba(16,185,129,.3)' : 'rgba(239,68,68,.3)' ?>;
         color:<?= $flash['type']==='success' ? '#10b981' : '#ef4444' ?>">
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  <?php endif; ?>
  <div class="kpi-grid">
    <div class="kpi"><div class="kpi-val"><?= $stats['total'] ?></div><div class="kpi-lbl">Candidatures</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--warning)"><?= $stats['en_attente'] ?></div><div class="kpi-lbl">En attente</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--success)"><?= $stats['acceptees'] ?></div><div class="kpi-lbl">Acceptées</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--danger)"><?= $stats['refusees'] ?></div><div class="kpi-lbl">Refusées</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--accent3)"><?= $stats['en_cours'] ?></div><div class="kpi-lbl">En cours</div></div>
  </div>

  <!-- TABS -->
  <div class="tabs">
    <button class="tab active" id="tab-candidatures" onclick="showTab('candidatures')">📁 Mes candidatures</button>
    <button class="tab"        id="tab-entreprises"  onclick="showTab('entreprises')">🏢 Entreprises (<?= count($entreprises) ?>)</button>
  </div>

  <!-- PANEL: Candidatures -->
  <div class="panel active" id="panel-candidatures">
    <div class="table-wrap">
      <?php if (empty($candidatures)): ?>
        <div class="empty">Vous n'avez encore postulé nulle part.<br>Explorez les entreprises pour commencer !</div>
      <?php else: ?>
      <table>
        <thead><tr><th>Entreprise</th><th>Date</th><th>Type</th><th>Statut</th><th>Note reçue</th></tr></thead>
        <tbody>
        <?php foreach ($candidatures as $c): ?>
          <tr>
            <td style="font-weight:500"><?= htmlspecialchars($c['entreprise_nom']) ?></td>
            <td style="color:var(--muted)"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
            <td>
              <?php if ($c['type_depot'] === 'abonnement'): ?>
                <span style="color:var(--accent);font-size:0.78rem">★ Abonnement</span>
              <?php else: ?>
                <span style="color:var(--muted);font-size:0.78rem">Unitaire</span>
              <?php endif; ?>
            </td>
            <td>
              <?php $badges = ['en_attente'=>'badge-wait En attente','acceptee'=>'badge-ok Acceptée','refusee'=>'badge-no Refusée','stage_en_cours'=>'badge-stage Stage en cours'];
              [$cls, $lbl] = explode(' ', $badges[$c['statut']] ?? 'badge-wait En attente', 2); ?>
              <span class="badge <?= $cls ?>"><?= $lbl ?></span>
            </td>
            <td>
              <?php if ($c['note_attribuee']): ?>
                <span class="stars"><?= str_repeat('★', (int)$c['note_attribuee']) ?><?= str_repeat('☆', 5 - (int)$c['note_attribuee']) ?> <?= $c['note_attribuee'] ?>/5</span>
              <?php else: ?>
                <span style="color:var(--muted);font-size:0.78rem">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- PANEL: Entreprises -->
  <div class="panel" id="panel-entreprises">
    <?php if (empty($entreprises)): ?>
      <div class="empty">Aucune entreprise disponible pour le moment.</div>
    <?php else: ?>
    <div class="ent-grid">
      <?php foreach ($entreprises as $e): ?>
      <div class="ent-card">
        <div class="ent-logo">🏢</div>
        <div class="ent-nom"><?= htmlspecialchars($e['nom']) ?></div>
        <div class="ent-secteur"><?= htmlspecialchars($e['secteur'] ?? 'Secteur non précisé') ?></div>
        <?php if ($e['ville']): ?>
          <div class="ent-secteur" style="margin-top:3px">📍 <?= htmlspecialchars($e['ville']) ?></div>
        <?php endif; ?>
        <?php if ($abonne): ?>
          <button class="btn-postuler" onclick="postuler(<?= $e['id'] ?>)">Postuler →</button>
        <?php else: ?>
          <button class="btn-postuler" style="opacity:0.5;cursor:not-allowed" title="Abonnement requis">🔒 Abonnement requis</button>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</main>

<script>
function showTab(name) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.getElementById('panel-' + name).classList.add('active');
  document.getElementById('tab-'   + name).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
}

async function postuler(entrepriseId) {
  if (!confirm('Confirmer votre candidature à cette entreprise ?')) return;
  const form = new FormData();
  form.append('entreprise_id', entrepriseId);
  const res  = await fetch('<?= BASE_URL ?>/chercheur/postuler', { method:'POST', body:form });
  const data = await res.json();
  alert((data.success ? '✅ ' : '❌ ') + data.message);
  if (data.success) location.reload();
}
</script>
</body>
</html>
