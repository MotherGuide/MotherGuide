<?php
session_start();

// Block admins — this page is for patients only
if (isset($_SESSION['admin_id'])) {
    header("Location: ./admin_dashboard.php");
    exit;
}

// Require patient login
$loggedIn = isset($_SESSION["user_id"]);
if (!$loggedIn) {
    header("Location: ./auth.html");
    exit;
}
$userName      = htmlspecialchars($_SESSION["user_name"] ?? "User");
$pregnancyWeek = (int) ($_SESSION["pregnancy_week"] ?? 18);
$pregnancyWeek = max(1, min(40, $pregnancyWeek));
// fetch tips from database
require_once __DIR__ . '/php/Database.php';
require_once __DIR__ . '/php/Tip.php';
$db = new Database();
$conn = $db->connect();
$tipModel = new Tip($conn);
$tipsByWeek = [];
$res = $conn->query("SELECT * FROM tips ORDER BY pregnancy_week ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $w = (int)$row['pregnancy_week'];
        if (!isset($tipsByWeek[$w])) {
            $tipsByWeek[$w] = [];
        }
        $tipsByWeek[$w][] = [
            'title' => $row['title'],
            'body'  => $row['content']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mother Dashboard – MotherGuide</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/8eed1c1f47.js" crossorigin="anonymous"></script>
    <style>
        * { font-family: 'Nunito', Lato, Montserrat, Arial, sans-serif; }

        body {
            background: url(./images/bg.png) no-repeat center top / cover fixed !important;
            min-height: 100vh;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 0;
            pointer-events: none;
        }

        #navbar { z-index: 100; }
        main { position: relative; z-index: 1; width: 100%; box-sizing: border-box; }

        .nav-center a { color: #ffffff !important; }
        .nav-center a:hover { color: #e75480 !important; }
        .logo { color: #ffffff !important; }

        /* ── Shared alignment wrapper ── */
        .content-col {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .content-col .welcome-box {
            max-width: 720px;
            width: 100%;
        }
        .content-col .comment-section {
            max-width: 720px; /* match welcome-box and tip card width for perfect alignment */
            width: 100%;
        }

        /* ── Dashboard layout ── */
        .dashboard-container {
            padding: 28px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }

        /* ── Welcome box ── */
        .welcome-box {
            background: rgba(255,255,255,0.88);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 20px 28px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 720px;  /* matches tip card */
            animation: fadeUp 0.4s ease both;
        }
        .welcome-box h2 {
            font-family: 'Playfair Display', serif;
            color: #e75480;
            font-size: 22px;
            margin-bottom: 4px;
        }
        .welcome-box p { font-size: 14px; color: #374151; }

        /* ── Tip section ── */
        .comment-section {
            width: 100%;
            min-width: 0;
            display: flex;
        }
        .comment-cards {
            flex: 1;
            min-width: 0;
            display: flex;
            justify-content: center;
        }

        /* ── Tip card ── */
        .comment-card {
            background: rgba(255,255,255,0.92) !important;
            border-radius: 18px !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.20) !important;
            padding: 32px 36px !important;
            width: 100%;
            min-width: 0;
            max-width: 720px;
            word-break: break-word;
            overflow-wrap: break-word;
            animation: fadeUp 0.45s ease both;
        }

        /* Week & trimester badges */
        .badge-row { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; align-items: center; }
        .week-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: linear-gradient(135deg,#fde8f0,#fce4ef);
            border: 1.5px solid #f9a8c9;
            border-radius: 20px; padding: 4px 13px;
            font-size: 12px; font-weight: 700; color: #c43d6a;
        }
        .tri-badge {
            display: inline-flex; align-items: center; gap: 5px;
            border-radius: 20px; padding: 4px 13px;
            font-size: 12px; font-weight: 700;
        }
        .tri-badge-1 { background: linear-gradient(135deg,#f0fdf9,#ccfbf1); border: 1.5px solid #5eead4; color: #0d9488; }
        .tri-badge-2 { background: linear-gradient(135deg,#f5f3ff,#ede9fe); border: 1.5px solid #c4b5fd; color: #7c3aed; }
        .tri-badge-3 { background: linear-gradient(135deg,#fffbeb,#fef3c7); border: 1.5px solid #fcd34d; color: #d97706; }

        .tip-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px; color: #1a1a2e;
            margin-bottom: 12px; line-height: 1.35;
        }
        .tip-content { font-size: 14.5px; color: #374151; line-height: 1.80; }
        .tip-content ul { margin: 10px 0 0 18px; padding: 0; }
        .tip-content ul li { margin-bottom: 6px; }

        /* Multiple tips per week */
        #tip-list { display:flex; flex-direction:column; gap:18px; }
        .single-tip { background: transparent; }
        .single-tip h3 { font-family: 'Playfair Display', serif; font-size:18px; margin-bottom:8px; color:#111827; }
        .single-tip .body { font-size:14.5px; color:#374151; line-height:1.75; }

        /* ── Stats row ── */
        .stats {
            margin-top: 20px; display: flex; flex-direction: row;
            gap: 18px; border-top: 1px solid #f3f4f6; padding-top: 14px;
        }
        .stat {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 600; color: #6b7280;
            cursor: pointer; transition: color 0.2s;
        }
        .stat:hover { color: #e75480; }
        .stat img { width: 16px; height: 16px; }
        .direction-arrows img { width: 100%; height: 100%; display: block; }
        .direction-arrows:hover { background: #fff; transform: scale(1.1); }
        .direction-arrows.disabled { opacity: 0.28; cursor: not-allowed; pointer-events: none; }

        /* ── Nav ── */
        .profile-name { font-weight: 600; font-size: 14px; margin-left: 6px; }
        .hamburger { display:none; flex-direction:column; justify-content:center; gap:5px; cursor:pointer; background:none; border:none; padding:4px; }
        .hamburger span { display:block; width:24px; height:2px; background:#ffffff; border-radius:2px; transition:all .3s ease; }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity:0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
        .mobile-menu { display:none; position:fixed; top:60px; left:0; width:100%; background:rgba(13,148,136,0.98); backdrop-filter:blur(10px); flex-direction:column; padding:16px 24px 20px; gap:0; z-index:99; box-shadow:0 8px 20px rgba(0,0,0,0.2); }
        .mobile-menu.open { display:flex; }
        .mobile-menu a { color:#ffffff; font-size:16px; font-weight:500; padding:13px 0; border-bottom:1px solid rgba(255,255,255,0.1); transition:color 0.2s; }
        .mobile-menu a:last-child { border-bottom:none; }
        .mobile-menu a:hover { color:#e75480; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── Tablet ── */
        @media (max-width: 900px) {
            .dashboard-container { padding: 24px 24px; }
            .comment-card { padding: 24px 24px !important; }
        }

        /* ── Mobile ── */
        @media (max-width: 640px) {
            .nav-center { display:none; }
            .hamburger  { display:flex; }
            #navbar { padding: 15px 20px; }
            .profile-name { display: none; }

            .dashboard-container { padding: 16px 12px; }

            .content-col { width: 100%; }
            .content-col .welcome-box,
            .content-col .comment-section { max-width: 100%; }

            .welcome-box { padding: 18px 20px; }

            .comment-cards { width: 100%; }
            .comment-card {
                padding: 20px 18px !important;
                border-radius: 14px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            .tip-title   { font-size: 17px !important; }
            .tip-content { font-size: 13.5px !important; }

            .emergency { margin: 14px 12px 24px; font-size: 13px; }
        }
    </style>
</head>
<body>

<header id="navbar" style="z-index:100;">
    <div class="nav-left">
        <a class="logo" href="./index.php">
            <img src="images/logo.png"> MotherGuide
        </a>
    </div>
    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="#">About</a>
        <a href="#">Resources</a>
        <a href="#">Contact</a>
    </div>
    <div class="nav-right" style="flex-direction:row;align-items:center;cursor:default;">
        <img src="./icons/user-regular-full.svg" class="profile-picture">
        <span class="profile-name" style="color:#ffffff;"><?php echo $userName; ?></span>
        <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<nav class="mobile-menu" id="mobile-menu">
    <a href="index.php">Home</a>
    <a href="#">About</a>
    <a href="#">Resources</a>
    <a href="#">Contact</a>
    <a href="logout.php">Logout</a>
</nav>

<main>
<div class="dashboard-container">

    <!-- Shared column: welcome box + tip card perfectly aligned -->
    <div class="content-col">

        <!-- Welcome box -->
        <div class="welcome-box">
            <h2><?php echo $userName; ?></h2>
            <p>Your tip for <strong>Week <?php echo $pregnancyWeek; ?></strong> of your pregnancy</p>
        </div>

        <!-- Tip card — no navigation arrows, locked to user's week -->
        <div class="comment-section">
            <div class="comment-cards">
                <div class="comment-card" id="tip-card">
                    <div class="badge-row" id="tip-badges"></div>
                    <div id="tip-list"></div>
                    <div class="stats">
                        <div class="stat"><img src="./icons/thumbs-up-regular-full.svg" alt=""><span class="likes-count">—</span></div>
                        <div class="stat"><img src="./icons/thumbs-down-regular-full.svg" alt=""><span class="dislikes-count">—</span></div>
                        <div class="stat"><img src="./icons/comment-dots-solid-full.svg" alt=""><span class="comments-count">—</span></div>
                        <div class="stat"><img src="./icons/eye-solid-full.svg" alt=""><span class="views-count">—</span></div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end .content-col -->

</div>

<a href="mailto:2024bse064@std.must.ac.ug" class="emergency">
    <img src="icons/triangle-exclamation-solid-full.svg" class="emergency-icon">
    Emergency? View Danger Signs or Call Health Worker
</a>
</main>

<script src="./js/script.js"></script>
<script>
// TIPS map populated from the database. Keys are week numbers.
const tips = <?php echo json_encode($tipsByWeek, JSON_HEX_TAG); ?>;

function triNum(w) { return w<=12?1:w<=26?2:3; }
function triLabel(w) { return ["","1st Trimester","2nd Trimester","3rd Trimester"][triNum(w)]; }

const USER_WEEK = <?php echo $pregnancyWeek; ?>;

// Render only the user's own week — no navigation allowed
(function(){
    const tip = tips[USER_WEEK] || { title: 'Tip coming soon', body: '<p>Your weekly tip is being updated. Please check back later.</p>' };
    const t   = triNum(USER_WEEK);
        document.getElementById('tip-badges').innerHTML =
            `<span class="week-badge"><i class="fa-solid fa-baby"></i>&nbsp;Week ${USER_WEEK}</span>` +
            `<span class="tri-badge tri-badge-${t}">${triLabel(USER_WEEK)}</span>`;
        // render all tips for the week
        const tipList = Array.isArray(tip) ? tip : (tip ? [tip] : []);
        document.getElementById('tip-list').innerHTML = tipList.map(ti => `
            <div class="single-tip">
                <h3 class="tip-title">${ti.title}</h3>
                <div class="body">${ti.body}</div>
            </div>
        `).join('');
})();

function toggleMenu() {
    document.getElementById('hamburger').classList.toggle('open');
    document.getElementById('mobile-menu').classList.toggle('open');
}
</script>
</body>
</html>