<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Inscription</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap');
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:#0a0e1a;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
.card{background:#111827;border:1px solid #1e2d45;border-radius:16px;padding:40px;width:100%;max-width:440px;}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:#00d4aa;letter-spacing:-0.04em;margin-bottom:4px;}
.subtitle{color:#64748b;font-size:0.83rem;margin-bottom:28px;}
label{display:block;font-size:0.78rem;color:#64748b;margin-bottom:6px;font-weight:500;}
input[type=text],input[type=email],input[type=tel],input[type=password]{
  width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;
  padding:11px 14px;color:#e2e8f0;font-family:'DM Sans',sans-serif;
  font-size:0.9rem;outline:none;transition:border-color 0.2s;margin-bottom:16px;}
input:focus{border-color:#00d4aa;}

/* Choix rôle */
.role-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px;}
.role-btn{border:2px solid #1e2d45;background:transparent;border-radius:10px;padding:14px 10px;
  cursor:pointer;color:#64748b;font-family:'DM Sans',sans-serif;font-size:0.85rem;
  font-weight:500;transition:all 0.2s;display:flex;flex-direction:column;align-items:center;gap:6px;}
.role-btn .icon{font-size:1.5rem;}
.role-btn.selected{border-color:#00d4aa;background:rgba(0,212,170,.08);color:#00d4aa;}

.row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.btn{width:100%;background:#00d4aa;color:#0a0e1a;border:none;border-radius:8px;padding:12px;
  font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;cursor:pointer;
  transition:opacity 0.2s;margin-top:4px;}
.btn:hover{opacity:0.88;}
.error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;
  border-radius:8px;padding:10px 14px;font-size:0.83rem;margin-bottom:16px;}
.prenom-nom{display:none;}
.prenom-nom.visible{display:block;}
.login-link{text-align:center;margin-top:16px;font-size:0.82rem;color:#64748b;}
.login-link a{color:#00d4aa;text-decoration:none;}

/* Ebook badge */
.ebook-badge{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);
  border-radius:8px;padding:10px 14px;font-size:0.8rem;color:#f59e0b;
  margin-bottom:18px;text-align:center;}
</style>
</head>
<body>
<div class="card">
  <div class="logo">STAFF</div>
  <div class="subtitle">Créer votre compte</div>

  <div class="ebook-badge">🎁 <strong>Livre numérique offert</strong> dès l'inscription</div>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>/register">

    <!-- Choix rôle -->
    <label>Type de compte</label>
    <div class="role-grid">
      <button type="button" class="role-btn <?= ($_POST['role'] ?? 'chercheur') === 'chercheur' ? 'selected' : '' ?>"
              data-role="chercheur" onclick="selectRole(this)">
        <span class="icon">🎓</span> Chercheur d'emploi
      </button>
      <button type="button" class="role-btn <?= ($_POST['role'] ?? '') === 'entreprise' ? 'selected' : '' ?>"
              data-role="entreprise" onclick="selectRole(this)">
        <span class="icon">🏢</span> Entreprise
      </button>
    </div>
    <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($_POST['role'] ?? 'chercheur') ?>">

    <!-- Nom / Prénom (chercheur) ou Nom entreprise -->
    <div id="champsNom">
      <div class="row" id="rangPrenom">
        <div>
          <label>Prénom *</label>
          <input type="text" name="prenom" placeholder="Jean" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
        </div>
        <div>
          <label>Nom *</label>
          <input type="text" name="nom" placeholder="Dupont" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        </div>
      </div>
      <div id="rangEntreprise" style="display:none">
        <label>Nom de l'entreprise *</label>
        <input type="text" name="nom_entreprise" placeholder="Ma Société SARL" value="<?= htmlspecialchars($_POST['nom_entreprise'] ?? '') ?>">
      </div>
    </div>

    <label>Adresse email *</label>
    <input type="email" name="email" placeholder="vous@email.com"
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

    <label>Téléphone *</label>
    <input type="tel" name="phone" placeholder="6XX XXX XXX"
           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>

    <label>Mot de passe *</label>
    <input type="password" name="password" placeholder="Minimum 6 caractères" required>

    <label>Confirmer le mot de passe *</label>
    <input type="password" name="confirm" placeholder="••••••••" required>

    <button class="btn" type="submit">Créer mon compte →</button>
  </form>

  <div class="login-link">
    Déjà un compte ? <a href="<?= BASE_URL ?>/login">Se connecter</a>
  </div>
</div>

<script>
function selectRole(el) {
  document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
  const role = el.dataset.role;
  document.getElementById('roleInput').value = role;

  if (role === 'entreprise') {
    document.getElementById('rangPrenom').style.display    = 'none';
    document.getElementById('rangEntreprise').style.display = 'block';
  } else {
    document.getElementById('rangPrenom').style.display    = 'grid';
    document.getElementById('rangEntreprise').style.display = 'none';
  }
}

// Init au chargement selon valeur POST
const roleActuel = document.getElementById('roleInput').value;
if (roleActuel === 'entreprise') {
  document.getElementById('rangPrenom').style.display    = 'none';
  document.getElementById('rangEntreprise').style.display = 'block';
}
</script>
</body>
</html>
