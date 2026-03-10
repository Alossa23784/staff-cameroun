<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
  <div>
    <div class="page-title">📢 Publicités</div>
    <div class="page-sub">Gérez les espaces publicitaires du site</div>
  </div>
  <button class="btn btn-primary" onclick="openModal('modalAjout')">➕ Ajouter une pub</button>
</div>

<!-- Positions disponibles -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:24px">
  <?php
  $positions = ['vitrine_banner'=>'Vitrine — Bannière', 'register_banner'=>'Page inscription', 'app_banner'=>'Accueil app'];
  foreach($positions as $key=>$label):
    $count = count(array_filter($pubs, fn($p) => $p['position']===$key && $p['is_active']));
  ?>
  <div class="card" style="margin:0">
    <div class="card-body" style="padding:14px">
      <div style="font-size:0.75rem;color:var(--muted)"><?= $label ?></div>
      <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.4rem;margin-top:4px;color:<?= $count?'var(--success)':'var(--muted)' ?>"><?= $count ?></div>
      <div style="font-size:0.7rem;color:var(--muted)">active<?= $count>1?'s':'' ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Toutes les publicités</div></div>
  <div class="card-body">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Titre</th><th>Position</th><th>Lien</th><th>Statut</th><th>Ajouté le</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($pubs as $p): ?>
        <tr id="pub-row-<?= $p['id'] ?>">
          <td><?= Security::escape($p['titre']) ?></td>
          <td><span class="badge badge-muted"><?= Security::escape($p['position']) ?></span></td>
          <td style="font-size:0.75rem;color:var(--muted);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= $p['lien_url'] ? '<a href="'.Security::escape($p['lien_url']).'" target="_blank" style="color:var(--accent)">'.Security::escape($p['lien_url']).'</a>' : '—' ?>
          </td>
          <td>
            <?php if($p['is_active']): ?>
              <span class="badge badge-success">Active</span>
            <?php else: ?>
              <span class="badge badge-muted">Inactive</span>
            <?php endif; ?>
          </td>
          <td style="color:var(--muted);font-size:0.75rem"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <button class="btn btn-ghost btn-sm" onclick="pubAction(<?= $p['id'] ?>,'toggle')"><?= $p['is_active']?'⏸ Désactiver':'▶ Activer' ?></button>
              <button class="btn btn-danger btn-sm" onclick="pubAction(<?= $p['id'] ?>,'supprimer')">🗑</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($pubs)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:32px">Aucune publicité. Cliquez sur "Ajouter" pour commencer.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal ajout -->
<div class="modal-overlay" id="modalAjout">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalAjout')">✕</button>
    <div class="modal-title">➕ Nouvelle publicité</div>
    <div class="form-group">
      <label class="form-label">Titre *</label>
      <input class="form-input" type="text" id="pubTitre" placeholder="Ex: Promo Janvier">
    </div>
    <div class="form-group">
      <label class="form-label">Position</label>
      <select class="form-select" id="pubPosition">
        <option value="vitrine_banner">Vitrine — Bannière</option>
        <option value="register_banner">Page inscription</option>
        <option value="app_banner">Accueil app</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">URL de l'image</label>
      <input class="form-input" type="url" id="pubImage" placeholder="https://...">
    </div>
    <div class="form-group">
      <label class="form-label">Lien de destination</label>
      <input class="form-input" type="url" id="pubLien" placeholder="https://...">
    </div>
    <div style="display:flex;gap:10px;margin-top:20px">
      <button class="btn btn-primary" onclick="ajouterPub()">Ajouter</button>
      <button class="btn btn-ghost" onclick="closeModal('modalAjout')">Annuler</button>
    </div>
  </div>
</div>

<script>
async function pubAction(id, action){
  const fd=new FormData();
  fd.append('pub_id',id); fd.append('action',action);
  fd.append('_csrf_token','<?= \Staff\Core\Security::csrfToken() ?>');
  const res=await fetch('<?= BASE_URL ?>/admin/publicites/action',{method:'POST',body:fd});
  const data=await res.json();
  showToast(data.msg,data.ok?'ok':'err');
  if(data.ok) setTimeout(()=>location.reload(),1000);
}
async function ajouterPub(){
  const fd=new FormData();
  fd.append('action','ajouter');
  fd.append('titre',    document.getElementById('pubTitre').value);
  fd.append('position', document.getElementById('pubPosition').value);
  fd.append('image_url',document.getElementById('pubImage').value);
  fd.append('lien_url', document.getElementById('pubLien').value);
  fd.append('_csrf_token','<?= \Staff\Core\Security::csrfToken() ?>');
  const res=await fetch('<?= BASE_URL ?>/admin/publicites/action',{method:'POST',body:fd});
  const data=await res.json();
  showToast(data.msg,data.ok?'ok':'err');
  if(data.ok){ closeModal('modalAjout'); setTimeout(()=>location.reload(),1000); }
}
</script>
