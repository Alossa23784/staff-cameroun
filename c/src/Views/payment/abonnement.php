<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Abonnement</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap');
:root{--bg:#0a0e1a;--surface:#111827;--border:#1e2d45;--accent:#00d4aa;--accent2:#ff6b35;--muted:#64748b;--text:#e2e8f0;--success:#10b981;--warning:#f59e0b;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
.wrap{width:100%;max-width:460px;}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:var(--accent);margin-bottom:24px;text-align:center;letter-spacing:-0.04em;}
.status-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px 20px;margin-bottom:20px;display:flex;align-items:center;gap:14px;}
.status-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
.status-active{background:var(--success);box-shadow:0 0 8px var(--success);}
.status-inactive{background:var(--muted);}
.status-label{font-size:0.82rem;color:var(--muted);}
.status-value{font-weight:500;font-size:0.9rem;}
.offer-card{background:linear-gradient(135deg,#00d4aa15,#7c3aed15);border:1px solid rgba(0,212,170,.3);border-radius:16px;padding:28px 24px;text-align:center;margin-bottom:24px;}
.offer-price{font-family:'Syne',sans-serif;font-weight:800;font-size:2.8rem;color:var(--accent);letter-spacing:-0.05em;}
.offer-currency{font-size:1.1rem;font-weight:600;vertical-align:super;}
.offer-period{color:var(--muted);font-size:0.85rem;margin-top:4px;}
.offer-features{list-style:none;margin-top:18px;text-align:left;display:inline-flex;flex-direction:column;gap:8px;}
.offer-features li{font-size:0.85rem;display:flex;align-items:center;gap:8px;}
.offer-features li::before{content:'✓';color:var(--accent);font-weight:700;}
.form-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px 24px;}
.form-title{font-family:'Syne',sans-serif;font-weight:700;font-size:1.05rem;margin-bottom:8px;}
.form-sub{color:var(--muted);font-size:0.82rem;margin-bottom:20px;}
label{display:block;font-size:0.78rem;color:var(--muted);margin-bottom:6px;}
input[type=tel]{width:100%;background:#0a0e1a;border:1px solid var(--border);border-radius:8px;padding:11px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.2s;margin-bottom:20px;}
input[type=tel]:focus{border-color:var(--accent);}
.btn-pay{width:100%;background:var(--accent);color:#0a0e1a;border:none;border-radius:10px;padding:14px;font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-pay:hover{opacity:0.88;}
.operators{display:flex;gap:8px;justify-content:center;margin-bottom:18px;}
.op-badge{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:6px 14px;font-size:0.78rem;color:var(--muted);}
.dates-info{background:rgba(0,212,170,.05);border:1px solid rgba(0,212,170,.15);border-radius:8px;padding:12px 14px;font-size:0.82rem;color:var(--muted);margin-bottom:16px;}
.dates-info strong{color:var(--accent);}
.error-msg{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;border-radius:8px;padding:10px 14px;font-size:0.83rem;margin-bottom:16px;}
.back{display:block;text-align:center;margin-top:16px;color:var(--muted);font-size:0.82rem;text-decoration:none;}
.back:hover{color:var(--accent);}
</style>
</head>
<body>
<div class="wrap">
  <div class="logo">STAFF</div>

  <!-- Statut -->
  <div class="status-card">
    <div class="status-dot <?= $actif ? 'status-active' : 'status-inactive' ?>"></div>
    <div>
      <div class="status-label">Statut abonnement</div>
      <div class="status-value">
        <?php if ($actif && $dateFin): ?>
          Actif — expire le <?= $dateFin->format('d/m/Y') ?>
        <?php else: ?>
          Aucun abonnement actif
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Offre -->
  <div class="offer-card">
    <div class="offer-price"><span class="offer-currency">FCFA</span> <?= number_format(ABONNEMENT_PRIX, 0, ',', ' ') ?></div>
    <div class="offer-period">par an · renouvelable · cumulable</div>
    <ul class="offer-features">
      <li>Candidatures illimitées</li>
      <li>Accès à toutes les offres</li>
      <li>Cumul possible (paiement anticipé)</li>
      <li>Livre numérique offert</li>
    </ul>
  </div>

  <!-- Formulaire -->
  <div class="form-card">
    <div class="form-title">Payer par Mobile Money</div>
    <div class="form-sub">Vous serez redirigé vers la page de paiement sécurisée Monetbil</div>

    <!-- Opérateurs acceptés -->
    <div class="operators">
      <span class="op-badge">🟡 MTN MoMo</span>
      <span class="op-badge">🟠 Orange Money</span>
    </div>

    <!-- Dates -->
    <div class="dates-info">
      Période : <strong><?= $dates['debut'] ?></strong> → <strong><?= $dates['fin'] ?></strong>
      <?php if ($actif): ?><span style="color:var(--warning)"> (cumul)</span><?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['pay_error'])): ?>
      <div class="error-msg"><?= htmlspecialchars($_SESSION['pay_error']) ?></div>
      <?php unset($_SESSION['pay_error']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/abonnement/initier">
      <label>Numéro Mobile Money *</label>
      <input type="tel" name="telephone"
             placeholder="6XX XXX XXX"
             value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
             maxlength="9" required>

      <button class="btn-pay" type="submit">
        🔒 Payer <?= number_format(ABONNEMENT_PRIX, 0, ',', ' ') ?> FCFA →
      </button>
    </form>

    <a href="<?= BASE_URL ?>/chercheur/dashboard" class="back">← Retour au dashboard</a>
  </div>
</div>
</body>
</html>
