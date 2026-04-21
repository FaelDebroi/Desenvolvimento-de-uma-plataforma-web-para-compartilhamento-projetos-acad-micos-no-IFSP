<?php
class AuthController extends Controller
{
    // ── Login ─────────────────────────────────────────────────

    public function loginForm(array $p = []): void
    {
        $this->requireGuest();
        $this->view('auth.login', [
            'pageTitle' => 'Entrar',
            'flash'     => $this->getFlash(),
            'csrf'      => $this->csrfToken(),
        ]);
    }

    public function login(array $p = []): void
    {
        $this->requireGuest();
        $this->verifyCsrf();

        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        $erros = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'E-mail inválido.';
        }
        if (strlen($senha) < 6) {
            $erros[] = 'Senha deve ter pelo menos 6 caracteres.';
        }

        if (empty($erros)) {
            $usuario = Usuario::findByEmail($email);
            if (!$usuario || !Usuario::verifyPassword($usuario, $senha)) {
                $erros[] = 'E-mail ou senha incorretos.';
            }
        }

        if (!empty($erros)) {
            $this->view('auth.login', [
                'pageTitle' => 'Entrar',
                'erros'     => $erros,
                'email'     => $email,
                'csrf'      => $this->csrfToken(),
            ]);
            return;
        }

        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        $redirect = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);

        $this->flash('success', 'Bem-vindo(a), ' . $usuario['nome'] . '!');
        $this->redirect($redirect ? ltrim(str_replace(BASE_URL, '', $redirect), '/') : '');
    }

    // ── Cadastro ──────────────────────────────────────────────

    public function cadastroForm(array $p = []): void
    {
        $this->requireGuest();
        $this->view('auth.cadastro', [
            'pageTitle' => 'Criar conta',
            'flash'     => $this->getFlash(),
            'csrf'      => $this->csrfToken(),
        ]);
    }

    public function cadastro(array $p = []): void
    {
        $this->requireGuest();
        $this->verifyCsrf();

        $nome   = trim($_POST['nome']   ?? '');
        $email  = strtolower(trim($_POST['email']  ?? ''));
        $senha  = $_POST['senha']  ?? '';
        $senha2 = $_POST['senha2'] ?? '';
        $tipo   = $_POST['tipo']   ?? '';
        $curso  = trim($_POST['curso']  ?? '');
        $erros  = [];

        if (strlen($nome) < 3)                              $erros[] = 'Nome deve ter ao menos 3 caracteres.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $erros[] = 'E-mail inválido.';
        if (strlen($senha) < 6)                             $erros[] = 'Senha deve ter ao menos 6 caracteres.';
        if ($senha !== $senha2)                             $erros[] = 'As senhas não coincidem.';
        if (!in_array($tipo, ['aluno', 'professor']))       $erros[] = 'Tipo de usuário inválido.';
        if (strlen($curso) < 3)                             $erros[] = 'Informe seu curso.';
        if (empty($erros) && Usuario::emailExists($email))  $erros[] = 'Este e-mail já está cadastrado.';

        if (!empty($erros)) {
            $this->view('auth.cadastro', [
                'pageTitle' => 'Criar conta',
                'erros'     => $erros,
                'old'       => compact('nome', 'email', 'tipo', 'curso'),
                'csrf'      => $this->csrfToken(),
            ]);
            return;
        }

        Usuario::create(compact('nome', 'email', 'senha', 'tipo', 'curso'));
        $this->flash('success', 'Conta criada com sucesso! Faça login para continuar.');
        $this->redirect('login');
    }

    // ── Logout ────────────────────────────────────────────────

    public function logout(array $p = []): void
    {
        session_destroy();
        $this->redirect('login');
    }

    // ── Recuperar senha ───────────────────────────────────────

    public function recuperarForm(array $p = []): void
    {
        $this->view('auth.recuperar', [
            'pageTitle' => 'Recuperar senha',
            'flash'     => $this->getFlash(),
            'csrf'      => $this->csrfToken(),
        ]);
    }

    public function recuperar(array $p = []): void
    {
        $this->verifyCsrf();

        $email   = strtolower(trim($_POST['email'] ?? ''));
        $usuario = Usuario::findByEmail($email);

        // Sempre mostrar a mesma mensagem para não vazar se email existe
        $this->flash('success', 'Se o e-mail estiver cadastrado, você receberá as instruções em breve.');

        if ($usuario) {
            $token = RecuperacaoSenha::create((int) $usuario['id']);
            $link  = url('redefinir-senha?token=' . $token);

            $assunto = '[' . SITE_NAME . '] Redefinição de senha';
            $corpo   = "Olá, {$usuario['nome']}!\n\n"
                     . "Clique no link abaixo para redefinir sua senha (válido por 1 hora):\n\n"
                     . $link . "\n\n"
                     . "Se você não solicitou, ignore este e-mail.";

            mail($email, $assunto, $corpo, "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">");
        }

        $this->redirect('recuperar-senha');
    }

    // ── Redefinir senha ───────────────────────────────────────

    public function redefinirForm(array $p = []): void
    {
        $token = $_GET['token'] ?? '';
        $rec   = RecuperacaoSenha::findValid($token);

        if (!$rec) {
            $this->flash('danger', 'Link inválido ou expirado. Solicite um novo.');
            $this->redirect('recuperar-senha');
        }

        $this->view('auth.redefinir', [
            'pageTitle' => 'Redefinir senha',
            'token'     => $token,
            'flash'     => $this->getFlash(),
            'csrf'      => $this->csrfToken(),
        ]);
    }

    public function redefinir(array $p = []): void
    {
        $this->verifyCsrf();

        $token  = $_POST['token']  ?? '';
        $senha  = $_POST['senha']  ?? '';
        $senha2 = $_POST['senha2'] ?? '';
        $rec    = RecuperacaoSenha::findValid($token);

        if (!$rec) {
            $this->flash('danger', 'Link inválido ou expirado.');
            $this->redirect('recuperar-senha');
        }

        $erros = [];
        if (strlen($senha) < 6) $erros[] = 'Senha deve ter ao menos 6 caracteres.';
        if ($senha !== $senha2)  $erros[] = 'As senhas não coincidem.';

        if (!empty($erros)) {
            $this->view('auth.redefinir', [
                'pageTitle' => 'Redefinir senha',
                'token'     => $token,
                'erros'     => $erros,
                'csrf'      => $this->csrfToken(),
            ]);
            return;
        }

        Usuario::updatePassword((int) $rec['usuario_id'], $senha);
        RecuperacaoSenha::markUsed($token);

        $this->flash('success', 'Senha redefinida com sucesso! Faça login.');
        $this->redirect('login');
    }
}
