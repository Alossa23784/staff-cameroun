<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Dashboard Entreprise</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --bg:#0a0e1a; --surface:#111827; --border:#1e2d45;
  --accent:#00d4aa; --accent2:#ff6b35; --accent3:#7c3aed;
  --text:#e2e8f0; --muted:#64748b;
  --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
}
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

/* SIDEBAR */
.sidebar { width:240px; background:var(--surface); border-right:1px solid var(--border); position:fixed; top:0; left:0; bottom:0; display:flex; flex-direction:column; }
.sidebar-logo { padding:24px 20px 18px; border-bottom:1px solid var(--border); }
.sidebar-logo h1 { font-family:'Syne',sans-serif; font-weight:800; font-size:1.5rem; color:var(--accent); letter-spacing:-0.04em; }
.sidebar-logo span { font-size:0.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.1em; }
.nav-item { display:flex; align-items:center; gap:10px; padding:10px 16px; margin:2px 8px; border-radius:8px; text-decoration:none; color:var(--muted); font-size:0.85rem; transition:all 0.2s; cursor:pointer; border:none; background:none; width:calc(100% - 16px); }
.nav-item:hover,.nav-item.active { background:rgba(0,212,170,.1); color:var(--accent); }
.sidebar-footer { margin-top:auto; padding:16px; border-top:1px solid var(--border); font-size:0.75rem; color:var(--muted); }

/* MAIN */
.main { margin-left:240px; flex:1; padding:28px 32px; }
.page-title { font-family:'Syne',sans-serif; font-weight:700; font-size:1.5rem; letter-spacing:-0.03em; margin-bottom:4px; }
.page-sub { color:var(--muted); font-size:0.85rem; margin-bottom:24px; }

/* KPI */
.kpi-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:28px; }
.kpi { background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:16px; }
.kpi-val { font-family:'Syne',sans-serif; font-weight:700; font-size:1.6rem; letter-spacing:-0.04em; }
.kpi-lbl { font-size:0.75rem; color:var(--muted); margin-top:4px; }

/* TABS */
.tabs { display:flex; gap:4px; background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:4px; margin-bottom:20px; width:fit-content; }
.tab { padding:8px 18px; border-radius:7px; border:none; background:none; color:var(--muted); cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.85rem; font-weight:500; transition:all 0.2s; }
.tab.active { background:var(--accent); color:#0a0e1a; font-weight:600; }

/* PANELS */
.panel { display:none; }
.panel.active { display:block; }

/* TABLE */
.table-wrap { background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
table { width:100%; border-collapse:collapse; }
th { padding:12px 16px; text-align:left; font-size:0.75rem; font-weight:500; color:var(--muted); text-transform:uppercase; letter-spacing:0.06em; border-bottom:1px solid var(--border); }
td { padding:14px 16px; font-size:0.85rem; border-bottom:1px solid rgba(30,45,69,.5); vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:rgba(255,255,255,.02); }

/* AVATAR */
.avatar { width:36px; height:36px; border-radius:50%; background:var(--accent3); display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:600; color:#fff; }

/* BADGES */
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:500; }
.badge-wait    { background:rgba(245,158,11,.15); color:var(--warning); }
.badge-ok      { background:rgba(16,185,129,.15); color:var(--success); }
.badge-no      { background:rgba(239,68,68,.15);  color:var(--danger); }
.badge-stage   { background:rgba(124,58,237,.15); color:var(--accent3); }

/* ACTIONS */
.btn-sm { padding:5px 12px; border-radius:6px; border:none; cursor:pointer; font-size:0.78rem; font-weight:500; font-family:'DM Sans',sans-serif; transition:all 0.2s; }
.btn-accept  { background:rgba(16,185,129,.15);  color:var(--success); }
.btn-refuse  { background:rgba(239,68,68,.15);   color:var(--danger); }
.btn-stage   { background:rgba(124,58,237,.15);  color:var(--accent3); }
.btn-sm:hover { opacity:0.8; transform:scale(1.03); }

/* ÉTOILES */
.stars { display:inline-flex; gap:2px; }
.star { font-size:1.2rem; cursor:pointer; color:#1e2d45; transition:color 0.15s; }
.star.filled,.star:hover,.star.hover { color:var(--warning); }

/* MODAL */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.7); display:none; align-items:center; justify-content:center; z-index:999; }
.modal-overlay.open { display:flex; }
.modal { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:28px; width:360px; }
.modal h3 { font-family:'Syne',sans-serif; font-weight:700; font-size:1.1rem; margin-bottom:16px; }
.modal-stars { display:flex; gap:8px; justify-content:center; margin:16px 0; }
.modal-star { font-size:2rem; cursor:pointer; color:#1e2d45; transition:color 0.15s; }
.modal-star.active { color:var(--warning); }
.btn-confirm { width:100%; background:var(--accent); color:#0a0e1a; border:none; border-radius:8px; padding:11px; font-family:'Syne',sans-serif; font-weight:700; cursor:pointer; margin-top:8px; }
.btn-cancel  { width:100%; background:none; border:1px solid var(--border); color:var(--muted); border-radius:8px; padding:10px; font-family:'DM Sans',sans-serif; cursor:pointer; margin-top:8px; }

.empty { text-align:center; padding:40px; color:var(--muted); font-size:0.88rem; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>STAFF</h1>
    <span>Espace Entreprise</span>
  </div>
  <div style="padding:12px 8px">
    <button class="nav-item active" onclick="showTab('attente')">📥 Candidatures reçues</button>
    <button class="nav-item" onclick="showTab('acceptees')">✅ Stages acceptés</button>
    <button class="nav-item" onclick="showTab('cours')">🎓 Stages en cours</button>
  </div>
  <div class="sidebar-footer">
    <?= htmlspecialchars($entreprise['nom'] ?? '') ?><br>
    <a href="<?= BASE_URL ?>/logout" style="color:var(--danger);text-decoration:none;font-size:0.78rem">⏻ Déconnexion</a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="page-title"><?= htmlspecialchars($entreprise['nom'] ?? 'Dashboard') ?></div>
  <div class="page-sub">Gestion des candidatures — <?= date('d F Y') ?></div>

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
    <div class="kpi"><div class="kpi-val"><?= $stats['total'] ?></div><div class="kpi-lbl">Total reçues</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--warning)"><?= $stats['en_attente'] ?></div><div class="kpi-lbl">En attente</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--success)"><?= $stats['acceptees'] ?></div><div class="kpi-lbl">Acceptées</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--danger)"><?= $stats['refusees'] ?></div><div class="kpi-lbl">Refusées</div></div>
    <div class="kpi"><div class="kpi-val" style="color:var(--accent3)"><?= $stats['en_cours'] ?></div><div class="kpi-lbl">En cours</div></div>
  </div>

  <!-- TABS -->
  <div class="tabs">
    <button class="tab active" id="tab-attente"  onclick="showTab('attente')">📥 En attente (<?= $stats['en_attente'] ?>)</button>
    <button class="tab"        id="tab-acceptees" onclick="showTab('acceptees')">✅ Acceptées (<?= $stats['acceptees'] ?>)</button>
    <button class="tab"        id="tab-cours"     onclick="showTab('cours')">🎓 Stages en cours (<?= $stats['en_cours'] ?>)</button>
  </div>

  <!-- PANEL: En attente -->
  <div class="panel active" id="panel-attente">
    <div class="table-wrap">
      <?php if (empty($candidatures)): ?>
        <div class="empty">Aucune candidature en attente</div>
      <?php else: ?>
      <table>
        <thead><tr><th>Candidat</th><th>Email</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($candidatures as $c): ?>
          <tr id="row-<?= $c['id'] ?>">
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="avatar"><?= strtoupper(substr($c['prenom'] ?? $c['nom'], 0, 1)) ?></div>
                <div>
                  <div style="font-weight:500"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></div>
                  <?php if ($c['note_moyenne'] > 0): ?>
                    <div style="font-size:0.72rem;color:var(--warning)">★ <?= $c['note_moyenne'] ?>/5</div>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td style="color:var(--muted)"><?= htmlspecialchars($c['chercheur_email']) ?></td>
            <td style="color:var(--muted)"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
            <td><span class="badge badge-wait">En attente</span></td>
            <td style="display:flex;gap:6px">
              <button class="btn-sm btn-accept" onclick="changerStatut(<?= $c['id'] ?>,'acceptee')">✓ Accepter</button>
              <button class="btn-sm btn-refuse" onclick="changerStatut(<?= $c['id'] ?>,'refusee')">✗ Refuser</button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- PANEL: Acceptées -->
  <div class="panel" id="panel-acceptees">
    <div class="table-wrap">
      <?php if (empty($acceptees)): ?>
        <div class="empty">Aucun stage accepté</div>
      <?php else: ?>
      <table>
        <thead><tr><th>Candidat</th><th>Email</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($acceptees as $c): ?>
          <tr id="row-acc-<?= $c['id'] ?>">
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="avatar"><?= strtoupper(substr($c['prenom'] ?? $c['nom'], 0, 1)) ?></div>
                <div style="font-weight:500"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></div>
              </div>
            </td>
            <td style="color:var(--muted)"><?= htmlspecialchars($c['chercheur_email']) ?></td>
            <td style="color:var(--muted)"><?= date('d/m/Y', strtotime($c['updated_at'])) ?></td>
            <td>
              <button class="btn-sm btn-stage" onclick="marquerStagiaire(<?= $c['id'] ?>)">
                🎓 Marquer comme Stagiaire
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- PANEL: Stages en cours -->
  <div class="panel" id="panel-cours">
    <div class="table-wrap">
      <?php if (empty($stages_en_cours)): ?>
        <div class="empty">Aucun stage en cours</div>
      <?php else: ?>
      <table>
        <thead><tr><th>Stagiaire</th><th>Email</th><th>Note actuelle</th><th>Notation</th></tr></thead>
        <tbody>
        <?php foreach ($stages_en_cours as $c): ?>
          <tr id="row-cours-<?= $c['id'] ?>">
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="avatar"><?= strtoupper(substr($c['prenom'] ?? $c['nom'], 0, 1)) ?></div>
                <div style="font-weight:500"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></div>
              </div>
            </td>
            <td style="color:var(--muted)"><?= htmlspecialchars($c['chercheur_email']) ?></td>
            <td>
              <?php if ($c['note_attribuee']): ?>
                <span style="color:var(--warning)">★ <?= $c['note_attribuee'] ?>/5 — déjà noté</span>
              <?php else: ?>
                <span style="color:var(--muted)">Pas encore noté</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!$c['note_attribuee']): ?>
                <button class="btn-sm btn-accept" onclick="ouvrirNotation(<?= $c['id'] ?>)">
                  ⭐ Noter
                </button>
              <?php else: ?>
                <span style="font-size:0.8rem;color:var(--muted)">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</main>

<!-- MODAL NOTATION -->
<div class="modal-overlay" id="modalNotation">
  <div class="modal">
    <h3>⭐ Noter le stagiaire</h3>
    <p style="color:var(--muted);font-size:0.85rem">Attribuez une note de 1 à 5 étoiles. Une seule note possible.</p>
    <div class="modal-stars" id="modalStars">
      <span class="modal-star" data-val="1" onclick="selectStar(1)">★</span>
      <span class="modal-star" data-val="2" onclick="selectStar(2)">★</span>
      <span class="modal-star" data-val="3" onclick="selectStar(3)">★</span>
      <span class="modal-star" data-val="4" onclick="selectStar(4)">★</span>
      <span class="modal-star" data-val="5" onclick="selectStar(5)">★</span>
    </div>
    <p id="noteLabel" style="text-align:center;color:var(--muted);font-size:0.82rem;margin-bottom:8px">Sélectionnez une note</p>
    <button class="btn-confirm" onclick="confirmerNote()" id="btnConfirmNote" disabled>Confirmer la note</button>
    <button class="btn-cancel"  onclick="fermerModal()">Annuler</button>
  </div>
</div>

<script>
let candidatureIdEnCours = null;
let noteSelectionnee     = null;

// ── Tabs ──────────────────────────────────────────────────
function showTab(name) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.getElementById('panel-' + name).classList.add('active');
  document.getElementById('tab-'   + name).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
}

// ── Changer statut ────────────────────────────────────────
async function changerStatut(id, statut) {
  const form = new FormData();
  form.append('id', id);
  form.append('statut', statut);
  const res  = await fetch('<?= BASE_URL ?>/entreprise/statut', { method:'POST', body:form });
  const data = await res.json();
  if (data.success) {
    document.getElementById('row-' + id)?.remove();
  } else {
    alert(data.message);
  }
}

// ── Marquer stagiaire ─────────────────────────────────────
async function marquerStagiaire(id) {
  if (!confirm('Confirmer : ce candidat devient stagiaire actif ?')) return;
  const form = new FormData();
  form.append('id', id);
  const res  = await fetch('<?= BASE_URL ?>/entreprise/stagiaire', { method:'POST', body:form });
  const data = await res.json();
  if (data.success) {
    document.getElementById('row-acc-' + id)?.remove();
    alert('✅ ' + data.message + '\nVoir l\'onglet "Stages en cours".');
  } else {
    alert(data.message);
  }
}

// ── Notation ──────────────────────────────────────────────
function ouvrirNotation(id) {
  candidatureIdEnCours = id;
  noteSelectionnee     = null;
  document.querySelectorAll('.modal-star').forEach(s => s.classList.remove('active'));
  document.getElementById('noteLabel').textContent = 'Sélectionnez une note';
  document.getElementById('btnConfirmNote').disabled = true;
  document.getElementById('modalNotation').classList.add('open');
}

function fermerModal() {
  document.getElementById('modalNotation').classList.remove('open');
}

function selectStar(val) {
  noteSelectionnee = val;
  document.querySelectorAll('.modal-star').forEach(s => {
    s.classList.toggle('active', parseInt(s.dataset.val) <= val);
  });
  const labels = ['','Très insuffisant','Insuffisant','Passable','Bien','Très bien'];
  document.getElementById('noteLabel').textContent = `${val}/5 — ${labels[val]}`;
  document.getElementById('btnConfirmNote').disabled = false;
}

async function confirmerNote() {
  if (!noteSelectionnee || !candidatureIdEnCours) return;
  const form = new FormData();
  form.append('id',   candidatureIdEnCours);
  form.append('note', noteSelectionnee);
  const res  = await fetch('<?= BASE_URL ?>/entreprise/noter', { method:'POST', body:form });
  const data = await res.json();
  fermerModal();
  if (data.success) {
    const btn = document.querySelector(`#row-cours-${candidatureIdEnCours} button`);
    if (btn) btn.parentElement.innerHTML = `<span style="color:var(--warning)">★ ${noteSelectionnee}/5 — noté</span>`;
    alert('✅ ' + data.message);
  } else {
    alert('❌ ' + data.message);
  }
}
</script>
</body>
</html>
