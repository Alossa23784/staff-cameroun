<?php use Staff\Core\Security; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Parrainage — STAFF</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0a0e1a;color:#e2e8f0;min-height:100vh}
.topbar{background:#111827;border-bottom:1px solid #1e2d45;padding:0 5%;height:60px;display:flex;align-items:center;justify-content:space-between}
.brand{font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;color:#00d4aa;text-decoration:none}
.back-link{color:#64748b;text-decoration:none;font-size:.85rem;transition:color .2s}
.back-link:hover{color:#00d4aa}
.container{max-width:900px;margin:0 auto;padding:36px 5%}
.page-title{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;margin-bottom:6px}
.page-sub{color:#64748b;font-size:.9rem;margin-bottom:32px}
.flash{padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.875rem;font-weight:500}
.flash.success{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:#00d4aa}
.flash.error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444}
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px}
.kpi{background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:20px}
.kpi-val{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:#00d4aa}
.kpi-lbl{color:#64748b;font-size:.78rem;margin-top:4px}
.card{background:#111827;border:1px solid #1e2d45;border-radius:14px;padding:24px;margin-bottom:20px}
.card-title{font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:16px;color:#e2e8f0}
.code-box{background:#0a0e1a;border:1px solid #1e2d45;border-radius:10px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px}
.code-val{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:#00d4aa;letter-spacing:.1em}
.copy-btn{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.25);color:#00d4aa;padding:8px 16px;border-radius:8px;cursor:pointer;font-size:.8rem;font-weight:600;transition:all .2s}
.copy-btn:hover{background:rgba(0,212,170,.2)}
.lien-box{background:#0a0e1a;border:1px dashed #1e2d45;border-radius:8px;padding:12px 16px;font-size:.8rem;color:#64748b;word-break:break-all;margin-bottom:8px}
.steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:0}
.step{text-align:center;padding:16px}
.step-num{width:40px;height:40px;border-radius:50%;background:rgba(0,212,170,.1);border:2px solid #00d4aa;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:800;color:#00d4aa;margin:0 auto 10px;font-size:.95rem}
.step-title{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;margin-bottom:6px}
.step-desc{color:#64748b;font-size:.78rem;line-height:1.5}
.form-group{margin-bottom:14px}
.form-label{display:block;font-size:.78rem;color:#94a3b8;margin-bottom:5px;font-weight:500}
.form-input{width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#e2e8f0;font-family:'DM Sans',sans-serif;font-size:.875rem;transition:border .2s}
.form-input:focus{outline:none;border-color:#00d4aa}
.btn-primary{background:#00d4aa;color:#0a0e1a;border:none;padding:11px 24px;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.875rem;font-weight:700;cursor:pointer;transition:all .2s}
.btn-primary:hover{background:#00b894;transform:translateY(-1px)}
table{width:100%;border-collapse:collapse}
th{text-align:left;font-size:.72rem;color:#64748b;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #1e2d45}
td{padding:12px;border-bottom:1px solid #1e2d45;font-size:.85rem}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700}
.badge-success{background:rgba(0,212,170,.12);color:#00d4aa}
.badge-warning{background:rgba(251,191,36,.12);color:#fbbf24}
.empty{text-align:center;padding:32px;color:#64748b;font-size:.875rem}
</style>
</head>
<body>
<div class="topbar">
  <a href="<?= BASE_URL ?>/chercheur/dashboard" class="brand">STAFF</a>
  <a href="<?= BASE_URL ?>/chercheur/dashboard" class="back-link">← Retour au dashboard</a>
</div>
<div class="container">
  <div class="page-title">🔗 Mon Parrainage</div>
  <div class="page-sub">Invitez vos amis et gagnez 500 FCFA par abonnement souscrit</div>

  <?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="flash <?= $f['type'] ?>"><?= Security::escape($f['msg']) ?></div>
  <?php endif; ?>

  <!-- KPIs -->
  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-val"><?= (int)($statsData['total_filleuls'] ?? 0) ?></div>
      <div class="kpi-lbl">Filleuls inscrits</div>
    </div>
    <div class="kpi">
      <div class="kpi-val"><?= number_format((int)($statsData['total_commissions'] ?? 0)) ?> F</div>
      <div class="kpi-lbl">Commissions totales</div>
    </div>
    <div class="kpi">
      <div class="kpi-val" style="color:#fbbf24"><?= number_format((int)($statsData['commissions_attente'] ?? 0)) ?> F</div>
      <div class="kpi-lbl">En attente</div>
    </div>
    <div class="kpi">
      <div class="kpi-val" style="color:#22c55e"><?= number_format((int)($statsData['commissions_recues'] ?? 0)) ?> F</div>
      <div class="kpi-lbl">Déjà reçu</div>
    </div>
  </div>

  <!-- Code & Lien -->
  <div class="card">
    <div class="card-title">🎫 Votre code de parrainage</div>
    <div class="code-box">
      <span class="code-val"><?= Security::escape($codeParrain) ?></span>
      <button class="copy-btn" onclick="copyCode('<?= Security::escape($codeParrain) ?>')">📋 Copier</button>
    </div>
    <div style="font-size:.78rem;color:#64748b;margin-bottom:16px">Partagez ce lien à vos amis :</div>
    <div class="code-box" style="padding:12px 16px">
      <span class="lien-box" style="background:transparent;border:none;padding:0;margin:0;flex:1"><?= Security::escape($lienParrainage) ?></span>
      <button class="copy-btn" onclick="copyLien('<?= Security::escape($lienParrainage) ?>')">📋 Copier</button>
    </div>
  </div>

  <!-- Comment ça marche -->
  <div class="card">
    <div class="card-title">💡 Comment ça marche ?</div>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-title">Partagez votre lien</div>
        <div class="step-desc">Envoyez votre lien ou code à vos amis par WhatsApp, Facebook ou SMS.</div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-title">Ils s'inscrivent</div>
        <div class="step-desc">Votre ami s'inscrit via votre lien et souscrit un abonnement à 3 500 FCFA.</div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-title">Vous recevez 500 FCFA</div>
        <div class="step-desc">500 FCFA sont crédités automatiquement sur votre Mobile Money.</div>
      </div>
    </div>
  </div>

  <!-- Numéro MoMo -->
  <div class="card">
    <div class="card-title">📱 Votre numéro Mobile Money</div>
    <form method="POST" action="<?= BASE_URL ?>/chercheur/parrainage/momo">
      <?= Security::csrfField() ?>
      <div class="form-group">
        <label class="form-label">Numéro MTN MoMo ou Orange Money (format : 6XXXXXXXX)</label>
        <input class="form-input" type="text" name="telephone_momo"
               value="<?= Security::escape($telephoneMomo) ?>"
               placeholder="ex: 677123456" maxlength="9">
      </div>
      <button type="submit" class="btn-primary">💾 Enregistrer</button>
    </form>
  </div>

  <!-- Liste filleuls -->
  <div class="card">
    <div class="card-title">👥 Mes filleuls (<?= count($filleulsList) ?>)</div>
    <?php if (!empty($filleulsList)): ?>
    <table>
      <thead>
        <tr>
          <th>Email</th>
          <th>Date</th>
          <th>Commission</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($filleulsList as $f): ?>
        <tr>
          <td><?= Security::escape($f['filleul_email']) ?></td>
          <td><?= date('d/m/Y', strtotime($f['created_at'])) ?></td>
          <td><?= number_format((int)$f['commission']) ?> FCFA</td>
          <td>
            <?php if ($f['statut'] === 'payee'): ?>
              <span class="badge badge-success">✓ Payée</span>
            <?php else: ?>
              <span class="badge badge-warning">⏳ En attente</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty">Aucun filleul pour l'instant. Partagez votre lien !</div>
    <?php endif; ?>
  </div>
</div>

<script>
function copyCode(code) {
  navigator.clipboard.writeText(code).then(() => alert('Code copié : ' + code));
}
function copyLien(lien) {
  navigator.clipboard.writeText(lien).then(() => alert('Lien copié !'));
}
</script>
</body>
</html>
