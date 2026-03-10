<div class="page-header">
  <div class="page-title">💳 Abonnements</div>
  <div class="page-sub">Total : <strong><?= $total ?></strong> abonnements</div>
</div>

<?php
$kpis = $db->query("SELECT
  COUNT(*) AS total,
  SUM(statut='actif') AS actifs,
  SUM(statut='expire') AS expires,
  SUM(statut='annule') AS annules
FROM abonnements")->fetch();
$revenus = $db->query("SELECT COALESCE(SUM(montant),0) AS total FROM paiements WHERE statut='succes'")->fetchColumn();
?>
<div class="kpi-grid">
  <div class="kpi-card"><div class="kpi-val" style="color:var(--accent)"><?= number_format((float)$revenus,0,',',' ') ?> <span style="font-size:1rem">FCFA</span></div><div class="kpi-lbl">Revenus totaux</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--success)"><?= $kpis['actifs'] ?></div><div class="kpi-lbl">Actifs</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--warning)"><?= $kpis['expires'] ?></div><div class="kpi-lbl">Expirés</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--danger)"><?= $kpis['annules'] ?></div><div class="kpi-lbl">Annulés</div></div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Liste des abonnements</div></div>
  <div class="card-body">
    <div class="filters">
      <input class="search-input" type="text" id="searchInput" placeholder="🔍 Rechercher par email..." value="<?= Security::escape($search) ?>">
      <select class="filter-select" id="statutFilter">
        <option value="">Tous statuts</option>
        <option value="actif"   <?= $statut==='actif'  ?'selected':''?>>Actifs</option>
        <option value="expire"  <?= $statut==='expire' ?'selected':''?>>Expirés</option>
        <option value="annule"  <?= $statut==='annule' ?'selected':''?>>Annulés</option>
      </select>
      <button class="btn btn-primary" onclick="applyFilters()">Filtrer</button>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Utilisateur</th><th>Début</th><th>Fin</th><th>Opérateur</th><th>Téléphone</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($abonnements as $a): ?>
        <tr id="abo-row-<?= $a['id'] ?>">
          <td>
            <div><?= Security::escape($a['email']) ?></div>
            <div style="color:var(--muted);font-size:0.73rem"><?= Security::escape(trim($a['prenom'].' '.$a['nom'])) ?></div>
          </td>
          <td style="color:var(--muted);font-size:0.8rem"><?= date('d/m/Y', strtotime($a['debut'])) ?></td>
          <td>
            <?php
              $fin = new DateTime($a['fin']);
              $now = new DateTime();
              $diff = $now->diff($fin);
              $bientot = $a['statut']==='actif' && $diff->days <= 7 && !$diff->invert;
            ?>
            <span <?= $bientot ? 'style="color:var(--warning)"' : '' ?>>
              <?= date('d/m/Y', strtotime($a['fin'])) ?>
              <?= $bientot ? ' ⚠️' : '' ?>
            </span>
          </td>
          <td><?= Security::escape($a['operateur'] ?? '—') ?></td>
          <td style="font-family:monospace;font-size:0.8rem"><?= Security::escape($a['telephone'] ?? '—') ?></td>
          <td>
            <?php if($a['statut']==='actif'): ?>
              <span class="badge badge-success">Actif</span>
            <?php elseif($a['statut']==='expire'): ?>
              <span class="badge badge-warning">Expiré</span>
            <?php else: ?>
              <span class="badge badge-danger">Annulé</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <button class="btn btn-ghost btn-sm" onclick="openProlonger(<?= $a['id'] ?>)">➕ Prolonger</button>
              <?php if($a['statut']==='actif'): ?>
                <button class="btn btn-danger btn-sm" onclick="aboAction(<?= $a['id'] ?>,'annuler')">✕</button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($abonnements)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:32px">Aucun abonnement trouvé</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($pages > 1): ?>
    <div style="display:flex;gap:6px;justify-content:center;margin-top:20px;flex-wrap:wrap">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="?page=<?=$i?>&q=<?=urlencode($search)?>&statut=<?=urlencode($statut)?>" class="btn <?= $i==$page?'btn-primary':'btn-ghost' ?> btn-sm"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal prolonger -->
<div class="modal-overlay" id="modalProlonger">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalProlonger')">✕</button>
    <div class="modal-title">➕ Prolonger l'abonnement</div>
    <input type="hidden" id="prolongerAboId">
    <div class="form-group">
      <label class="form-label">Nombre de jours à ajouter</label>
      <input class="form-input" type="number" id="prolongerJours" value="30" min="1" max="365">
    </div>
    <div style="display:flex;gap:10px;margin-top:16px">
      <button class="btn btn-primary" onclick="confirmerProlonger()">Confirmer</button>
      <button class="btn btn-ghost"   onclick="closeModal('modalProlonger')">Annuler</button>
    </div>
  </div>
</div>

<script>
function applyFilters(){
  window.location='<?= BASE_URL ?>/admin/abonnements?q='+encodeURIComponent(document.getElementById('searchInput').value)+'&statut='+document.getElementById('statutFilter').value;
}
document.getElementById('searchInput').addEventListener('keydown',e=>{if(e.key==='Enter')applyFilters();});

async function aboAction(id,action,jours=null){
  const fd=new FormData();
  fd.append('abo_id',id); fd.append('action',action);
  if(jours) fd.append('jours',jours);
  fd.append('_csrf_token','<?= \Staff\Core\Security::csrfToken() ?>');
  const res=await fetch('<?= BASE_URL ?>/admin/abonnements/action',{method:'POST',body:fd});
  const data=await res.json();
  showToast(data.msg,data.ok?'ok':'err');
  if(data.ok) setTimeout(()=>location.reload(),1000);
}
function openProlonger(id){ document.getElementById('prolongerAboId').value=id; openModal('modalProlonger'); }
function confirmerProlonger(){
  const id=document.getElementById('prolongerAboId').value;
  const j=document.getElementById('prolongerJours').value;
  closeModal('modalProlonger');
  aboAction(id,'prolonger',j);
}
</script>
