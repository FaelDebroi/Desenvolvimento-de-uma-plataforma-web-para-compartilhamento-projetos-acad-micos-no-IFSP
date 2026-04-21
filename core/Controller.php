<?php
class Controller
{
    // ── Rendering ─────────────────────────────────────────────

    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $path = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($path)) {
            throw new RuntimeException("View não encontrada: {$path}");
        }
        require $path;
    }

    protected function redirect(string $path = ''): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    // ── Auth guards ───────────────────────────────────────────

    protected function requireAuth(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->flash('warning', 'Faça login para continuar.');
            $this->redirect('login');
        }
    }

    protected function requireGuest(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }

    protected function currentUser(): ?array
    {
        if (empty($_SESSION['usuario_id'])) return null;
        return Usuario::find((int) $_SESSION['usuario_id']);
    }

    // ── Flash messages ────────────────────────────────────────

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = compact('type', 'message');
    }

    public function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) return null;
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    // ── CSRF ──────────────────────────────────────────────────

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Token de segurança inválido. Volte e tente novamente.');
        }
    }

    // ── JSON helper ───────────────────────────────────────────

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
