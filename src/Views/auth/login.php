<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Connexion</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap');

* { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family: 'DM Sans', sans-serif;
  background: #0a0e1a;
  color: #e2e8f0;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card {
  background: #111827;
  border: 1px solid #1e2d45;
  border-radius: 16px;
  padding: 44px 40px;
  width: 100%;
  max-width: 400px;
}
.logo {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 2rem;
  color: #00d4aa;
  letter-spacing: -0.04em;
  margin-bottom: 4px;
}
.subtitle { color: #64748b; font-size: 0.85rem; margin-bottom: 32px; }

label { display:block; font-size:0.8rem; color:#64748b; margin-bottom:6px; font-weight:500; }
input[type=email], input[type=password] {
  width: 100%;
  background: #0a0e1a;
  border: 1px solid #1e2d45;
  border-radius: 8px;
  padding: 11px 14px;
  color: #e2e8f0;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s;
  margin-bottom: 18px;
}
input:focus { border-color: #00d4aa; }
.btn {
  width: 100%;
  background: #00d4aa;
  color: #0a0e1a;
  border: none;
  border-radius: 8px;
  padding: 12px;
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  letter-spacing: 0.02em;
  transition: opacity 0.2s;
  margin-top: 4px;
}
.btn:hover { opacity: 0.88; }
.error {
  background: rgba(239,68,68,.1);
  border: 1px solid rgba(239,68,68,.3);
  color: #ef4444;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.83rem;
  margin-bottom: 18px;
}
</style>
</head>
<body>
<div class="card">
  <div class="logo">STAFF</div>
  <div class="subtitle">Espace Administration</div>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>/login">
    <label>Adresse email</label>
    <input type="email" name="email" placeholder="admin@staff.cm"
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

    <label>Mot de passe</label>
    <input type="password" name="password" placeholder="••••••••" required>

    <button class="btn" type="submit">Se connecter →</button>
  </form>
</div>
</body>
</html>
