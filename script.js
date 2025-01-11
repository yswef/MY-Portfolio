const themeToggle = document.getElementById('switch');
const currentTheme = localStorage.getItem('theme');

if (currentTheme === 'dark') {
    document.body.classList.add('dark-theme');
    // themeToggle.textContent = 'Light Mode';
}

themeToggle.addEventListener('click', function () {
    document.body.classList.toggle('dark-theme');
    const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
    // this.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
});
const canvas = document.getElementById('codeRainCanvas');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const fontSize = 16;
        const columns = Math.floor(canvas.width / fontSize);
        const drops = Array(columns).fill(1);
        const codes = ['HTML', 'CSS', 'JS', 'Python', 'Code', 'if', 'else', '<>', '{}','print'];

        function drawRain() {
            const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
            ctx.fillStyle = theme === 'dark' ? 'rgba(51, 51, 51, 0.1)' : 'rgba(244, 244, 244, 0.1)';
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

        function toggleTheme() {
            document.body.classList.toggle('dark-theme');
            document.body.classList.toggle('light-theme');
        }

        setInterval(drawRain, 45);

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
        
        // تحديد جميع العناصر التي تريد مراقبتها
const targets = [
    document.getElementById("Abut_me"),   // القسم الأول باستخدام id
    document.querySelector(".title"),    // القسم الثاني باستخدام class
    document.querySelector("footer")     // القسم الثالث باستخدام نوع العنصر
];

// تحديد الأزرار الخاصة بالأقسام
const tabs = document.querySelectorAll(".tab");

// مراقبة العناصر باستخدام Intersection Observer
const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // العثور على العنصر المرئي
                const activeElement = entry.target;

                // تحديث التحديد في الأزرار
                tabs.forEach((tab) => {
                    // تحقق إذا كان هذا الزر مرتبطًا بالعنصر المرئي
                    if (tab.id === `tab${targets.indexOf(activeElement) + 1}`) {
                        tab.checked = true; // تفعيل الزر المرتبط
                    }
                });
            }
        });
    },
    {
        threshold: 0.6, // التفعيل عند ظهور 60% من العنصر
    }
);

// مراقبة كل عنصر في قائمة الأهداف
targets.forEach((target) => {
    if (target) observer.observe(target);
});


//يجب ان يكون اخر قسم لكي لا يتعطل الموقع اثناء تشغيلة اوفلاين 
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
// إيقاف التمرير عند الضغط بالماوس
swiper.el.addEventListener('mouseenter', function () {
    swiper.autoplay.stop(); // إيقاف التمرير التلقائي عند الماوس فوق العنصر
});

swiper.el.addEventListener('mouseleave', function () {
    swiper.autoplay.start(); // إعادة تشغيل التمرير التلقائي عند مغادرة الماوس
});