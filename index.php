<?php
session_start();
$loggedIn = isset($_SESSION["user_id"]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>MotherGuide</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/8eed1c1f47.js" crossorigin="anonymous"></script>
</head>
<body>
    <header id="navbar">
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
        <div class="nav-right">
            <a class="btn btn-primary" href="./auth.html">Register</a>
            <a class="btn btn-secondary" href="./auth_admin_login.html">Admin Login</a>
        </div>
    </header>

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
                    <a class="btn btn-primary" href="./auth.html">Register as Mother</a>
                    <a class="btn btn-secondary" href="./auth_admin_login.html">Admin Login</a>
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
                <a href="./tips.html" class="btn btn-primary">Read More</a>
            </div>

            <div class="card">
                <!-- <img src="images/warning.png"> -->
                <!-- <i class="fa-solid fa-triangle-exclamation card-icons"></i> -->
                <img src="icons/triangle-exclamation-solid-full.svg">
                <h3>Warning Signs</h3>
                <p>Know when to seek medical help.</p>
                <a href="./tips.html" class="btn btn-primary">View</a>
            </div>

            <div class="card">
                <!-- <img src="images/birth.png"> -->
                <!-- <i class="fa-solid fa-baby card-icons"></i> -->
                <img src="icons/baby-solid-full.svg">
                <h3>Preparing for Delivery</h3>
                <p>Birth checklist and planning tips.</p>
                <a href="./tips.html" class="btn btn-primary">Read More</a>
            </div>

            <div class="card">
                <!-- <img src="images/baby.png"> -->
                <!-- <i class="fa-solid fa-person-breastfeeding card-icons"></i> -->
                <img src="icons/person-breastfeeding-solid-full.svg">
                <h3>Baby Care</h3>
                <p>After delivery care guidance.</p>
                <a href="./tips.html" class="btn btn-primary">Read More</a>
            </div>
        </div>

        <a href="mailto:2024bse064@std.must.ac.ug" class="emergency">
            <!-- <i class="fa-solid fa-triangle-exclamation emergency-icon"></i>  --> 
            <img src="icons/triangle-exclamation-solid-full.svg" class="emergency-icon"> 
            Emergency? View Danger Signs or Call Health Worker
        </a>

    </main>

    <script src="./js/script.js"></script>

</body>
</html>