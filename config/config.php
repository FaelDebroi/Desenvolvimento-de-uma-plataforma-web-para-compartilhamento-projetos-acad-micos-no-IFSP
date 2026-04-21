<?php
// ── Database ──────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'tcc_ifsp');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ── Application ───────────────────────────────────────────────
define('BASE_URL', 'http://localhost/tcc');
define('BASE_PATH', '/tcc');
define('SITE_NAME', 'IFSP Projetos');

// ── Uploads ───────────────────────────────────────────────────
define('UPLOAD_PATH', ROOT . '/public/uploads');
define('UPLOAD_URL', BASE_URL . '/public/uploads');

define('MAX_FILE_SIZE', 250 * 1024 * 1024);  // 250 MB
define('MAX_IMAGE_SIZE', 250 * 1024 * 1024); // 250 MB

define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/zip',
    'text/plain',
]);

// ── Mail ──────────────────────────────────────────────────────
define('MAIL_FROM', 'noreply@ifsp.edu.br');
define('MAIL_FROM_NAME', 'IFSP Projetos');
