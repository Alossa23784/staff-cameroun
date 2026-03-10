<div class="page-header">
  <div class="page-title">👥 Utilisateurs</div>
  <div class="page-sub">Total : <strong><?= $total ?></strong> utilisateurs inscrits</div>
</div>

<!-- KPI -->
<?php
$kpis = $db->query("SELECT
  COUNT(*) AS total,
  SUM(role='chercheur') AS chercheurs,
  SUM(role='entreprise') AS entreprises,
  SUM(is_active=0) AS bloques
FROM users WHERE role != 'admin'")->fetch();
?>
<div class="kpi-grid">
  <div class="kpi-card"><div class="kpi-val"><?= $kpis['total'] ?></div><div class="kpi-lbl">Total inscrits</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--accent)"><?= $kpis['chercheurs'] ?></div><div class="kpi-lbl">Chercheurs</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--accent3)"><?= $kpis['entreprises'] ?></div><div class="kpi-lbl">Entreprises</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--danger)"><?= $kpis['bloques'] ?></div><div class="kpi-lbl">Bloqués</div></div>
</div>

<!-- Filtres -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Liste des utilisateurs</div>
  </div>
  <div class="card-body">
    <div class="filters">
      <input class="search-input" type="text" id="searchInput" placeholder="🔍 Rechercher email, nom..." value="<?= Security::escape($search) ?>">
      <select class="filter-select" id="roleFilter">
        <option value="">Tous les rôles</option>
        <option value="chercheur"  <?= $role==='chercheur' ?'selected':''?>>Chercheurs</option>
        <option value="entreprise" <?= $role==='entreprise'?'selected':''?>>Entreprises</option>
      </select>
      <button class="btn btn-primary" onclick="applyFilters()">Filtrer</button>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Email</th><th>Nom</th><th>Rôle</th><th>Statut</th><th>Abonnement</th><th>Inscrit le</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr id="user-row-<?= $u['id'] ?>">
          <td><?= Security::escape($u['email']) ?></td>
          <td><?= Security::escape(trim($u['prenom'] . ' ' . ($u['nom'] ?: $u['entreprise_nom']))) ?: '—' ?></td>
          <td>
            <?php if($u['role']==='chercheur'): ?>
              <span class="badge badge-accent">Chercheur</span>
            <?php elseif($u['role']==='entreprise'): ?>
              <span class="badge" style="background:rgba(124,58,237,.15);color:var(--accent3)">Entreprise</span>
            <?php else: ?>
              <span class="badge badge-warning">Admin</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($u['is_active']): ?>
              <span class="badge badge-success">Actif</span>
            <?php else: ?>
              <span class="badge badge-danger">Bloqué</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($u['abo_fin']): ?>
              <span class="badge badge-success">✓ <?= date('d/m/Y', strtotime($u['abo_fin'])) ?></span>
            <?php else: ?>
              <span style="color:var(--muted);font-size:0.75rem">Aucun</span>
            <?php endif; ?>
          </td>
          <td style="color:var(--muted);font-size:0.75rem"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <?php if($u['role'] !== 'admin'): ?>
                <?php if($u['is_active']): ?>
                  <button class="btn btn-warning btn-sm" onclick="userAction(<?= $u['id'] ?>,'bloquer')">🔒 Bloquer</button>
                <?php else: ?>
                  <button class="btn btn-ghost btn-sm" onclick="userAction(<?= $u['id'] ?>,'debloquer')">🔓 Débloquer</button>
                <?php endif; ?>
                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $u['id'] ?>)">🗑</button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($users)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:32px">Aucun utilisateur trouvé</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if($pages > 1): ?>
    <div style="display:flex;gap:6px;justify-content:center;margin-top:20px;flex-wrap:wrap">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="?page=<?=$i?>&q=<?=urlencode($search)?>&role=<?=urlencode($role)?>"
           class="btn <?= $i==$page?'btn-primary':'btn-ghost' ?> btn-sm"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal confirmation suppression -->
<div class="modal-overlay" id="modalDelete">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalDelete')">✕</button>
    <div class="modal-title">⚠️ Supprimer cet utilisateur ?</div>
    <p style="color:var(--muted);font-size:0.85rem;margin-bottom:20px">Cette action est irréversible. Toutes ses données seront supprimées.</p>
    <div style="display:flex;gap:10px">
      <button class="btn btn-danger" id="btnConfirmDelete">Supprimer</button>
      <button class="btn btn-ghost" onclick="closeModal('modalDelete')">Annuler</button>
    </div>
  </div>
</div>

<script>
function applyFilters(){
  const q    = document.getElementById('searchInput').value;
  const role = document.getElementById('roleFilter').value;
  window.location = '<?= BASE_URL ?>/admin/users?q='+encodeURIComponent(q)+'&role='+role;
}
document.getElementById('searchInput').addEventListener('keydown', e => { if(e.key==='Enter') applyFilters(); });

async function userAction(id, action){
  const fd = new FormData();
  fd.append('user_id', id);
  fd.append('action', action);
  fd.append('_csrf_token', '<?= \Staff\Core\Security::csrfToken() ?>');
  const res  = await fetch('<?= BASE_URL ?>/admin/users/action', {method:'POST', body:fd});
  const data = await res.json();
  showToast(data.msg, data.ok ? 'ok' : 'err');
  if(data.ok) setTimeout(() => location.reload(), 1000);
}

let deleteId = null;
function confirmDelete(id){
  deleteId = id;
  openModal('modalDelete');
  document.getElementById('btnConfirmDelete').onclick = () => userAction(id, 'supprimer');
}
</script>
