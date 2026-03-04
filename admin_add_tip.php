<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth_admin_login.html');
    exit;
}
$adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Add Tip · MotherGuide</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://kit.fontawesome.com/8eed1c1f47.js" crossorigin="anonymous"></script>
  <style>
    * { font-family: 'Nunito', Arial, sans-serif; box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: url(./images/bg.png) no-repeat center top / cover fixed !important;
      min-height: 100vh;
      overflow-x: hidden;
    }
    body::before {
      content: '';
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.40);
      z-index: 0;
      pointer-events: none;
    }

    /* ── Navbar ── */
    #navbar {
      position: fixed; top: 0; width: 100vw; height: 60px;
      display: flex; align-items: center; justify-content: space-between;
      padding: 15px 40px;
      background: rgba(13,148,136,0.85);
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
      z-index: 100;
      transition: all 0.3s ease;
    }
    #navbar.scrolled { background: rgba(13,148,136,1); }
    .logo { display:flex; align-items:center; font-size:20px; font-weight:700; color:#fff; text-decoration:none; }
    .logo img { width:80px; margin-right:-16px; }
    .nav-right { display:flex; align-items:center; gap:12px; }
    .admin-badge {
      background: rgba(255,255,255,0.18);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 20px;
      padding: 4px 12px;
      font-size: 12px; font-weight: 700; color: #fff;
      display: flex; align-items: center; gap: 6px;
    }
    .admin-badge i { color: #fcd34d; }

    /* ── Hamburger ── */
    .hamburger { display:none; flex-direction:column; justify-content:center; gap:5px; cursor:pointer; background:none; border:none; padding:4px; }
    .hamburger span { display:block; width:24px; height:2px; background:#fff; border-radius:2px; transition:all .3s; }
    .hamburger.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity:0; }
    .hamburger.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }
    .mobile-menu { display:none; position:fixed; top:60px; left:0; width:100%; background:rgba(13,148,136,0.98); backdrop-filter:blur(10px); flex-direction:column; padding:16px 24px 20px; gap:0; z-index:99; box-shadow:0 8px 20px rgba(0,0,0,0.2); }
    .mobile-menu.open { display:flex; }
    .mobile-menu a { color:#fff; font-size:16px; font-weight:500; padding:13px 0; border-bottom:1px solid rgba(255,255,255,0.1); text-decoration:none; transition:color .2s; }
    .mobile-menu a:last-child { border-bottom:none; }
    .mobile-menu a:hover { color:#e75480; }

    /* ── Page layout ── */
    main {
      position: relative; z-index: 1;
      min-height: 100vh;
      padding: 80px 40px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* ── Page header ── */
    .page-header {
      width: 100%; max-width: 680px;
      margin-bottom: 20px;
      animation: fadeUp 0.4s ease both;
    }
    .page-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 30px; color: #fff;
      margin-bottom: 4px;
    }
    .page-header p { font-size: 13.5px; color: rgba(255,255,255,0.75); }

    /* ── Glass form card ── */
    .glass-card {
      background: rgba(255,255,255,0.92);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.20);
      padding: 36px 40px;
      width: 100%; max-width: 680px;
      animation: fadeUp 0.45s ease both;
    }

    /* ── Form elements ── */
    .input-group { margin-bottom: 18px; }
    .input-group label {
      display: block;
      font-size: 13px; font-weight: 700; color: #374151;
      margin-bottom: 6px;
    }
    .input-wrap { position: relative; }
    .input-wrap i.icon {
      position: absolute; left: 13px; top: 50%;
      transform: translateY(-50%);
      color: #9ca3af; font-size: 14px; pointer-events: none;
    }
    .input-wrap.top-icon i.icon { top: 16px; transform: none; }

    .input-wrap input,
    .input-wrap select,
    .input-wrap textarea {
      width: 100%;
      padding: 12px 14px 12px 40px;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      font-family: 'Nunito', sans-serif;
      font-size: 14px; color: #1f2937;
      background: #fafafa;
      transition: all 0.2s; outline: none;
      appearance: none;
    }
    .input-wrap textarea {
      min-height: 180px;
      resize: vertical;
      padding-top: 12px;
      line-height: 1.6;
    }
    .input-wrap input:focus,
    .input-wrap select:focus,
    .input-wrap textarea:focus {
      border-color: #0d9488;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
    }

    .hint {
      font-size: 11.5px; color: #9ca3af;
      margin-top: 5px; display: flex; align-items: center; gap: 5px;
    }
    .hint i { color: #0d9488; font-size: 11px; }

    /* ── Week preview badge ── */
    .week-preview {
      display: inline-flex; align-items: center; gap: 6px;
      background: linear-gradient(135deg,#f0fdf9,#ccfbf1);
      border: 1.5px solid #5eead4;
      border-radius: 20px; padding: 4px 14px;
      font-size: 12px; font-weight: 700; color: #0d9488;
      margin-top: 6px;
    }

    /* ── Submit button ── */
    .btn-submit {
      width: 100%; padding: 13px;
      background: linear-gradient(135deg,#0d9488,#0f766e);
      color: white; border: none; border-radius: 10px;
      font-family: 'Nunito', sans-serif;
      font-size: 15px; font-weight: 700; cursor: pointer;
      letter-spacing: 0.3px; transition: all 0.25s;
      margin-top: 4px;
      box-shadow: 0 4px 14px rgba(13,148,136,0.35);
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,148,136,0.45); }
    .btn-submit:active { transform: translateY(0); }

    /* ── Back link ── */
    .back-link {
      display: inline-flex; align-items: center; gap: 6px;
      color: rgba(255,255,255,0.80);
      font-size: 13px; font-weight: 600;
      text-decoration: none;
      margin-bottom: 10px;
      transition: color 0.2s;
    }
    .back-link:hover { color: #fff; }

    /* ── Message ── */
    .msg {
      margin-top: 14px;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13.5px; font-weight: 600;
      display: none;
    }
    .msg.success { background:#f0fdf9; border:1.5px solid #5eead4; color:#0d9488; display:block; }
    .msg.error   { background:#fef2f2; border:1.5px solid #fca5a5; color:#dc2626; display:block; }

    @keyframes fadeUp {
      from { opacity:0; transform:translateY(18px); }
      to   { opacity:1; transform:translateY(0); }
    }

    /* ── Tablet ── */
    @media (max-width: 768px) {
      main { padding: 76px 20px 32px; }
      .glass-card { padding: 28px 24px; }
    }

    /* ── Mobile ── */
    @media (max-width: 600px) {
      #navbar { padding: 15px 18px; }
      .admin-badge span { display: none; }
      .hamburger { display: flex; }

      main { padding: 72px 14px 28px; }
      .glass-card { padding: 22px 18px; border-radius: 14px; }
      .page-header h1 { font-size: 24px; }
    }
  </style>
</head>
<body>

<header id="navbar">
  <a class="logo" href="./index.php">
    <img src="images/logo.png"> MotherGuide
  </a>
  <div class="nav-right">
    <div class="admin-badge">
      <i class="fa-solid fa-shield-halved"></i>
      <span><?php echo $adminName; ?></span>
    </div>
    <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<nav class="mobile-menu" id="mobile-menu">
  <a href="admin_dashboard.php"><i class="fa-solid fa-gauge" style="margin-right:8px"></i>Dashboard</a>
  <a href="admin_add_tip.php"><i class="fa-solid fa-plus" style="margin-right:8px"></i>Add Tip</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket" style="margin-right:8px"></i>Logout</a>
</nav>

<main>

  <div class="page-header">
    <a class="back-link" href="admin_dashboard.php">
      <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
    <h1>Add a New Tip</h1>
    <p>Tips are shown to mothers on their dashboard based on their pregnancy week.</p>
  </div>

  <div class="glass-card">
    <form id="tip-form">

      <!-- Week selector -->
      <div class="input-group">
        <label for="pregnancy_week">Pregnancy Week</label>
        <div class="input-wrap">
          <i class="fa-solid fa-calendar-heart icon"></i>
          <select id="pregnancy_week" name="pregnancy_week" onchange="updateBadge(this.value)">
            <optgroup label="1st Trimester (Weeks 1–12)">
              <?php for ($i=1; $i<=12; $i++): ?>
                <option value="<?php echo $i; ?>">Week <?php echo $i; ?></option>
              <?php endfor; ?>
            </optgroup>
            <optgroup label="2nd Trimester (Weeks 13–26)">
              <?php for ($i=13; $i<=26; $i++): ?>
                <option value="<?php echo $i; ?>">Week <?php echo $i; ?></option>
              <?php endfor; ?>
            </optgroup>
            <optgroup label="3rd Trimester (Weeks 27–40)">
              <?php for ($i=27; $i<=40; $i++): ?>
                <option value="<?php echo $i; ?>">Week <?php echo $i; ?></option>
              <?php endfor; ?>
            </optgroup>
          </select>
        </div>
        <span class="week-preview" id="week-badge">
          <i class="fa-solid fa-baby"></i> Week 1 · 1st Trimester
        </span>
      </div>

      <!-- Title -->
      <div class="input-group">
        <label for="title">Tip Title</label>
        <div class="input-wrap">
          <i class="fa-solid fa-heading icon"></i>
          <input type="text" id="title" name="title" placeholder="e.g. Eat for Two — But Eat Smart">
        </div>
      </div>

      <!-- Content -->
      <div class="input-group">
        <label for="content">Tip Content <span style="font-weight:400;color:#9ca3af;">(HTML allowed)</span></label>
        <div class="input-wrap top-icon">
          <i class="fa-solid fa-align-left icon"></i>
          <textarea id="content" name="content" placeholder="Write your tip here. You can use HTML tags like &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt; etc."></textarea>
        </div>
        <p class="hint"><i class="fa-solid fa-circle-info"></i> Use &lt;p&gt; for paragraphs and &lt;ul&gt;&lt;li&gt; for bullet lists</p>
      </div>

      <button class="btn-submit" type="submit">
        <i class="fa-solid fa-circle-plus"></i> Add Tip
      </button>

      <div class="msg" id="msg"></div>
    </form>
  </div>

</main>

<script>
  // Trimester helper
  function trimesterLabel(w) {
    return w <= 12 ? '1st Trimester' : w <= 26 ? '2nd Trimester' : '3rd Trimester';
  }
  function updateBadge(w) {
    document.getElementById('week-badge').innerHTML =
      `<i class="fa-solid fa-baby"></i> Week ${w} · ${trimesterLabel(parseInt(w))}`;
  }

  // Form submit
  document.getElementById('tip-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg = document.getElementById('msg');
    msg.className = 'msg';
    msg.textContent = '';

    try {
      const res  = await fetch('api/admin_add_tip.php', { method:'POST', body: new FormData(e.target) });
      const json = await res.json();
      if (json.status === 'success') {
        msg.className = 'msg success';
        msg.innerHTML = `<i class="fa-solid fa-circle-check"></i> Tip added successfully (ID: ${json.id})`;
        e.target.reset();
        updateBadge(1);
      } else {
        msg.className = 'msg error';
        msg.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> ${json.message || 'An error occurred'}`;
      }
    } catch(err) {
      msg.className = 'msg error';
      msg.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> Network error — please try again`;
    }
  });

  // Navbar scroll
  window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 40);
  });
  function toggleMenu() {
    document.getElementById('hamburger').classList.toggle('open');
    document.getElementById('mobile-menu').classList.toggle('open');
  }
</script>
</body>
</html>