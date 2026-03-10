<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Nouveau mot de passe</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap');
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:#0a0e1a;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
.card{background:#111827;border:1px solid #1e2d45;border-radius:16px;padding:40px;width:100%;max-width:400px;}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:#00d4aa;letter-spacing:-0.04em;margin-bottom:6px;}
.subtitle{color:#64748b;font-size:0.83rem;margin-bottom:28px;}
label{display:block;font-size:0.78rem;color:#64748b;margin-bottom:6px;}
input[type=password]{width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:11px 14px;color:#e2e8f0;font-family:'DM Sans',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.2s;margin-bottom:18px;}
input:focus{border-color:#00d4aa;}
.btn{width:100%;background:#00d4aa;color:#0a0e1a;border:none;border-radius:8px;padding:12px;font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;cursor:pointer;transition:opacity 0.2s;}
.btn:hover{opacity:0.88;}
.msg-ok{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#10b981;border-radius:8px;padding:10px 14px;font-size:0.83rem;margin-bottom:16px;}
.msg-err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;border-radius:8px;padding:10px 14px;font-size:0.83rem;margin-bottom:16px;}
.back{display:block;text-align:center;margin-top:16px;color:#64748b;font-size:0.82rem;text-decoration:none;}
.back:hover{color:#00d4aa;}
</style>
</head>
<body>
<div class="card">
  <div class="logo">STAFF</div>
  <div class="subtitle">Définir un nouveau mot de passe</div>

  <?php if (!empty($error)):   ?><div class="msg-err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if (!empty($success)): ?>
    <div class="msg-ok">✅ Mot de passe modifié avec succès !</div>
    <a href="<?= BASE_URL ?>/login" class="btn" style="display:block;text-align:center;text-decoration:none;margin-top:8px">Se connecter →</a>
  <?php elseif (!empty($valid)): ?>
  <form method="POST" action="<?= BASE_URL ?>/reset-password">
    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
    <label>Nouveau mot de passe</label>
    <input type="password" name="password" placeholder="••••••••" required minlength="6">
    <label>Confirmer le mot de passe</label>
    <input type="password" name="confirm" placeholder="••••••••" required>
    <button class="btn" type="submit">Enregistrer →</button>
  </form>
  <?php else: ?>
    <div class="msg-err">Lien invalide ou expiré. Veuillez refaire une demande.</div>
    <a href="<?= BASE_URL ?>/forgot-password" class="btn" style="display:block;text-align:center;text-decoration:none;margin-top:8px">Refaire une demande</a>
  <?php endif; ?>

  <a href="<?= BASE_URL ?>/login" class="back">← Retour à la connexion</a>
</div>
</body>
</html>
