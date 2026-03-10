<div class="page-header">
  <div class="page-title">🔗 Parrainages</div>
  <div class="page-sub">Gestion des commissions et filleuls</div>
</div>

<div class="kpi-grid">
  <div class="kpi-card"><div class="kpi-val"><?= $stats['total'] ?></div><div class="kpi-lbl">Total parrainages</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--accent)"><?= number_format((float)$stats['total_commissions'],0,',',' ') ?> <span style="font-size:1rem">FCFA</span></div><div class="kpi-lbl">Commissions totales</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--success)"><?= number_format((float)$stats['total_paye'],0,',',' ') ?> <span style="font-size:1rem">FCFA</span></div><div class="kpi-lbl">Déjà payé</div></div>
  <div class="kpi-card"><div class="kpi-val" style="color:var(--warning)"><?= number_format((float)$stats['en_attente'],0,',',' ') ?> <span style="font-size:1rem">FCFA</span></div><div class="kpi-lbl">En attente paiement</div></div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Liste des parrainages</div></div>
  <div class="card-body">
    <div class="filters">
      <select class="filter-select" id="statutFilter" onchange="applyFilters()">
        <option value="">Tous statuts</option>
        <option value="en_attente" <?= $statut==='en_attente'?'selected':''?>>En attente</option>
        <option value="valide"     <?= $statut==='valide'    ?'selected':''?>>Validés</option>
        <option value="paye"       <?= $statut==='paye'      ?'selected':''?>>Payés</option>
      </select>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Parrain</th><th>Filleul</th><th>Commission</th><th>MoMo parrain</th><th>Statut</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach($parrainages as $p): ?>
        <tr>
          <td>
            <div style="font-size:0.8rem"><?= Security::escape($p['parrain_email']) ?></div>
            <div style="color:var(--muted);font-size:0.72rem"><?= Security::escape(trim($p['parrain_prenom'].' '.$p['parrain_nom'])) ?></div>
          </td>
          <td style="font-size:0.8rem;color:var(--muted)"><?= Security::escape($p['filleul_email']) ?></td>
          <td style="color:var(--warning);font-weight:600"><?= number_format((float)$p['commission'],0,',',' ') ?> FCFA</td>
          <td style="font-family:monospace;font-size:0.78rem"><?= Security::escape($p['telephone_momo'] ?? '—') ?></td>
          <td>
            <?php if($p['statut']==='paye'): ?>
              <span class="badge badge-accent">✓ Payé</span>
            <?php elseif($p['statut']==='valide'): ?>
              <span class="badge badge-success">Validé</span>
            <?php else: ?>
              <span class="badge badge-muted">En attente</span>
            <?php endif; ?>
          </td>
          <td style="color:var(--muted);font-size:0.75rem"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($parrainages)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:32px">Aucun parrainage trouvé</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($pages > 1): ?>
    <div style="display:flex;gap:6px;justify-content:center;margin-top:20px;flex-wrap:wrap">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="?page=<?=$i?>&statut=<?=urlencode($statut)?>" class="btn <?= $i==$page?'btn-primary':'btn-ghost' ?> btn-sm"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<script>
function applyFilters(){ window.location='<?= BASE_URL ?>/admin/parrainages?statut='+document.getElementById('statutFilter').value; }
</script>
