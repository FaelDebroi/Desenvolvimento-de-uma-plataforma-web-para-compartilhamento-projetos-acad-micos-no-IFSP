<?php

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return BASE_URL . '/public/' . ltrim($path, '/');
}

function upload_url(string $path): string
{
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

function time_ago(string $datetime): string
{
    $diff = (new DateTime())->diff(new DateTime($datetime));
    if ($diff->y > 0) return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
    if ($diff->m > 0) return $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '') . ' atrás';
    if ($diff->d > 0) return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
    if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
    if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
    return 'agora mesmo';
}

function status_label(string $status): string
{
    return match ($status) {
        'em_desenvolvimento' => 'Em desenvolvimento',
        'beta'               => 'Beta',
        'concluido'          => 'Concluído',
        default              => $status,
    };
}

function status_class(string $status): string
{
    return match ($status) {
        'em_desenvolvimento' => 'badge-warning',
        'beta'               => 'badge-info',
        'concluido'          => 'badge-success',
        default              => 'badge-secondary',
    };
}

function tipo_label(string $tipo): string
{
    return match ($tipo) {
        'aluno'     => 'Aluno',
        'professor' => 'Professor',
        default     => $tipo,
    };
}

function format_bytes(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

function upload_file(array $file, string $dir, array $allowedTypes, int $maxSize): string|false
{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > $maxSize)         return false;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes, true)) return false;

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destDir  = UPLOAD_PATH . '/' . $dir;

    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
        return false;
    }

    return $dir . '/' . $filename;
}

function paginate(int $total, int $perPage, int $current): array
{
    $totalPages = (int) ceil($total / max(1, $perPage));
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $current,
        'total_pages' => $totalPages,
        'has_prev'    => $current > 1,
        'has_next'    => $current < $totalPages,
        'prev'        => $current - 1,
        'next'        => $current + 1,
    ];
}

function csrf_field(string $token): string
{
    return '<input type="hidden" name="csrf_token" value="' . h($token) . '">';
}

function avatar_initials(string $nome): string
{
    $parts = explode(' ', trim($nome));
    $ini   = mb_substr($parts[0], 0, 1);
    if (count($parts) > 1) {
        $ini .= mb_substr(end($parts), 0, 1);
    }
    return mb_strtoupper($ini);
}
