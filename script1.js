const languageSelect = document.getElementById('languageSelect');
const userPreferredLanguage = localStorage.getItem('language');

// دالة لتحميل ملف الترجمة
async function loadTranslation(lang) {
    try {
        const response = await fetch(`translations/${lang}.json`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const translations = await response.json();
        return translations;
    } catch (error) {
        console.error('Error loading translation file:', error);
    }
}

// دالة لتحديث النصوص بناءً على اللغة المحددة
async function updateContent(lang) {
    const translations = await loadTranslation(lang);
    if (translations) {
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (translations[key]) {
                element.innerHTML = translations[key]; // استخدم innerHTML لتحديث النصوص
                element.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr'); // تحديد اتجاه النص
            }
        });
    }
}

// تحميل اللغة المفضلة عند فتح الصفحة
window.addEventListener('load', () => {
    let preferredLanguage = userPreferredLanguage;

    if (!preferredLanguage) {
        const systemLanguage = navigator.language || navigator.languages[0];
        preferredLanguage = systemLanguage.startsWith('ar') ? 'ar' : 'en'; // اختر اللغة بناءً على إعدادات النظام
    }

    languageSelect.value = preferredLanguage; // قم بتحديث القائمة المنسدلة
    updateContent(preferredLanguage); // حدث المحتوى بناءً على اللغة المفضلة
});

// تغيير اللغة عند اختيار لغة جديدة
languageSelect.addEventListener('change', () => {
    const selectedLanguage = languageSelect.value;
    localStorage.setItem('language', selectedLanguage); // حفظ اللغة المفضلة
    updateContent(selectedLanguage); // تحديث المحتوى
});
