<?php
require_once 'config/database.php';
include 'includes/header.php';
?>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-logo">UVIA</div>
        <div class="hero-image">
            <img src="assets/images/logo.png" alt="">
        </div>
        <h1 class="hero-title">
            أهلاً بك في <span class="highlight">لوفيا</span>
        </h1>
        <p class="hero-subtitle">
            قهوة تُحضّر لمزاجك.
        </p>
        <a href="menu.php" class="btn-primary">
            <i class="fas fa-utensils"></i>
            استكشف المنيو
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>