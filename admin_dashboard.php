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
  <title>Admin Dashboard · MotherGuide</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://kit.fontawesome.com/8eed1c1f47.js" crossorigin="anonymous"></script>
  <style>
    /* ── Standard UI Theme ── */
    * { font-family: 'Nunito', Arial, sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: url(./images/bg.png) no-repeat center top / cover fixed !important;
      min-height: 100vh;
    }
    body::before {
      content: ''; position: fixed; inset: 0;
      background: rgba(0,0,0,0.45); z-index: 0;
    }

    /* ── Navbar ── */
    #navbar {
      position: fixed; top: 0; width: 100%; height: 60px;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 40px; background: rgba(13,148,136,0.95);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 100;
    }
    .logo { display:flex; align-items:center; font-size:20px; font-weight:700; color:#fff; text-decoration:none; }
    .logo img { width:80px; margin-right:-16px; }
    .admin-badge {
      background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
      border-radius: 20px; padding: 4px 14px; font-size: 13px; color: #fff;
      display: flex; align-items: center; gap: 8px;
    }
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
    @media (max-width: 600px) { .hamburger { display:flex; } #navbar { padding: 0 20px; } }

    /* ── Layout ── */
    main { position: relative; z-index: 1; padding: 100px 20px 40px; max-width: 1000px; margin: auto; }
    
    .page-header { margin-bottom: 30px; animation: fadeUp 0.4s ease both; }
    .page-header h1 { font-family: 'Playfair Display', serif; font-size: 32px; color: #fff; }
    .page-header p { color: rgba(255,255,255,0.8); font-size: 15px; }

    /* ── Glass Cards ── */
    .glass-card {
      background: rgba(255,255,255,0.95); border-radius: 20px;
      padding: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);
      margin-bottom: 25px; animation: fadeUp 0.5s ease both;
    }

    /* ── Stats & Actions ── */
    .dashboard-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
    .stat-box { background: #f0fdf9; border: 1.5px solid #ccfbf1; border-radius: 15px; padding: 20px; text-align: center; }
    .stat-box h2 { font-size: 36px; color: #0d9488; }
    
    .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .action-btn {
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 8px; padding: 20px; border-radius: 15px; text-decoration: none;
      font-weight: 700; font-size: 14px; transition: all 0.3s; color: #fff;
    }
    .btn-add { background: linear-gradient(135deg, #e75480, #d81b60); }
    .btn-manage { background: linear-gradient(135deg, #0d9488, #0f766e); }
    .btn-logout { background: linear-gradient(135deg, #475569, #1e293b); }
    .action-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.15); }

    /* ── Tips List ── */
    #manage-section { display: none; margin-top: 20px; }
    .tip-item {
      padding: 18px; border-bottom: 1px solid #e5e7eb;
      display: flex; justify-content: space-between; align-items: center;
    }
    .tip-item:last-child { border: none; }
    .tip-info h3 { font-size: 16px; color: #111827; margin-bottom: 4px; }
    .tip-info p { font-size: 13px; color: #6b7280; line-height: 1.5; }
    .week-tag { font-size: 11px; font-weight: 700; background: #ccfbf1; color: #0d9488; padding: 2px 8px; border-radius: 10px; }

    .btn-group { display: flex; gap: 8px; }
    .btn-mini { padding: 6px 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 12px; font-weight: 600; color: #fff; }
    .btn-edit { background: #0d9488; }
    .btn-delete { background: #ef4444; }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) {
      .dashboard-grid { grid-template-columns: 1fr; }
      .action-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<header id="navbar">
  <a class="logo" href="admin_dashboard.php">
    <img src="images/logo.png"> MotherGuide
  </a>
  <div style="display:flex;align-items:center;gap:12px;">
    <div class="admin-badge">
      <i class="fa-solid fa-user-shield"></i>
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
    <h1>Admin Panel</h1>
    <p>Welcome back! You have full control over the tips library.</p>
  </div>

  <div class="dashboard-grid">
    <!-- Quick Stats -->
    <div class="glass-card">
      <div class="stat-box">
        <h2 id="totalTips">--</h2>
        <small>Active Tips</small>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-card">
      <div class="action-grid">
        <a href="admin_add_tip.php" class="action-btn btn-add">
          <i class="fa-solid fa-plus-circle fa-xl"></i> Add New Tip
        </a>
        <button onclick="toggleManage()" class="action-btn btn-manage" style="border:none; cursor:pointer;">
          <i class="fa-solid fa-list-check fa-xl"></i> Manage Tips
        </button>
        <a href="logout.php" class="action-btn btn-logout">
          <i class="fa-solid fa-power-off fa-xl"></i> Sign Out
        </a>
      </div>
    </div>
  </div>

  <!-- Hidden Tips Section -->
  <div id="manage-section" class="glass-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h2 style="font-family:'Playfair Display';">Library Content</h2>
      <button onclick="toggleManage()" style="background:none; border:none; color:#6b7280; cursor:pointer;"><i class="fa-solid fa-xmark"></i> Close</button>
    </div>
    <div id="tipsContainer">
      <div style="text-align:center; padding:40px; color:#9ca3af;">
        <i class="fa-solid fa-spinner fa-spin fa-2xl"></i><br><br>
        Fetching tips...
      </div>
    </div>
  </div>
</main>

<script>
let isManageOpen = false;

function toggleMenu() {
  document.getElementById('hamburger').classList.toggle('open');
  document.getElementById('mobile-menu').classList.toggle('open');
}

async function toggleManage() {
  const section = document.getElementById("manage-section");
  isManageOpen = !isManageOpen;
  
  if (isManageOpen) {
    section.style.display = "block";
    section.scrollIntoView({ behavior: 'smooth' });
    await loadTips();
  } else {
    section.style.display = "none";
  }
}

async function loadTips() {
  const container = document.getElementById("tipsContainer");

  try {
    const res = await fetch("api/get_tips.php");
    const json = await res.json();

    if (json.status !== "success") {
      container.innerHTML = `<div style="color:red; text-align:center;">Failed to load tips.</div>`;
      return;
    }

    document.getElementById("totalTips").textContent = json.data.length;
    container.innerHTML = "";

    if (json.data.length === 0) {
      container.innerHTML = `<div style="text-align:center; padding:20px; color:#9ca3af;">No tips found in database.</div>`;
      return;
    }

    json.data.forEach(tip => {
      const div = document.createElement("div");
      div.className = "tip-item";
      div.innerHTML = `
        <div class="tip-info">
          <span class="week-tag">Week ${tip.pregnancy_week}</span>
          <h3>${tip.title}</h3>
          <p>${tip.content.replace(/<[^>]*>/g, '').substring(0, 80)}...</p>
        </div>
        <div class="btn-group">
          <button class="btn-mini btn-edit" onclick="editTip('${tip.id}')"><i class="fa-solid fa-pen"></i></button>
          <button class="btn-mini btn-delete" onclick="deleteTip('${tip.id}')"><i class="fa-solid fa-trash"></i></button>
        </div>
      `;
      container.appendChild(div);
    });

  } catch (err) {
    container.innerHTML = `<div style="color:red; text-align:center;">Network error.</div>`;
  }
}

// Initial fetch just for the count, without showing the list
window.onload = async () => {
    const res = await fetch("api/get_tips.php");
    const json = await res.json();
    if(json.status === "success") document.getElementById("totalTips").textContent = json.data.length;
};

async function deleteTip(id) {
  if (!confirm("Are you sure you want to delete this tip permanently?")) return;

  const formData = new FormData();
  formData.append("id", id);

  const res = await fetch("api/delete_tip.php", { method: "POST", body: formData });
  const json = await res.json();

  if (json.status === "success") {
    loadTips();
  } else {
    alert("Error: " + json.message);
  }
}

function editTip(id) {
  window.location.href = "admin_edit_tip.php?id=" + id;
}
</script>
</body>
</html>