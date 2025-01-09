const themeToggle = document.getElementById('switch');
const currentTheme = localStorage.getItem('theme');

if (currentTheme === 'dark') {
    document.body.classList.add('dark-theme');
    themeToggle.textContent = 'Light Mode';
}

themeToggle.addEventListener('click', function () {
    document.body.classList.toggle('dark-theme');
    const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
    this.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
});
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