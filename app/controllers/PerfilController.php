<?php
class PerfilController extends Controller
{
    public function show(array $p = []): void
    {
        $usuario = Usuario::find((int) $p['id']);
        if (!$usuario) {
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            return;
        }

        $this->view('perfil.show', [
            'pageTitle' => h($usuario['nome']) . ' — ' . SITE_NAME,
            'perfil'    => $usuario,
            'projetos'  => Projeto::findByUsuario((int) $usuario['id']),
            'flash'     => $this->getFlash(),
            'usuario'   => $this->currentUser(),
        ]);
    }

    public function edit(array $p = []): void
    {
        $this->requireAuth();
        $usuario = $this->currentUser();

        $this->view('perfil.edit', [
            'pageTitle' => 'Editar perfil',
            'usuario'   => $usuario,
            'flash'     => $this->getFlash(),
            'csrf'      => $this->csrfToken(),
        ]);
    }

    public function update(array $p = []): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $usuario = $this->currentUser();
        $nome    = trim($_POST['nome']     ?? '');
        $curso   = trim($_POST['curso']    ?? '');
        $bio     = trim($_POST['bio']      ?? '');
        $linkedin= trim($_POST['linkedin'] ?? '');
        $github  = trim($_POST['github']   ?? '');
        $erros   = [];

        if (strlen($nome) < 3)  $erros[] = 'Nome deve ter ao menos 3 caracteres.';
        if (strlen($curso) < 3) $erros[] = 'Informe seu curso.';
        if ($linkedin && !filter_var($linkedin, FILTER_VALIDATE_URL)) $erros[] = 'URL do LinkedIn inválida.';
        if ($github   && !filter_var($github,   FILTER_VALIDATE_URL)) $erros[] = 'URL do GitHub inválida.';

        // Trocar senha (opcional)
        $senhaAtual = $_POST['senha_atual']  ?? '';
        $novaSenha  = $_POST['nova_senha']   ?? '';
        $novaSenha2 = $_POST['nova_senha2']  ?? '';
        $trocarSenha = !empty($senhaAtual) || !empty($novaSenha);

        if ($trocarSenha) {
            if (!Usuario::verifyPassword($usuario, $senhaAtual)) {
                $erros[] = 'Senha atual incorreta.';
            } elseif (strlen($novaSenha) < 6) {
                $erros[] = 'Nova senha deve ter ao menos 6 caracteres.';
            } elseif ($novaSenha !== $novaSenha2) {
                $erros[] = 'As novas senhas não coincidem.';
            }
        }

        if (!empty($erros)) {
            $this->view('perfil.edit', [
                'pageTitle' => 'Editar perfil',
                'erros'     => $erros,
                'usuario'   => array_merge($usuario, compact('nome','curso','bio','linkedin','github')),
                'csrf'      => $this->csrfToken(),
            ]);
            return;
        }

        // Upload de foto de perfil
        $foto = null;
        if (!empty($_FILES['foto_perfil']['name'])) {
            $foto = upload_file($_FILES['foto_perfil'], 'fotos', ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
            if (!$foto) {
                $erros[] = 'Foto inválida (use JPG/PNG, máx. 5 MB).';
                $this->view('perfil.edit', [
                    'pageTitle' => 'Editar perfil',
                    'erros'     => $erros,
                    'usuario'   => $usuario,
                    'csrf'      => $this->csrfToken(),
                ]);
                return;
            }
            // Apagar foto antiga
            if ($usuario['foto_perfil']) {
                $old = UPLOAD_PATH . '/' . $usuario['foto_perfil'];
                if (file_exists($old)) unlink($old);
            }
        }

        $data = compact('nome', 'curso', 'bio', 'linkedin', 'github');
        if ($foto) $data['foto_perfil'] = $foto;

        Usuario::update((int) $usuario['id'], $data);

        if ($trocarSenha) {
            Usuario::updatePassword((int) $usuario['id'], $novaSenha);
        }

        // Atualizar nome na sessão
        $_SESSION['usuario_nome'] = $nome;

        $this->flash('success', 'Perfil atualizado com sucesso!');
        $this->redirect('perfil/' . $usuario['id']);
    }
}
