<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
start_secure_session();
logout();
header('Location: index.php');
exit;
