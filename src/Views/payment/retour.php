<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>STAFF — Résultat paiement</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap');
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:#0a0e1a;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
.card{background:#111827;border:1px solid #1e2d45;border-radius:16px;padding:44px 36px;width:100%;max-width:400px;text-align:center;}
.icon{font-size:3.5rem;margin-bottom:16px;}
.title{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;letter-spacing:-0.03em;margin-bottom:10px;}
.sub{color:#64748b;font-size:0.88rem;margin-bottom:28px;line-height:1.6;}
.btn{display:inline-block;background:#00d4aa;color:#0a0e1a;padding:12px 28px;border-radius:10px;font-family:'Syne',sans-serif;font-weight:700;text-decoration:none;transition:opacity 0.2s;}
.btn:hover{opacity:0.88;}
.btn-ghost{display:inline-block;color:#64748b;padding:12px 28px;font-size:0.85rem;text-decoration:none;margin-top:8px;}
</style>
</head>
<body>
<div class="card">
  <?php if ($success): ?>
    <div class="icon">🎉</div>
    <div class="title" style="color:#00d4aa">Paiement réussi !</div>
    <div class="sub">
      Votre abonnement STAFF a été activé avec succès.<br>
      Vous pouvez maintenant postuler à toutes les offres.
    </div>
    <a href="<?= BASE_URL ?>/chercheur/dashboard" class="btn">Accéder à mon espace →</a>
  <?php else: ?>
    <div class="icon">❌</div>
    <div class="title" style="color:#ef4444">Paiement échoué</div>
    <div class="sub">
      Le paiement n'a pas pu être effectué.<br>
      Vérifiez votre solde et réessayez.
    </div>
    <a href="<?= BASE_URL ?>/abonnement" class="btn">Réessayer →</a>
    <br>
    <a href="<?= BASE_URL ?>/chercheur/dashboard" class="btn-ghost">Retour au dashboard</a>
  <?php endif; ?>
</div>
</body>
</html>
