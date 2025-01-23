// Theme Toggle
const themeToggle = document.getElementById('switch');
const userPreferredTheme = localStorage.getItem('theme');

// Apply user or system preferred theme
if (userPreferredTheme) {
    document.body.classList.toggle('dark-theme', userPreferredTheme === 'dark');
} else {
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.body.classList.toggle('dark-theme', systemPrefersDark);
}

// Toggle theme on button click
themeToggle.addEventListener('click', function () {
    document.body.classList.toggle('dark-theme');
    const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
});

// Code Rain Animation
const canvas = document.getElementById('codeRainCanvas');
const ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

const fontSize = 16;
const columns = Math.floor(canvas.width / fontSize);
const drops = Array(columns).fill(1);
const codes = ['HTML', 'CSS', 'JS', 'Python', 'Code', 'if', 'else', '<>', '{}', 'print'];

function drawRain() {
    const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    ctx.fillStyle = theme === 'dark' ? 'rgba(51, 51, 51, 0.1)' : 'rgba(244, 244, 244, 0.14)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = theme === 'dark' ? 'rgba(255, 255, 255, 0.5)' : 'rgba(0, 0, 0, 0.5)';
    ctx.font = `${fontSize}px monospace`;
    for (let i = 0; i < drops.length; i++) {
        const text = codes[Math.floor(Math.random() * codes.length)];
        const x = i * fontSize;
        const y = drops[i] * fontSize;
        ctx.fillText(text, x, y);
        if (y > canvas.height && Math.random() > 0.975) {
            drops[i] = 0;
        }
        drops[i]++;
    }
}

let rainInterval = setInterval(drawRain, 70);

// Handle window resize with debounce
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        drawRain();
    }, 100);
});

// Smooth Scroll to Section
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) {
        console.error(`Section ${sectionId} not found`);
        return;
    }

    const targetPosition = section.offsetTop;
    const startPosition = window.scrollY;
    const distance = targetPosition - startPosition;
    const duration = 1000;
    let startTime = null;

    function animationScroll(currentTime) {
        if (!startTime) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = ease(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animationScroll);
    }

    function ease(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return (c / 2) * t * t + b;
        t--;
        return (-c / 2) * (t * (t - 2) - 1) + b;
    }

    requestAnimationFrame(animationScroll);
}

// Intersection Observer for Sections
const sections = document.querySelectorAll("section, footer, .custom-class, [id]");
const targets = [
    document.getElementById("About_me"),
    document.querySelector(".title"),
    document.querySelector("footer")
];

const tabs = document.querySelectorAll(".tab");

const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const activeElement = entry.target;
                tabs.forEach((tab) => {
                    if (tab.id === `tab${targets.indexOf(activeElement) + 1}`) {
                        tab.checked = true;
                    }
                });
            }
        });
    },
    { threshold: 0.3 }
);

targets.forEach((target) => {
    if (target) observer.observe(target);
});

// Swiper Carousel
if (document.querySelector('.swiper-container')) {
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });

    swiper.el.addEventListener('mouseenter', function () {
        swiper.autoplay.stop();
    });

    swiper.el.addEventListener('mouseleave', function () {
        swiper.autoplay.start();
    });
}

// Language Selector
const languageSelect = document.getElementById('languageSelect');
const userPreferredLanguage = localStorage.getItem('language');

async function loadTranslation(lang) {
    try {
        const response = await fetch(`translations/${lang}.json`);
        if (!response.ok) return loadTranslation('en'); // Fallback to English
        const translations = await response.json();
        return translations;
    } catch (error) {
        console.error('Error loading translation file:', error);
    }
}

async function updateContent(lang) {
    const translations = await loadTranslation(lang);
    if (translations) {
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (translations[key]) {
                element.innerHTML = translations[key];
                element.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
            }
        });
    }
}

window.addEventListener('load', () => {
    let preferredLanguage = userPreferredLanguage || (navigator.language.startsWith('ar') ? 'ar' : 'en');
    languageSelect.value = preferredLanguage;
    updateContent(preferredLanguage);
});

languageSelect.addEventListener('change', () => {
    const selectedLanguage = languageSelect.value;
    localStorage.setItem('language', selectedLanguage);
    updateContent(selectedLanguage);
});

// Custom Cursor
const cursor = document.getElementById('cursor');

document.addEventListener('mousemove', (e) => {
    cursor.style.left = `${e.pageX}px`;
    cursor.style.top = `${e.pageY}px`;
});

document.addEventListener('mouseleave', () => {
    cursor.style.opacity = '0';
});

document.addEventListener('mouseenter', () => {
    cursor.style.opacity = '1';
});

document.addEventListener('mousedown', () => {
    cursor.classList.add('click');
});

document.addEventListener('mouseup', () => {
    cursor.classList.remove('click');
});

// Pause Animation When Tab is Inactive
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(rainInterval);
    } else {
        rainInterval = setInterval(drawRain, 70);
    }
});

document.addEventListener('mousemove', (e) => {
    cursor.style.left = `${e.pageX}px`;
    cursor.style.top = `${e.pageY}px`;
});

document.addEventListener('mouseleave', () => {
    cursor.style.opacity = '0';
});

document.addEventListener('mouseenter', () => {
    cursor.style.opacity = '1';
});

document.addEventListener('mouseout', (e) => {
    if (!e.relatedTarget && e.clientY <= 0) {
        cursor.style.opacity = '0';
    }
});

document.addEventListener('mousedown', () => {
    cursor.classList.add('click');
});

document.addEventListener('mouseup', () => {
    cursor.classList.remove('click');
});

