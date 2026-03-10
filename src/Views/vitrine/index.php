<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAFF — Stages & Emploi au Cameroun</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap');

:root {
  --bg:#0a0e1a; --surface:#111827; --border:#1e2d45;
  --accent:#00d4aa; --accent2:#ff6b35; --accent3:#7c3aed;
  --text:#e2e8f0; --muted:#64748b;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden;}

/* ── NAVBAR ── */
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 6vw;height:68px;display:flex;align-items:center;justify-content:space-between;background:rgba(10,14,26,.85);backdrop-filter:blur(12px);border-bottom:1px solid rgba(30,45,69,.6);}
.nav-logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;color:var(--accent);letter-spacing:-0.04em;text-decoration:none;}
.nav-links{display:flex;align-items:center;gap:28px;}
.nav-links a{color:var(--muted);text-decoration:none;font-size:0.88rem;font-weight:500;transition:color 0.2s;}
.nav-links a:hover{color:var(--accent);}
.nav-cta{background:var(--accent);color:#0a0e1a!important;padding:8px 20px;border-radius:8px;font-family:'Syne',sans-serif;font-weight:700!important;font-size:0.85rem!important;}
.nav-cta:hover{opacity:0.88;}

/* ── PROMO BANNER (clignotant rouge) ── */
.promo-banner{background:#1a0505;border-bottom:1px solid rgba(239,68,68,.3);padding:10px;text-align:center;margin-top:68px;}
.promo-text{color:#ef4444;font-weight:700;font-size:0.9rem;letter-spacing:0.02em;animation:clignoter 1.7s ease-in-out infinite;}
@keyframes clignoter{0%,100%{opacity:1;}50%{opacity:0;}}

/* ── HERO SLIDER ── */
.hero{position:relative;height:100vh;min-height:580px;overflow:hidden;margin-top:0;}
.slides{display:flex;height:100%;transition:transform 0.8s cubic-bezier(.77,0,.18,1);}
.slide{min-width:100%;height:100%;position:relative;display:flex;align-items:center;justify-content:center;}

.slide-bg{position:absolute;inset:0;background-size:cover;background-position:center;}
.slide:nth-child(1) .slide-bg{background:linear-gradient(135deg,#0a0e1a 0%,#0d1f3c 50%,#0a1628 100%);}
.slide:nth-child(2) .slide-bg{background:linear-gradient(135deg,#0f1a2e 0%,#1a0a2e 50%,#0a1628 100%);}
.slide:nth-child(3) .slide-bg{background:linear-gradient(135deg,#0a1a1a 0%,#0a2e1e 50%,#0a1628 100%);}

.slide-overlay{position:absolute;inset:0;background:rgba(10,14,26,.55);}
.slide-content{position:relative;z-index:2;text-align:center;padding:0 6vw;max-width:800px;}
.slide-tag{display:inline-block;background:rgba(0,212,170,.15);border:1px solid rgba(0,212,170,.3);color:var(--accent);font-size:0.78rem;font-weight:600;padding:5px 14px;border-radius:20px;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:20px;}
.slide-title{font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(2rem,5vw,3.8rem);letter-spacing:-0.04em;line-height:1.1;margin-bottom:16px;}
.slide-title .highlight{color:var(--accent);}
.slide-sub{font-size:1.1rem;color:rgba(226,232,240,.7);max-width:520px;margin:0 auto 32px;}
.slide-btn{display:inline-block;background:var(--accent);color:#0a0e1a;padding:13px 32px;border-radius:10px;font-family:'Syne',sans-serif;font-weight:700;font-size:0.95rem;text-decoration:none;transition:all 0.2s;}
.slide-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,212,170,.3);}

/* Slider controls */
.slider-dots{position:absolute;bottom:28px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:10;}
.dot{width:8px;height:8px;border-radius:50%;background:rgba(226,232,240,.3);cursor:pointer;transition:all 0.3s;}
.dot.active{background:var(--accent);width:24px;border-radius:4px;}
.slider-arrow{position:absolute;top:50%;transform:translateY(-50%);z-index:10;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:var(--text);width:44px;height:44px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;transition:all 0.2s;backdrop-filter:blur(8px);}
.slider-arrow:hover{background:rgba(0,212,170,.2);border-color:var(--accent);}
.arrow-prev{left:24px;}
.arrow-next{right:24px;}

/* Décoration géométrique */
.geo{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;}
.geo-1{width:400px;height:400px;background:rgba(0,212,170,.06);top:-100px;right:-100px;}
.geo-2{width:300px;height:300px;background:rgba(124,58,237,.06);bottom:-50px;left:-50px;}

/* ── PUB BANNER (entre sections) ── */
.pub-section{padding:20px 6vw;background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.pub-inner{max-width:1100px;margin:0 auto;}
.pub-label{font-size:0.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px;}
.pub-slot{background:linear-gradient(90deg,rgba(30,45,69,.8),rgba(20,30,50,.8));border:1px dashed rgba(100,116,139,.3);border-radius:10px;height:90px;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:0.82rem;cursor:pointer;transition:all 0.2s;}
.pub-slot:hover{border-color:var(--accent);color:var(--accent);}
.pub-slot img{width:100%;height:100%;object-fit:cover;border-radius:10px;}

/* ── FONCTIONNALITÉS ── */
.features{padding:80px 6vw;max-width:1200px;margin:0 auto;}
.section-tag{display:inline-block;background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.2);color:var(--accent);font-size:0.72rem;font-weight:600;padding:4px 12px;border-radius:20px;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:14px;}
.section-title{font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.6rem,3vw,2.4rem);letter-spacing:-0.03em;margin-bottom:10px;}
.section-sub{color:var(--muted);font-size:0.95rem;max-width:520px;margin-bottom:48px;}
.feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;}
.feat-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:24px;transition:all 0.2s;}
.feat-card:hover{border-color:rgba(0,212,170,.3);transform:translateY(-3px);}
.feat-icon{font-size:2rem;margin-bottom:14px;}
.feat-title{font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:8px;}
.feat-desc{color:var(--muted);font-size:0.85rem;line-height:1.6;}

/* ── TARIF ── */
.tarif-section{padding:80px 6vw;background:var(--surface);border-top:1px solid var(--border);}
.tarif-inner{max-width:500px;margin:0 auto;text-align:center;}
.tarif-card{background:linear-gradient(135deg,rgba(0,212,170,.08),rgba(124,58,237,.08));border:1px solid rgba(0,212,170,.25);border-radius:20px;padding:40px 32px;margin-top:32px;}
.tarif-price{font-family:'Syne',sans-serif;font-weight:800;font-size:3.5rem;color:var(--accent);letter-spacing:-0.05em;}
.tarif-currency{font-size:1.2rem;vertical-align:super;}
.tarif-period{color:var(--muted);margin-bottom:24px;}
.tarif-list{list-style:none;text-align:left;margin-bottom:28px;display:flex;flex-direction:column;gap:10px;}
.tarif-list li{display:flex;align-items:center;gap:10px;font-size:0.88rem;}
.tarif-list li::before{content:'✓';color:var(--accent);font-weight:700;flex-shrink:0;}
.tarif-btn{display:block;background:var(--accent);color:#0a0e1a;padding:14px;border-radius:10px;font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;text-decoration:none;transition:all 0.2s;}
.tarif-btn:hover{opacity:0.88;transform:translateY(-1px);}

/* ── CONTACT ── */
.contact-section{padding:80px 6vw;max-width:700px;margin:0 auto;text-align:center;}
.contact-cards{display:flex;gap:16px;justify-content:center;margin-top:32px;flex-wrap:wrap;}
.contact-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:22px 28px;display:flex;align-items:center;gap:14px;text-decoration:none;color:var(--text);transition:all 0.2s;min-width:220px;}
.contact-card:hover{border-color:rgba(0,212,170,.3);transform:translateY(-2px);}
.contact-icon{font-size:1.8rem;}
.contact-label{font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.08em;}
.contact-value{font-weight:500;font-size:0.9rem;margin-top:2px;}
/* Placeholder futurs contacts */
.contact-placeholder{color:var(--muted);font-size:0.8rem;margin-top:16px;font-style:italic;}

/* ── FOOTER ── */
footer{background:var(--surface);border-top:1px solid var(--border);padding:28px 6vw;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
.footer-logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;color:var(--accent);}
.footer-text{color:var(--muted);font-size:0.78rem;}
.footer-links{display:flex;gap:20px;}
.footer-links a{color:var(--muted);text-decoration:none;font-size:0.78rem;transition:color 0.2s;}
.footer-links a:hover{color:var(--accent);}

@media(max-width:640px){
  .nav-links{display:none;}
  .kpi-grid,.feat-grid{grid-template-columns:1fr 1fr;}
  .contact-cards{flex-direction:column;align-items:center;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
  <a href="#" class="nav-logo">STAFF</a>
  <div class="nav-links">
    <a href="#fonctionnalites">Fonctionnalités</a>
    <a href="#tarifs">Tarifs</a>
    <a href="#contact">Contact</a>
    <a href="<?= BASE_URL ?>/login">Connexion</a>
    <a href="<?= BASE_URL ?>/register" class="nav-cta">S'inscrire →</a>
  </div>
</nav>

<!-- ── BANNIÈRE PROMOTIONNELLE CLIGNOTANTE ── -->
<div class="promo-banner">
  <span class="promo-text">🎁 livre numérique offert dès l'inscription</span>
</div>

<!-- ── HERO SLIDER ── -->
<section class="hero">
  <div class="slides" id="slides">

    <!-- Slide 1 -->
    <div class="slide">
      <div class="slide-bg"></div>
      <div class="geo geo-1"></div>
      <div class="geo geo-2"></div>
      <div class="slide-overlay"></div>
      <div class="slide-content">
        <div class="slide-tag">Stages / Emploi</div>
        <h1 class="slide-title">
          <span class="highlight">INTÉGRATION</span><br>dans le milieu professionnel
        </h1>
        <p class="slide-sub">Trouvez votre premier emploi ou stage et lancez votre carrière au Cameroun.</p>
        <a href="<?= BASE_URL ?>/register" class="slide-btn">Commencer maintenant →</a>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="slide">
      <div class="slide-bg"></div>
      <div class="geo geo-1"></div>
      <div class="geo geo-2"></div>
      <div class="slide-overlay"></div>
      <div class="slide-content">
        <div class="slide-tag">Stages / Emploi</div>
        <h1 class="slide-title">
          <span class="highlight">OPPORTUNITÉ</span>,<br>trouvez votre place
        </h1>
        <p class="slide-sub">Des centaines d'entreprises vous attendent. Postulez en quelques clics.</p>
        <a href="<?= BASE_URL ?>/register" class="slide-btn">Voir les offres →</a>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="slide">
      <div class="slide-bg"></div>
      <div class="geo geo-1"></div>
      <div class="geo geo-2"></div>
      <div class="slide-overlay"></div>
      <div class="slide-content">
        <div class="slide-tag">Stages / Emploi</div>
        <h1 class="slide-title">
          <span class="highlight">DÉVOILE</span><br>ton savoir-faire
        </h1>
        <p class="slide-sub">Mettez en valeur vos compétences et votre CV auprès des meilleurs employeurs.</p>
        <a href="<?= BASE_URL ?>/register" class="slide-btn">Créer mon profil →</a>
      </div>
    </div>

  </div>

  <!-- Contrôles slider -->
  <button class="slider-arrow arrow-prev" onclick="prevSlide()">‹</button>
  <button class="slider-arrow arrow-next" onclick="nextSlide()">›</button>
  <div class="slider-dots" id="dots">
    <div class="dot active" onclick="goToSlide(0)"></div>
    <div class="dot"        onclick="goToSlide(1)"></div>
    <div class="dot"        onclick="goToSlide(2)"></div>
  </div>
</section>

<!-- ── ESPACE PUBLICITAIRE — Bannière vitrine ── -->
<div class="pub-section">
  <div class="pub-inner">
    <div class="pub-label">Publicité</div>
    <div class="pub-slot" id="pubVitrine">
      <?php if (!empty($pubVitrine[0]) && $pubVitrine[0]['image']): ?>
        <a href="<?= htmlspecialchars($pubVitrine[0]['lien_url'] ?? '#') ?>" target="_blank">
          <img src="<?= htmlspecialchars($pubVitrine[0]['image']) ?>" alt="<?= htmlspecialchars($pubVitrine[0]['titre'] ?? '') ?>">
        </a>
      <?php else: ?>
        📢 Espace publicitaire disponible — Contactez-nous
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── FONCTIONNALITÉS ── -->
<section class="features" id="fonctionnalites">
  <div class="section-tag">Plateforme</div>
  <h2 class="section-title">Tout ce dont vous avez besoin</h2>
  <p class="section-sub">STAFF simplifie la mise en relation entre chercheurs d'emploi et entreprises.</p>
  <div class="feat-grid">
    <div class="feat-card">
      <div class="feat-icon">🎯</div>
      <div class="feat-title">Candidatures en ligne</div>
      <div class="feat-desc">Postulez directement aux offres de stage et d'emploi depuis votre espace personnel.</div>
    </div>
    <div class="feat-card">
      <div class="feat-icon">🏢</div>
      <div class="feat-title">Entreprises vérifiées</div>
      <div class="feat-desc">Accédez à une base d'entreprises filtrées par secteur et zone géographique.</div>
    </div>
    <div class="feat-card">
      <div class="feat-icon">📊</div>
      <div class="feat-title">Suivi en temps réel</div>
      <div class="feat-desc">Suivez le statut de vos candidatures : acceptée, refusée ou en attente.</div>
    </div>
    <div class="feat-card">
      <div class="feat-icon">⭐</div>
      <div class="feat-title">Notation des stagiaires</div>
      <div class="feat-desc">Les entreprises notent les stagiaires, valorisant votre profil pour les recruteurs.</div>
    </div>
    <div class="feat-card">
      <div class="feat-icon">📱</div>
      <div class="feat-title">Mobile Money</div>
      <div class="feat-desc">Abonnement simple via MTN Mobile Money ou Orange Money — 3 500 FCFA/an.</div>
    </div>
    <div class="feat-card">
      <div class="feat-icon">🎁</div>
      <div class="feat-title">Livre numérique offert</div>
      <div class="feat-desc">Un livre numérique vous est envoyé gratuitement dès votre inscription.</div>
    </div>
  </div>
</section>

<!-- ── TARIFS ── -->
<section class="tarif-section" id="tarifs">
  <div class="tarif-inner">
    <div class="section-tag">Abonnement</div>
    <h2 class="section-title">Un seul tarif, simple et clair</h2>
    <div class="tarif-card">
      <div class="tarif-price"><span class="tarif-currency">FCFA</span> 3 500</div>
      <div class="tarif-period">par an · renouvelable</div>
      <ul class="tarif-list">
        <li>Candidatures illimitées</li>
        <li>Accès à toutes les offres de stage & emploi</li>
        <li>Suivi complet de vos candidatures</li>
        <li>Cumul possible (paiement anticipé)</li>
        <li>Livre numérique offert à l'inscription</li>
        <li>Paiement via MTN MoMo ou Orange Money</li>
      </ul>
      <a href="<?= BASE_URL ?>/register" class="tarif-btn">S'abonner maintenant →</a>
    </div>
  </div>
</section>

<!-- ── CONTACT ── -->
<section class="contact-section" id="contact">
  <div class="section-tag">Contact</div>
  <h2 class="section-title">Nous contacter</h2>
  <p style="color:var(--muted);font-size:0.9rem;margin-top:10px">Une question ? Écrivez-nous ou suivez-nous sur Facebook.</p>

  <div class="contact-cards">
    <a href="mailto:staffonlinecm@gmail.com" class="contact-card">
      <div class="contact-icon">✉️</div>
      <div>
        <div class="contact-label">Email</div>
        <div class="contact-value">staffonlinecm@gmail.com</div>
      </div>
    </a>
    <a href="https://www.facebook.com/staffonline" target="_blank" class="contact-card">
      <div class="contact-icon">📘</div>
      <div>
        <div class="contact-label">Facebook</div>
        <div class="contact-value">staffonline</div>
      </div>
    </a>
  </div>
  <!-- Placeholder pour contacts futurs -->
  <!-- CONTACTS_SUPPLEMENTAIRES -->
</section>

<!-- ── FOOTER ── -->
<footer>
  <div class="footer-logo">STAFF</div>
  <div class="footer-text">© <?= date('Y') ?> STAFF Platform — Stages & Emploi au Cameroun</div>
  <div class="footer-links">
    <a href="<?= BASE_URL ?>/login">Connexion</a>
    <a href="<?= BASE_URL ?>/register">S'inscrire</a>
  </div>
</footer>

<!-- ── SLIDER JS ── -->
<script>
let current = 0;
const total = 3;
let timer   = null;

function updateSlider() {
  document.getElementById('slides').style.transform = `translateX(-${current * 100}%)`;
  document.querySelectorAll('.dot').forEach((d, i) => d.classList.toggle('active', i === current));
}

function nextSlide() { current = (current + 1) % total; updateSlider(); resetTimer(); }
function prevSlide() { current = (current - 1 + total) % total; updateSlider(); resetTimer(); }
function goToSlide(i) { current = i; updateSlider(); resetTimer(); }

function resetTimer() {
  clearInterval(timer);
  timer = setInterval(nextSlide, 5000);
}

// Auto-play
timer = setInterval(nextSlide, 5000);
</script>
</body>
</html>
