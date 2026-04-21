<?php
class ProjetoController extends Controller
{
    // ── Visualizar ────────────────────────────────────────────

    public function show(array $p = []): void
    {
        $projeto = Projeto::find((int) $p['id']);
        if (!$projeto) {
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            return;
        }

        Projeto::incrementViews((int) $projeto['id']);

        $this->view('projetos.show', [
            'pageTitle' => h($projeto['titulo']) . ' — ' . SITE_NAME,
            'projeto' => $projeto,
            'arquivos' => Arquivo::findByProjeto((int) $projeto['id']),
            'comentarios' => Comentario::findByProjeto((int) $projeto['id']),
            'flash' => $this->getFlash(),
            'usuario' => $this->currentUser(),
            'csrf' => $this->csrfToken(),
        ]);
    }

    // ── Criar ─────────────────────────────────────────────────

    public function create(array $p = []): void
    {
        $this->requireAuth();
        $this->view('projetos.create', [
            'pageTitle' => 'Novo projeto',
            'tecnologias' => Projeto::getAllTecnologias(),
            'flash' => $this->getFlash(),
            'usuario' => $this->currentUser(),
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function store(array $p = []): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $usuario = $this->currentUser();
        $erros = $this->validateProjetoForm();

        if (!empty($erros)) {
            $this->view('projetos.create', [
                'pageTitle' => 'Novo projeto',
                'erros' => $erros,
                'old' => $_POST,
                'tecnologias' => Projeto::getAllTecnologias(),
                'usuario' => $usuario,
                'csrf' => $this->csrfToken(),
            ]);
            return;
        }

        $capa = null;
        if (!empty($_FILES['imagem_capa']['name'])) {
            $capa = upload_file($_FILES['imagem_capa'], 'capas', ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
            if (!$capa) {
                $erros[] = 'Imagem de capa inválida (use JPG/PNG, máx. 200 MB).';
            }
        }

        if (!empty($erros)) {
            $this->view('projetos.create', [
                'pageTitle' => 'Novo projeto',
                'erros' => $erros,
                'old' => $_POST,
                'tecnologias' => Projeto::getAllTecnologias(),
                'usuario' => $usuario,
                'csrf' => $this->csrfToken(),
            ]);
            return;
        }

        $tags = $this->parseTecnologias($_POST['tags'] ?? '');

        $id = Projeto::create([
            'usuario_id' => $usuario['id'],
            'titulo' => $_POST['titulo'],
            'descricao' => $_POST['descricao'],
            'area' => $_POST['area'] ?? '',
            'status' => $_POST['status'],
            'repositorio' => $_POST['repositorio'] ?? '',
            'imagem_capa' => $capa,
            'tecnologias' => $tags,
        ]);

        // Anexos opcionais
        $this->handleFileUploads($id);

        $this->flash('success', 'Projeto publicado com sucesso!');
        $this->redirect("projeto/{$id}");
    }

    // ── Editar ────────────────────────────────────────────────

    public function edit(array $p = []): void
    {
        $this->requireAuth();
        $projeto = $this->findAndAuthorize((int) $p['id']);

        $this->view('projetos.edit', [
            'pageTitle' => 'Editar projeto',
            'projeto' => $projeto,
            'arquivos' => Arquivo::findByProjeto((int) $projeto['id']),
            'tecnologias' => Projeto::getAllTecnologias(),
            'flash' => $this->getFlash(),
            'usuario' => $this->currentUser(),
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function update(array $p = []): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $projeto = $this->findAndAuthorize((int) $p['id']);
        $erros = $this->validateProjetoForm();

        if (!empty($erros)) {
            $this->view('projetos.edit', [
                'pageTitle' => 'Editar projeto',
                'erros' => $erros,
                'projeto' => array_merge($projeto, $_POST),
                'arquivos' => Arquivo::findByProjeto((int) $projeto['id']),
                'tecnologias' => Projeto::getAllTecnologias(),
                'usuario' => $this->currentUser(),
                'csrf' => $this->csrfToken(),
            ]);
            return;
        }

        $capa = null;
        if (!empty($_FILES['imagem_capa']['name'])) {
            $capa = upload_file($_FILES['imagem_capa'], 'capas', ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
            if (!$capa) {
                $erros[] = 'Imagem de capa inválida (use JPG/PNG, máx. 250 MB).';
            }
        }

        $tags = $this->parseTecnologias($_POST['tags'] ?? '');

        Projeto::update((int) $projeto['id'], [
            'titulo' => $_POST['titulo'],
            'descricao' => $_POST['descricao'],
            'area' => $_POST['area'] ?? '',
            'status' => $_POST['status'],
            'repositorio' => $_POST['repositorio'] ?? '',
            'imagem_capa' => $capa,
            'tecnologias' => $tags,
        ]);

        $this->handleFileUploads((int) $projeto['id']);

        // Remover arquivos marcados para deleção
        foreach ($_POST['remover_arquivo'] ?? [] as $arquivoId) {
            $arq = Arquivo::find((int) $arquivoId);
            if ($arq && $arq['projeto_id'] == $projeto['id']) {
                Arquivo::delete((int) $arquivoId);
            }
        }

        $this->flash('success', 'Projeto atualizado com sucesso!');
        $this->redirect("projeto/{$projeto['id']}");
    }

    // ── Deletar ───────────────────────────────────────────────

    public function delete(array $p = []): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $projeto = $this->findAndAuthorize((int) $p['id']);
        Projeto::delete((int) $projeto['id']);

        $this->flash('success', 'Projeto removido.');
        $this->redirect('');
    }

    // ── Comentários ───────────────────────────────────────────

    public function comentar(array $p = []): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $projeto = Projeto::find((int) $p['id']);
        $conteudo = trim($_POST['conteudo'] ?? '');

        if (!$projeto || strlen($conteudo) < 3) {
            $this->flash('danger', 'Comentário inválido.');
            $this->redirect("projeto/{$p['id']}");
            return;
        }

        Comentario::create((int) $projeto['id'], (int) $_SESSION['usuario_id'], $conteudo);
        $this->flash('success', 'Comentário adicionado.');
        $this->redirect("projeto/{$p['id']}#comentarios");
    }

    public function deletarComentario(array $p = []): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $com = Comentario::find((int) $p['id']);
        if (!$com) {
            $this->redirect('');
            return;
        }

        $uid = (int) $_SESSION['usuario_id'];
        $tipo = $_SESSION['usuario_tipo'];

        // Pode deletar se for o autor do comentário ou professor
        if ($com['usuario_id'] == $uid || $tipo === 'professor') {
            Comentario::delete((int) $com['id']);
            $this->flash('success', 'Comentário removido.');
        }

        $this->redirect("projeto/{$com['projeto_id']}#comentarios");
    }

    // ── Helpers ───────────────────────────────────────────────

    private function findAndAuthorize(int $id): array
    {
        $projeto = Projeto::find($id);
        if (!$projeto) {
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            exit;
        }
        if ($projeto['usuario_id'] != $_SESSION['usuario_id']) {
            $this->flash('danger', 'Você não tem permissão para esta ação.');
            $this->redirect("projeto/{$id}");
        }
        return $projeto;
    }

    private function validateProjetoForm(): array
    {
        $erros = [];
        if (strlen(trim($_POST['titulo'] ?? '')) < 3)
            $erros[] = 'Título deve ter ao menos 3 caracteres.';
        if (strlen(trim($_POST['descricao'] ?? '')) < 10)
            $erros[] = 'Descrição deve ter ao menos 10 caracteres.';
        if (!in_array($_POST['status'] ?? '', ['em_desenvolvimento', 'beta', 'concluido'])) {
            $erros[] = 'Status inválido.';
        }
        $repo = trim($_POST['repositorio'] ?? '');
        if ($repo && !filter_var($repo, FILTER_VALIDATE_URL)) {
            $erros[] = 'Link do repositório inválido.';
        }
        return $erros;
    }

    private function parseTecnologias(string $raw): array
    {
        return array_filter(array_map('trim', explode(',', $raw)));
    }

    private function handleFileUploads(int $projetoId): void
    {
        if (empty($_FILES['arquivos']['name'][0]))
            return;

        $files = $_FILES['arquivos'];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK)
                continue;

            $file = [
                'name' => $files['name'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'size' => $files['size'][$i],
                'error' => $files['error'][$i],
            ];

            $caminho = upload_file($file, 'arquivos', ALLOWED_FILE_TYPES, MAX_FILE_SIZE);
            if ($caminho) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']) ?: 'application/octet-stream';
                finfo_close($finfo);

                Arquivo::create($projetoId, [
                    'nome_original' => $file['name'],
                    'caminho' => $caminho,
                    'tipo_mime' => $mime,
                    'tamanho_kb' => (int) ceil($file['size'] / 1024),
                ]);
            }
        }
    }
}
