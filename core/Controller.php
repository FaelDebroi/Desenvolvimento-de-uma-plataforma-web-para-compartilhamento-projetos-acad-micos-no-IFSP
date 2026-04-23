<?php
class Controller
{
    // ── Rendering ─────────────────────────────────────────────

    // Renderiza uma view com os dados fornecidos
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $path = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($path)) {
            throw new RuntimeException("View não encontrada: {$path}");
        }
        require $path;
    }
    // Redireciona para outra rota
    protected function redirect(string $path = ''): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    // ── Auth guards ───────────────────────────────────────────
    // Verifica se o usuário está autenticado, caso contrário redireciona para login
    protected function requireAuth(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->flash('warning', 'Faça login para continuar.');
            $this->redirect('login');
        }
    }
    // Verifica se o usuário é convidado (não autenticado), caso contrário redireciona para a página inicial
    protected function requireGuest(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    // Retorna os dados do usuário atualmente autenticado, ou null se não houver
    protected function currentUser(): ?array
    {
        if (empty($_SESSION['usuario_id'])) return null;
        return Usuario::find((int) $_SESSION['usuario_id']);
    }

    // ── Flash messages ────────────────────────────────────────
    // Define uma mensagem flash para ser exibida na próxima requisição
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = compact('type', 'message');
    }
    // Obtém e limpa a mensagem flash da sessão
    public function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) return null;
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    // ── CSRF ──────────────────────────────────────────────────
    // Gera um token CSRF e o armazena na sessão
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    // Verifica se o token CSRF enviado no formulário é válido
    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Token de segurança inválido. Volte e tente novamente.');
        }
    }

    // ── JSON helper ───────────────────────────────────────────
    // Envia uma resposta JSON com os dados fornecidos e o status HTTP especificado
    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
