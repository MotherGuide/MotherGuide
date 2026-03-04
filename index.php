<?php
session_start();
$loggedIn = isset($_SESSION["user_id"]);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotherGuide</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/8eed1c1f47.js" crossorigin="anonymous"></script>
    <style>
        /* ── Fonts ── */
        * { font-family: 'Nunito', Lato, Montserrat, Arial, sans-serif; }

        /* ── Background: cover + fixed, matching auth.html ── */
        body {
            background: url(./images/bg.png) no-repeat center top / cover fixed !important;
        }

        /* ── Dark overlay so content stays readable on any bg ── */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: rgba(0, 0, 0, 0.30);
            z-index: 0;
            pointer-events: none;
        }

        /* ── Lift main content above overlay ── */
        main {
            position: relative;
            z-index: 1;
        }

        /* ── Nav links white (matches auth.html) ── */
        .nav-center a { color: #ffffff !important; }
        .nav-center a:hover { color: #e75480 !important; }

        /* ── Logo white text ── */
        .logo { color: #ffffff !important; }

        /* ── Hero heading font ── */
        .hero-text h1 {
            font-family: 'Playfair Display', serif;
        }

        /* ── Hero text legibility on dark overlay ── */
        .hero-text p { color: #f1f5f9; }

        /* ── Hamburger + mobile menu (matches auth.html exactly) ── */
        .hamburger { display:none; flex-direction:column; justify-content:center; gap:5px; cursor:pointer; background:none; border:none; padding:4px; }
        .hamburger span { display:block; width:24px; height:2px; background:#ffffff; border-radius:2px; transition:all .3s ease; }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity:0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .mobile-menu { display:none; position:fixed; top:60px; left:0; width:100%; background:rgba(13,148,136,0.98); backdrop-filter:blur(10px); flex-direction:column; padding:16px 24px 20px; gap:0; z-index:99; box-shadow:0 8px 20px rgba(0,0,0,0.2); }
        .mobile-menu.open { display:flex; }
        .mobile-menu a { color:#ffffff; font-size:16px; font-weight:500; padding:13px 0; border-bottom:1px solid rgba(255,255,255,0.1); transition: color 0.2s; }
        .mobile-menu a:last-child { border-bottom:none; }
        .mobile-menu a:hover { color: #e75480; }

        /* ── Tablet: 2×2 card grid ── */
        @media (max-width: 900px) {
            .cards {
                flex-wrap: wrap;
                padding: 30px 30px;
                gap: 16px;
            }
            .card { width: calc(50% - 10px); }
            .hero { padding: 40px 30px; }
            .emergency { margin: 20px 30px; }
        }

        /* ── Mobile ── */
        @media (max-width: 640px) {
            .nav-center { display:none; }
            .hamburger { display:flex; }
            #navbar { padding: 15px 20px; }

            /* Hero: single column, centred */
            .hero {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 36px 20px 28px;
                gap: 20px;
            }
            .hero-text { width: 100%; }
            .hero-text h1 { font-size: 30px; }
            .hero-text p { font-size: 15px; }
            .hero-buttons { display: flex; flex-direction: column; gap: 10px; align-items: center; }
            .hero-buttons .btn { width: 100%; max-width: 280px; text-align: center; padding: 11px 18px; font-size: 14px; }

            /* Cards: single column */
            .cards {
                flex-direction: column;
                align-items: center;
                padding: 20px 20px;
                gap: 14px;
            }
            .card { width: 100%; max-width: 360px; }

            /* Emergency bar */
            .emergency { margin: 14px 20px 24px; font-size: 13px; }
        }

        /* ── Extra small (≤ 380px) ── */
        @media (max-width: 380px) {
            .hero-text h1 { font-size: 26px; }
            .auth-card { padding: 32px 18px; }
        }
    </style>
</head>
<body>
    <header id="navbar" style="z-index:100;">
        <div class="nav-left">
            <a class="logo" href="./index.php">
                <img src="images/logo.png">
                MotherGuide
            </a>
        </div>
        <div class="nav-center">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Resources</a>
            <a href="#">Contact</a>
        </div>
        <div class="nav-right" style="display:flex;align-items:center;gap:10px;">
                <?php if ($loggedIn): ?>
                    <img src="./icons/user-regular-full.svg" class="profile-picture" style="width:28px;height:28px">
                    <span class="profile-name" style="color:#ffffff;font-weight:600"><?php echo htmlspecialchars($_SESSION["user_name"]); ?></span>
                    <a href="logout.php" style="color:#ffffff;margin-left:10px;font-weight:700;text-decoration:none;">Logout</a>
                <?php endif; ?>
                <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu"><span></span><span></span><span></span></button>
            </div>
    </header>

    <nav class="mobile-menu" id="mobile-menu">
        <a href="index.php">Home</a>
        <a href="#">About</a>
        <a href="tips.html">Resources</a>
        <a href="#">Contact</a>
    </nav>

    <main>

        <div class="hero">
            <div class="hero-text">

                <?php if ($loggedIn): ?>
                    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
                    <p>Your current pregnancy week: <?php echo $_SESSION["pregnancy_week"]; ?></p>
                <?php else: ?>
                    <h1>Welcome to <br> MotherGuide</h1>
                <?php endif; ?>
                
                <p>Your trusted pregnancy education companion.</p>
                        <div class="hero-buttons">
                            <?php if ($loggedIn): ?>
                                <a class="btn btn-primary" href="./tips.php">Read Tips</a>
                            <?php else: ?>
                                <a class="btn btn-primary" href="./auth.html">Register as Mother</a>
                                <a class="btn btn-secondary" href="./auth_admin_login.html">Admin Login</a>
                            <?php endif; ?>
                        </div>
            </div>
            <!-- <img src="images/hero-mother.png"> -->
            <!-- <img src="images/2.png" style="width: 500px;"> -->
        </div>

        <div class="cards">
            <div class="card">
                <!-- <img src="images/nutrition.png"> -->
                <!-- <i class="fa-solid fa-apple-whole card-icons"></i> -->
                <img src="icons/apple-whole-solid-full.svg">
                <h3>Nutrition Tips</h3>
                <p>Healthy food guidance for you and baby.</p>
                <a href="./tips.php" class="btn btn-primary">Read More</a>
            </div>

            <div class="card">
                <!-- <img src="images/warning.png"> -->
                <!-- <i class="fa-solid fa-triangle-exclamation card-icons"></i> -->
                <img src="icons/triangle-exclamation-solid-full.svg">
                <h3>Warning Signs</h3>
                <p>Know when to seek medical help.</p>
                <a href="./tips.php" class="btn btn-primary">View</a>
            </div>

            <div class="card">
                <!-- <img src="images/birth.png"> -->
                <!-- <i class="fa-solid fa-baby card-icons"></i> -->
                <img src="icons/baby-solid-full.svg">
                <h3>Preparing for Delivery</h3>
                <p>Birth checklist and planning tips.</p>
                <a href="./tips.php" class="btn btn-primary">Read More</a>
            </div>

            <div class="card">
                <!-- <img src="images/baby.png"> -->
                <!-- <i class="fa-solid fa-person-breastfeeding card-icons"></i> -->
                <img src="icons/person-breastfeeding-solid-full.svg">
                <h3>Baby Care</h3>
                <p>After delivery care guidance.</p>
                <a href="./tips.php" class="btn btn-primary">Read More</a>
            </div>
        </div>

        <a href="mailto:2024bse064@std.must.ac.ug" class="emergency">
            <!-- <i class="fa-solid fa-triangle-exclamation emergency-icon"></i>  --> 
            <img src="icons/triangle-exclamation-solid-full.svg" class="emergency-icon"> 
            Emergency? View Danger Signs or Call Health Worker
        </a>

    </main>

    <script src="./js/script.js"></script>
    <script>
        function toggleMenu(){
            const btn = document.getElementById('hamburger');
            const menu = document.getElementById('mobile-menu');
            btn.classList.toggle('open');
            menu.classList.toggle('open');
        }
    </script>

</body>
</html>