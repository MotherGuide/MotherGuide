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
  <title>Admin Dashboard — MotherGuide</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://kit.fontawesome.com/8eed1c1f47.js" crossorigin="anonymous"></script>
  <style>
    * { font-family: 'Nunito', Arial, sans-serif; box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: url(./images/bg.png) no-repeat center top / cover fixed !important;
      min-height: 100vh;
      overflow-x: hidden;
      color: #1a1a2e;
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
      width: 100%; max-width: 860px;
      margin-bottom: 24px;
      animation: fadeUp 0.4s ease both;
    }
    .page-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 32px; color: #fff;
      margin-bottom: 4px;
    }
    .page-header p { font-size: 14px; color: rgba(255,255,255,0.75); }

    /* ── Glass card ── */
    .glass-card {
      background: rgba(255,255,255,0.92);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.20);
      padding: 32px 36px;
      width: 100%; max-width: 860px;
      animation: fadeUp 0.45s ease both;
    }

    /* ── Stat row ── */
    .stat-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 28px;
    }
    .stat-box {
      background: linear-gradient(135deg, #f0fdf9, #ccfbf1);
      border: 1.5px solid #5eead4;
      border-radius: 14px;
      padding: 20px;
      text-align: center;
    }
    .stat-box.pink {
      background: linear-gradient(135deg, #fde8f0, #fce4ef);
      border-color: #f9a8c9;
    }
    .stat-box.amber {
      background: linear-gradient(135deg, #fffbeb, #fef3c7);
      border-color: #fcd34d;
    }
    .stat-box i { font-size: 22px; margin-bottom: 8px; display:block; }
    .stat-box .teal i  { color: #0d9488; }
    .stat-box .stat-val { font-size: 28px; font-weight: 700; color: #1a1a2e; }
    .stat-box .stat-lbl { font-size: 12px; color: #6b7280; font-weight: 600; margin-top: 2px; }

    /* ── Section title ── */
    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 18px; color: #1a1a2e;
      margin-bottom: 16px;
      padding-bottom: 10px;
      border-bottom: 1.5px solid #f3f4f6;
    }

    /* ── Action buttons ── */
    .actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
    }
    .action-btn {
      display: flex; align-items: center; gap: 12px;
      padding: 16px 20px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700; font-size: 14px;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .action-btn i { font-size: 20px; flex-shrink: 0; }

    .btn-teal  { background: linear-gradient(135deg,#0d9488,#0f766e); color:#fff; }
    .btn-pink  { background: linear-gradient(135deg,#e75480,#c43d6a); color:#fff; box-shadow:0 4px 14px rgba(231,84,128,0.35); }
    .btn-slate { background: linear-gradient(135deg,#475569,#334155); color:#fff; }

    .action-btn .btn-label { display:flex; flex-direction:column; }
    .action-btn .btn-label small { font-weight: 400; font-size: 11px; opacity: 0.85; margin-top:1px; }

    @keyframes fadeUp {
      from { opacity:0; transform:translateY(18px); }
      to   { opacity:1; transform:translateY(0); }
    }

    /* ── Tablet ── */
    @media (max-width: 768px) {
      main { padding: 76px 20px 32px; }
      .glass-card { padding: 24px 22px; }
      .stat-row { grid-template-columns: repeat(3,1fr); gap:10px; }
      .stat-box { padding: 14px 10px; }
      .stat-box .stat-val { font-size: 22px; }
    }

    /* ── Mobile ── */
    @media (max-width: 600px) {
      #navbar { padding: 15px 18px; }
      .admin-badge span { display: none; }
      .hamburger { display: flex; }

      main { padding: 72px 14px 28px; }
      .glass-card { padding: 20px 16px; border-radius: 14px; }
      .page-header h1 { font-size: 24px; }

      /* Stack stats vertically — full width, easy to read */
      .stat-row { grid-template-columns: 1fr; gap: 12px; }
      .stat-box {
        padding: 18px 20px;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 16px;
        text-align: left;
        border-radius: 12px;
      }
      .stat-box i { font-size: 28px; margin-bottom: 0; flex-shrink: 0; }
      .stat-box .stat-val { font-size: 28px; }
      .stat-box .stat-lbl { font-size: 13px; }

      .actions { grid-template-columns: 1fr; }
      .action-btn { padding: 14px 16px; }
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
    <h1>Admin Dashboard</h1>
    <p>Welcome back, <?php echo $adminName; ?>. Manage tips and content from here.</p>
  </div>

  <div class="glass-card">

    <!-- Stats -->
    <div class="stat-row">
      <div class="stat-box">
        <i class="fa-solid fa-lightbulb" style="color:#0d9488;"></i>
        <div class="stat-val">40</div>
        <div class="stat-lbl">Total Tips</div>
      </div>
      <div class="stat-box pink">
        <i class="fa-solid fa-users" style="color:#e75480;"></i>
        <div class="stat-val">—</div>
        <div class="stat-lbl">Mothers</div>
      </div>
      <div class="stat-box amber">
        <i class="fa-solid fa-eye" style="color:#d97706;"></i>
        <div class="stat-val">—</div>
        <div class="stat-lbl">Views</div>
      </div>
    </div>

    <!-- Actions -->
    <div class="section-title">Quick Actions</div>
    <div class="actions">
      <a class="action-btn btn-pink" href="admin_add_tip.php">
        <i class="fa-solid fa-circle-plus"></i>
        <span class="btn-label">Add Tip<small>Post a new weekly tip</small></span>
      </a>
      <a class="action-btn btn-teal" href="#">
        <i class="fa-solid fa-list"></i>
        <span class="btn-label">Manage Tips<small>Edit or delete tips</small></span>
      </a>
      <a class="action-btn btn-slate" href="logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span class="btn-label">Logout<small>End admin session</small></span>
      </a>
    </div>

  </div>
</main>

<script>
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