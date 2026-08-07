<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// تسجيل مشاهدة بسيط للصفحة الرئيسية (يُستخدم في إحصائيات لوحة التحكم)
try {
    db()->prepare('INSERT INTO page_views (page) VALUES (:p)')->execute(['p' => 'home']);
} catch (Throwable $e) {
    // لا نكسر عرض الصفحة إذا فشل تسجيل المشاهدة لأي سبب
}

$year = date('Y');
$age = (function () {
    $birth = new DateTime('2006-06-26');
    $now = new DateTime();
    return $now->diff($birth)->y;
})();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="يوسف الحمزي — مهندس برمجيات: تطوير بايثون، مواقع ووردبريس، وأنظمة ERP.">
<title>Yusef Alhmzy — Portfolio</title>
<link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32.png">
<link rel="apple-touch-icon" href="assets/img/favicon-192.png">
<link rel="stylesheet" href="css/fonts.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar" id="navbar">
    <a href="#home" class="nav-logo">
        <img src="assets/img/favicon-32.png" alt="">
        <span>Yusef Alhmzy</span>
    </a>
    <ul class="nav-links">
        <li><a href="#about" data-i18n="nav_about"></a></li>
        <li><a href="#skills" data-i18n="nav_skills"></a></li>
        <li><a href="#projects" data-i18n="nav_projects"></a></li>
        <li><a href="#services" data-i18n="nav_services"></a></li>
        <li><a href="#contact" data-i18n="nav_contact"></a></li>
    </ul>
    <div class="nav-actions">
        <select id="langSelect" class="nav-select" aria-label="Language">
            <option value="ar">العربية</option>
            <option value="en">English</option>
            <option value="es">Español</option>
            <option value="fr">Français</option>
            <option value="ja">日本語</option>
        </select>
        <label class="theme-switch">
            <input type="checkbox" id="themeSwitch">
            <span class="knob"></span>
        </label>
        <button class="nav-burger" id="navBurger" aria-label="Menu">☰</button>
    </div>
</nav>

<div class="mobile-drawer" id="mobileDrawer">
    <button class="close-drawer" id="closeDrawer">×</button>
    <a href="#about" data-i18n="nav_about"></a>
    <a href="#skills" data-i18n="nav_skills"></a>
    <a href="#projects" data-i18n="nav_projects"></a>
    <a href="#services" data-i18n="nav_services"></a>
    <a href="#contact" data-i18n="nav_contact"></a>
</div>

<section class="hero" id="home">
    <div class="hero-orb o1"></div>
    <div class="hero-orb o2"></div>
    <div class="container hero-grid">
        <div>
            <p class="eyebrow" data-i18n="hero_greeting"></p>
            <h1 class="hero-title">Yusef <span class="accent">Alhmzy</span></h1>
            <p class="hero-tagline" data-i18n="hero_role" style="font-weight:700; color:var(--gold); margin-bottom:6px;"></p>
            <p class="hero-tagline" data-i18n="hero_tagline"></p>
            <div class="hero-cta">
                <a href="#projects" class="btn btn-gold" data-i18n="hero_cta_projects"></a>
                <a href="#contact" class="btn btn-ghost" data-i18n="hero_cta_contact"></a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="num" data-counter="<?= (int) $age ?>">0</div><div class="label" data-i18n="stat_age"></div></div>
                <div class="hero-stat"><div class="num" data-counter="3">0</div><div class="label" data-i18n="stat_projects"></div></div>
                <div class="hero-stat"><div class="num" data-counter="5">0</div><div class="label" data-i18n="stat_langs"></div></div>
            </div>
        </div>
        <div class="hero-portrait">
            <div class="hero-portrait-ring">
                <img src="assets/img/Yusef.jpg" alt="Yusef Alhmzy" width="320" height="320">
            </div>
        </div>
    </div>
</section>

<section id="about">
    <div class="container about-grid">
        <div class="about-text reveal">
            <p class="eyebrow" data-i18n="about_eyebrow"></p>
            <h2 class="section-title" data-i18n="about_title"></h2>
            <p id="bioIntro"></p>
            <p id="bioDream"></p>
        </div>
        <div class="reveal">
            <div class="timeline-card">
                <h4 data-i18n="fact_role_label"></h4>
                <p data-i18n="fact_role_value"></p>
            </div>
            <div class="timeline-card">
                <h4 data-i18n="fact_focus_label"></h4>
                <p data-i18n="fact_focus_value"></p>
            </div>
            <div class="timeline-card">
                <h4 data-i18n="fact_status_label"></h4>
                <p data-i18n="fact_status_value"></p>
            </div>
        </div>
    </div>
</section>

<section id="skills">
    <div class="container">
        <p class="eyebrow" data-i18n="skills_eyebrow"></p>
        <h2 class="section-title" data-i18n="skills_title"></h2>
        <p class="section-sub" data-i18n="skills_sub"></p>
        <div class="skills-grid">
            <div class="skill-chip reveal"><div class="icon">🐍</div><div class="name">Python</div></div>
            <div class="skill-chip reveal"><div class="icon">📝</div><div class="name">WordPress</div></div>
            <div class="skill-chip reveal"><div class="icon">🏢</div><div class="name">ERP</div></div>
            <div class="skill-chip reveal"><div class="icon">🌐</div><div class="name">HTML / CSS</div></div>
            <div class="skill-chip reveal"><div class="icon">⚡</div><div class="name">JavaScript</div></div>
            <div class="skill-chip reveal"><div class="icon">🐘</div><div class="name">PHP</div></div>
            <div class="skill-chip reveal"><div class="icon">🗄️</div><div class="name">SQLite / SQL</div></div>
            <div class="skill-chip reveal"><div class="icon">🔧</div><div class="name">Git</div></div>
        </div>
    </div>
</section>

<section id="projects">
    <div class="container">
        <p class="eyebrow" data-i18n="projects_eyebrow"></p>
        <h2 class="section-title" data-i18n="projects_title"></h2>
        <p class="section-sub" data-i18n="projects_sub"></p>
        <div class="projects-grid" id="projectsGrid"></div>
    </div>
</section>

<section id="services">
    <div class="container">
        <p class="eyebrow" data-i18n="services_eyebrow"></p>
        <h2 class="section-title" data-i18n="services_title"></h2>
        <p class="section-sub" data-i18n="services_sub"></p>
        <div class="services-grid">
            <div class="service-card reveal">
                <div class="icon">🐍</div>
                <h3 data-i18n="service1_title"></h3>
                <p data-i18n="service1_desc"></p>
            </div>
            <div class="service-card reveal">
                <div class="icon">📝</div>
                <h3 data-i18n="service2_title"></h3>
                <p data-i18n="service2_desc"></p>
            </div>
            <div class="service-card reveal">
                <div class="icon">🏢</div>
                <h3 data-i18n="service3_title"></h3>
                <p data-i18n="service3_desc"></p>
            </div>
        </div>
    </div>
</section>

<section id="contact">
    <div class="container contact-grid">
        <div class="reveal">
            <p class="eyebrow" data-i18n="contact_eyebrow"></p>
            <h2 class="section-title" data-i18n="contact_title"></h2>
            <p class="section-sub" data-i18n="contact_sub"></p>

            <form id="contactForm" class="contact-form" style="position:relative;">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <input type="text" name="name" required data-i18n-placeholder="form_name_ph" placeholder="">
                <input type="email" name="email" required data-i18n-placeholder="form_email_ph" placeholder="">
                <textarea name="message" required data-i18n-placeholder="form_message_ph" placeholder=""></textarea>
                <button type="submit" class="btn btn-gold" data-i18n="form_submit"></button>
                <div id="formMsg" class="form-msg" data-ok="" data-err=""></div>
            </form>
        </div>

        <div class="social-list reveal">
            <a class="social-row" href="https://www.facebook.com/profile.php?id=100085119330877" target="_blank" rel="noopener noreferrer"><span class="ic">📘</span> Facebook</a>
            <a class="social-row" href="https://github.com/yswef" target="_blank" rel="noopener noreferrer"><span class="ic">💻</span> GitHub</a>
            <a class="social-row" href="https://www.youtube.com/@animeking7343" target="_blank" rel="noopener noreferrer"><span class="ic">▶️</span> YouTube</a>
            <a class="social-row" href="https://t.me/PP_Q6" target="_blank" rel="noopener noreferrer"><span class="ic">✈️</span> Telegram</a>
            <a class="social-row" href="https://wa.me/+967780143832" target="_blank" rel="noopener noreferrer"><span class="ic">💬</span> WhatsApp</a>
            <a class="social-row" href="https://www.instagram.com/anme_king1090/" target="_blank" rel="noopener noreferrer"><span class="ic">📷</span> Instagram</a>
            <a class="social-row" href="https://discord.gg/9g7uXxmw7J" target="_blank" rel="noopener noreferrer"><span class="ic">🎮</span> Discord</a>
        </div>
    </div>
</section>

<footer class="site-footer">
    <p data-i18n="footer_rights"><?= 'All rights reserved © Yusef ' . e((string) $year) ?></p>
</footer>

<script src="js/main.js" defer></script>
</body>
</html>
