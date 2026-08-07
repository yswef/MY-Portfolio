<?php
/**
 * طبقة الاتصال بقاعدة البيانات (SQLite عبر PDO).
 * تُنشئ الجداول تلقائيًا في أول تشغيل إذا لم تكن موجودة (idempotent).
 */

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dbPath = __DIR__ . '/../data/portfolio.sqlite';
    $dbExists = file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    if (!$dbExists) {
        init_schema($pdo);
    }

    return $pdo;
}

function init_schema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title_ar TEXT NOT NULL,
            title_en TEXT,
            title_es TEXT,
            title_fr TEXT,
            title_ja TEXT,
            desc_ar TEXT NOT NULL,
            desc_en TEXT,
            desc_es TEXT,
            desc_fr TEXT,
            desc_ja TEXT,
            image TEXT,
            github_url TEXT,
            download_url TEXT,
            sort_order INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS site_settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT
        );

        CREATE TABLE IF NOT EXISTS page_views (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page TEXT,
            viewed_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            attempted_at TEXT DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // قيم افتراضية أولية للسيرة الذاتية (نفس النصوص الحالية) — قابلة للتعديل من لوحة التحكم لاحقًا
    $defaults = [
        'bio_intro_ar' => 'يوسف الحمزي، مهندس برمجيات شغوف بعمره 20 عامًا، متخصص في تطوير الأنظمة بلغة Python، ومواقع WordPress، وأنظمة ERP.',
        'bio_intro_en' => 'Yusef Alhmzy, a passionate 20-year-old software engineer specializing in Python systems development, WordPress websites, and ERP systems.',
        'bio_dream_ar' => 'أحب التحديات التقنية وأسعى دائمًا لتعلّم أدوات جديدة والتطور في مسيرتي.',
        'bio_dream_en' => 'I love technical challenges and I am always striving to learn new tools and grow in my career.',
        'site_name' => 'Yusef Alhmzy',
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES (:k, :v)');
    foreach ($defaults as $k => $v) {
        $stmt->execute(['k' => $k, 'v' => $v]);
    }

    // مشاريع افتراضية أولية — قابلة للتعديل/الحذف بالكامل من لوحة التحكم
    $projectCount = (int) $pdo->query('SELECT COUNT(*) c FROM projects')->fetch()['c'];
    if ($projectCount === 0) {
        $seedImagesSource = __DIR__ . '/../assets/img/';
        $uploadsDir = __DIR__ . '/../assets/img/uploads/';
        foreach (['p1.jpg' => 'seed-p1.jpg', 'p2.jpg' => 'seed-p2.jpg', 'p3.jpg' => 'seed-p3.jpg'] as $src => $dest) {
            $srcPath = $seedImagesSource . $src;
            $destPath = $uploadsDir . $dest;
            if (is_file($srcPath) && !is_file($destPath)) {
                @copy($srcPath, $destPath);
            }
        }

        $seedProjects = [
            [
                'MailboxSplitter', 'MailboxSplitter', 'MailboxSplitter', 'MailboxSplitter', 'MailboxSplitter',
                'أداة كونسول بلغة بايثون تعالج وتنظم ملفات .inbox، وتقسّم البريد إلى ملفات مصنّفة حسب المرسل أو الموضوع أو التاريخ.',
                'A Python console app that processes and organizes .inbox files, splitting mail into categorized files by sender, subject, or date.',
                'Aplicación de consola en Python que organiza archivos .inbox, dividiendo el correo por remitente, asunto o fecha.',
                'Application console Python qui organise les fichiers .inbox par expéditeur, sujet ou date.',
                '送信者、件名、日付でメールボックスを整理するPythonコンソールアプリ。',
                'seed-p1.jpg', 'https://github.com/yswef', null, 1, 1,
            ],
            [
                'تطبيق إدارة المهام والإنتاجية', 'Task Management App', 'Gestión de Tareas', 'Gestion des Tâches', 'タスク管理アプリ',
                'تطبيق كونسول لإدارة المهام وتتبع الوقت وزيادة الإنتاجية، بميزات مثل مؤقت بومودورو وتقارير الإنتاجية.',
                'A console app for task management, time tracking, and productivity, with a Pomodoro timer and productivity reports.',
                'Aplicación de consola para gestión de tareas y productividad, con temporizador Pomodoro.',
                'Application console pour la gestion des tâches et la productivité, avec minuteur Pomodoro.',
                'タスク管理と生産性向上のためのコンソールアプリ。ポモドーロタイマー付き。',
                'seed-p2.jpg', 'https://github.com/yswef', null, 2, 1,
            ],
            [
                'PassSafe', 'PassSafe', 'PassSafe', 'PassSafe', 'PassSafe',
                'أداة تفحص قوة كلمات المرور وتساعدك على إنشاء كلمات مرور آمنة بسهولة مع ملاحظات فورية.',
                'A tool that checks password strength and helps you create secure passwords with instant feedback.',
                'Herramienta que verifica la seguridad de tus contraseñas.',
                'Outil qui vérifie la sécurité de vos mots de passe.',
                'パスワードの強度をチェックするツール。',
                'seed-p3.jpg', 'https://github.com/yswef', null, 3, 1,
            ],
        ];

        $insert = $pdo->prepare('INSERT INTO projects
            (title_ar, title_en, title_es, title_fr, title_ja, desc_ar, desc_en, desc_es, desc_fr, desc_ja,
             image, github_url, download_url, sort_order, is_published)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        foreach ($seedProjects as $row) {
            $insert->execute($row);
        }
    }
}

function get_setting(string $key, string $default = ''): string
{
    $stmt = db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key = :k');
    $stmt->execute(['k' => $key]);
    $row = $stmt->fetch();
    return $row ? (string) $row['setting_value'] : $default;
}

function set_setting(string $key, string $value): void
{
    $stmt = db()->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (:k, :v)
         ON CONFLICT(setting_key) DO UPDATE SET setting_value = :v'
    );
    $stmt->execute(['k' => $key, 'v' => $value]);
}
