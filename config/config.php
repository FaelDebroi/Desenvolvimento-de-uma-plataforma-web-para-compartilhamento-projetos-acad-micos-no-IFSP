<?php
// ── Database ──────────────────────────────────────────────────
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3308');
define('DB_NAME', 'tcc_ifsp');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ── Application ───────────────────────────────────────────────
define('BASE_URL', 'http://localhost/Desenvolvimento-de-uma-plataforma-web-para-compartilhamento-projetos-acad-micos-no-IFSP');
define('BASE_PATH', '/Desenvolvimento-de-uma-plataforma-web-para-compartilhamento-projetos-acad-micos-no-IFSP');
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
