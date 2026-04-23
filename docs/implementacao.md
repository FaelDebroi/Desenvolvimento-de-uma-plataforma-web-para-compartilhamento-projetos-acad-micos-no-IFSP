# Implementação — Plataforma de Projetos Acadêmicos IFSP

> Este documento detalha os arquivos mais relevantes do sistema para fins de documentação técnica do TCC.
> Cada seção descreve **o que o arquivo faz**, **por que foi estruturado assim** e **os trechos de código mais significativos**.

---

## Sumário

1. [index.php — Ponto de entrada](#1-indexphp--ponto-de-entrada)
2. [core/Database.php — Conexão com o banco](#2-coredatabasephp--conexão-com-o-banco)
3. [core/Router.php — Roteamento de URLs](#3-corerouterphp--roteamento-de-urls)
4. [core/Controller.php — Controlador base](#4-corecontrollerphp--controlador-base)
5. [app/models/Projeto.php — Model principal](#5-appmodelsprojeto-php--model-principal)
6. [app/controllers/ProjetoController.php — Controller principal](#6-appcontrollersproje-tocontrollerphp--controller-principal)

---

## 1. `index.php` — Ponto de entrada

### O que é

O `index.php` é o único arquivo acessado diretamente pelo servidor web. Todas as requisições HTTP da aplicação passam por ele antes de qualquer outra coisa. Esse padrão é chamado de **Front Controller**.

### Por que foi feito assim

Em vez de ter um arquivo PHP separado para cada página (como `login.php`, `projetos.php`, `perfil.php`), toda a aplicação tem um único ponto de entrada. Isso traz duas vantagens diretas:

- **Controle centralizado:** qualquer lógica que precisa rodar em todas as requisições (iniciar a sessão, carregar configurações, registrar dependências) fica em um só lugar.
- **URLs limpas:** o servidor redireciona `/projeto/5` para `index.php`, que interpreta o caminho e decide o que executar — sem expor a estrutura de arquivos do servidor.

### Como funciona

```php
<?php
declare(strict_types=1);

// Define constantes de caminhos absolutos no servidor
define('ROOT',       __DIR__);
define('APP_PATH',   ROOT . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('CORE_PATH',  ROOT . '/core');

// Carrega configurações e todas as classes necessárias
require_once ROOT . '/config/config.php';
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Router.php';
require_once ROOT . '/helpers.php';

// Carrega todos os Models e Controllers dinamicamente
foreach (['Usuario','Projeto','Comentario','Arquivo','RecuperacaoSenha'] as $m) {
    require_once APP_PATH . "/models/{$m}.php";
}
foreach (['AuthController','HomeController','ProjetoController','PerfilController'] as $c) {
    require_once APP_PATH . "/controllers/{$c}.php";
}

// Inicia a sessão PHP (necessário para login e CSRF)
session_start();

// Cria o roteador e despacha a requisição atual
$router = new Router();
$router->dispatch();
```

O arquivo é intencionalmente curto. Sua responsabilidade é apenas **montar o ambiente** e **delegar** para o roteador. Toda a lógica de negócio fica nos Controllers e Models.

---

## 2. `

### O que é

A classe `Database` é responsável por criar e fornecer a conexão com o banco de dados MySQL. Ela implementa o padrão de projeto **Singleton**, garantindo que apenas uma conexão PDO seja criada durante toda a execução da requisição.

### Por que foi feito assim

Abrir uma conexão com o banco de dados tem um custo de processamento. Se cada Model criasse sua própria conexão, uma única página que acessa usuários, projetos e comentários abriria três conexões separadas desnecessariamente. Com o Singleton, a conexão é criada uma vez e reutilizada por todas as classes que precisarem dela.

A escolha do **PDO** (PHP Data Objects) em vez de funções nativas `mysqli_*` se justifica por:

- **Prepared statements nativos**, que eliminam a possibilidade de injeção de SQL ao separar o comando SQL dos dados fornecidos pelo usuário.
- **Independência de banco de dados**, uma vez que PDO suporta MySQL, PostgreSQL e outros com a mesma interface.
- **Modo de erro por exceção**, que garante que falhas de banco de dados não passem silenciosas.

### Como funciona

```php
<?php
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}   // Impede instanciação direta

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET   // Constantes definidas em config.php
            );
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lança exceção em erros
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retorna arrays associativos
                PDO::ATTR_EMULATE_PREPARES   => false,                   // Prepared statements reais
            ]);
        }
        return self::$instance;
    }
}
```

O construtor privado impede que qualquer parte do código faça `new Database()`. A única forma de obter a conexão é chamando `Database::getInstance()`, que retorna sempre o mesmo objeto PDO.

---

## 3. `core/Router.php` — Roteamento de URLs

### O que é

O `Router` é o componente que analisa a URL da requisição e decide qual Controller e qual método devem ser executados. Ele foi desenvolvido do zero, sem bibliotecas externas.

### Por que foi feito assim

O roteamento customizado permite URLs semânticas como `/projeto/42/editar` em vez de `/index.php?acao=editar&id=42`. Além de ser mais legível para o usuário, URLs limpas são uma prática recomendada de desenvolvimento web.

A implementação usa **expressões regulares** para identificar parâmetros dinâmicos nas rotas (como `{id}`), convertendo-os em grupos de captura e extraindo os valores automaticamente.

### Como funciona

**Registro das rotas:**

```php
private function registerRoutes(): void
{
    // Cada rota associa: método HTTP + caminho URL → Controller + método PHP
    $this->add('GET',  '',                     'HomeController',    'index');
    $this->add('GET',  'projeto/{id}',         'ProjetoController', 'show');
    $this->add('GET',  'projeto/{id}/editar',  'ProjetoController', 'edit');
    $this->add('POST', 'projeto/{id}/editar',  'ProjetoController', 'update');
    $this->add('POST', 'projeto/{id}/deletar', 'ProjetoController', 'delete');
    // ... demais rotas
}
```

**Despacho da requisição:**

```php
public function dispatch(): void
{
    $method = $_SERVER['REQUEST_METHOD'];   // GET, POST, etc.
    $uri    = $_SERVER['REQUEST_URI'];

    // Remove o prefixo base (/tcc) e a query string (?busca=...)
    $uri = preg_replace('#^' . preg_quote(BASE_PATH, '#') . '#', '', $uri);
    $uri = strtok($uri, '?');
    $uri = trim($uri, '/');

    foreach ($this->routes as $route) {
        if ($route['method'] !== $method) continue;

        // Converte {id} em grupo de captura regex: ([^/]+)
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) continue;

        // Extrai os valores dos parâmetros dinâmicos pelo nome
        array_shift($matches);
        preg_match_all('/\{([^}]+)\}/', $route['path'], $paramNames);
        $params = array_combine($paramNames[1], $matches);

        // Instancia o controller e chama o método correspondente
        $ctrl = new $route['controller']();
        $ctrl->{$route['action']}($params);
        return;
    }

    // Nenhuma rota correspondeu: retorna 404
    http_response_code(404);
    require VIEWS_PATH . '/errors/404.php';
}
```

Quando a URL `/projeto/42/editar` é acessada via GET, o Router identifica a rota `'GET', 'projeto/{id}/editar'`, extrai `['id' => '42']` e chama `ProjetoController::edit(['id' => '42'])`.

---

## 4. `core/Controller.php` — Controlador base

### O que é

A classe `Controller` é a superclasse de todos os Controllers da aplicação. Ela concentra funcionalidades que todos os controladores precisam: renderizar views, redirecionar, controlar acesso, enviar mensagens temporárias e proteger formulários contra ataques CSRF.

### Por que foi feito assim

Ao invés de repetir o mesmo código de verificação de login em cada controller, toda essa lógica fica centralizada aqui. Os controllers específicos herdam da classe base com `extends Controller` e utilizam esses métodos diretamente.

### Funcionalidades principais

#### Renderização de views

```php
protected function view(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);   // Transforma array em variáveis ($projeto, $usuario, etc.)
    $path = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
    require $path;
}
```

O método `view('projetos.show', ['projeto' => $dados])` carrega o arquivo `app/views/projetos/show.php` e disponibiliza `$projeto` como variável local dentro do template.

#### Guards de autenticação

```php
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
        $this->redirect('');   // Usuário já logado: redireciona para home
    }
}
```

Qualquer método de Controller que precise de autenticação chama `$this->requireAuth()` como primeira linha. Se o usuário não estiver logado, é redirecionado automaticamente para a página de login.

#### Proteção CSRF

CSRF (*Cross-Site Request Forgery*) é um ataque onde um site malicioso força o navegador do usuário a fazer requisições para a aplicação sem que ele saiba. A proteção é feita com tokens únicos por sessão:

```php
protected function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // Gera token de 64 caracteres hexadecimais criptograficamente seguros
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

protected function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    // hash_equals previne timing attacks na comparação
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Token de segurança inválido.');
    }
}
```

Todo formulário HTML da aplicação inclui um campo oculto com o token da sessão. Ao receber uma requisição POST, o controller verifica se o token enviado corresponde ao da sessão. Requisições de outros domínios não têm acesso ao token, portanto são rejeitadas.

---

## 5. `app/models/Projeto.php` — Model principal

### O que é

O model `Projeto` é responsável por toda a comunicação com a tabela `projetos` do banco de dados. Ele encapsula as consultas SQL e expõe métodos estáticos para criar, buscar, atualizar e remover projetos.

### Por que foi feito assim

Concentrar o SQL no Model evita que consultas ao banco fiquem espalhadas pelos Controllers e Views. Isso segue o princípio de separação de responsabilidades do padrão MVC: o Controller decide **o que fazer**, o Model sabe **como buscar e salvar**.

Todos os métodos utilizam **prepared statements** do PDO, nunca interpolando dados do usuário diretamente nas queries. Isso elimina a possibilidade de injeção de SQL.

### Funcionalidades principais

#### Listagem com filtros e paginação

```php
public static function findAll(array $f = [], int $page = 1, int $perPage = 20): array
{
    [$where, $params] = self::buildWhere($f);

    $order = match ($f['ordem'] ?? '') {
        'visualizacoes' => 'p.visualizacoes DESC',
        default         => 'p.criado_em DESC',
    };

    $offset = ($page - 1) * $perPage;

    $sql = "SELECT p.*, u.nome AS autor_nome, u.tipo AS autor_tipo,
                   GROUP_CONCAT(DISTINCT t.nome ORDER BY t.nome SEPARATOR ',') AS tecnologias
            FROM projetos p
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN projeto_tecnologias pt ON pt.projeto_id = p.id
            LEFT JOIN tecnologias t          ON t.id = pt.tecnologia_id
            {$where}
            GROUP BY p.id
            ORDER BY {$order}
            LIMIT ? OFFSET ?";

    $s = self::db()->prepare($sql);
    $s->execute([...$params, $perPage, $offset]);
    return $s->fetchAll();
}
```

A query usa `GROUP_CONCAT` para retornar as tecnologias de cada projeto como uma string separada por vírgula (`PHP,MySQL,JavaScript`) em uma única consulta, evitando o problema N+1 de consultas.

#### Construção dinâmica de filtros

```php
private static function buildWhere(array $f): array
{
    $where  = ['p.publicado = 1'];   // Sempre filtra projetos visíveis
    $params = [];

    if (!empty($f['busca'])) {
        $where[]  = '(p.titulo LIKE ? OR p.descricao LIKE ?)';
        $like     = '%' . $f['busca'] . '%';
        $params[] = $like;
        $params[] = $like;
    }
    if (!empty($f['area']))   { $where[] = 'p.area = ?';   $params[] = $f['area']; }
    if (!empty($f['status'])) { $where[] = 'p.status = ?'; $params[] = $f['status']; }
    if (!empty($f['tecnologia'])) {
        $where[]  = 'EXISTS (SELECT 1 FROM projeto_tecnologias pt2
                     JOIN tecnologias t2 ON t2.id = pt2.tecnologia_id
                     WHERE pt2.projeto_id = p.id AND t2.nome = ?)';
        $params[] = $f['tecnologia'];
    }

    return ['WHERE ' . implode(' AND ', $where), $params];
}
```

Os filtros são construídos dinamicamente: só são adicionados à cláusula `WHERE` os que foram efetivamente informados. Os valores nunca são concatenados diretamente no SQL — são passados como parâmetros separados ao PDO.

#### Soft delete

```php
public static function delete(int $id): bool
{
    return self::db()
        ->prepare('UPDATE projetos SET publicado = 0 WHERE id = ?')
        ->execute([$id]);
}
```

Em vez de remover fisicamente o registro do banco, o sistema apenas marca o campo `publicado = 0`. Isso preserva a integridade referencial (comentários e arquivos associados continuam existindo) e permite recuperação de dados se necessário.

---

## 6. `app/controllers/ProjetoController.php` — Controller principal

### O que é

O `ProjetoController` gerencia todas as operações relacionadas a projetos: visualizar, criar, editar, excluir, comentar e remover comentários. É o controller mais completo da aplicação e ilustra o fluxo MVC de ponta a ponta.

### Por que foi feito assim

Cada método do controller corresponde a uma ação do usuário e segue sempre o mesmo fluxo: **verificar permissão → validar dados → chamar o Model → redirecionar ou renderizar**.

### Fluxo de criação de projeto (`store`)

O método `store` é chamado quando o usuário envia o formulário de novo projeto via POST. Ele demonstra o fluxo completo de uma operação de escrita:

```php
public function store(array $p = []): void
{
    $this->requireAuth();   // 1. Bloqueia usuários não autenticados
    $this->verifyCsrf();    // 2. Valida token de segurança do formulário

    $erros = $this->validateProjetoForm();  // 3. Valida os campos obrigatórios

    if (!empty($erros)) {
        // 4a. Se houver erros, re-exibe o formulário com as mensagens
        $this->view('projetos.create', [
            'erros' => $erros,
            'old'   => $_POST,   // Preserva os valores digitados pelo usuário
            ...
        ]);
        return;
    }

    // 5. Processa upload da imagem de capa (se enviada)
    $capa = null;
    if (!empty($_FILES['imagem_capa']['name'])) {
        $capa = upload_file($_FILES['imagem_capa'], 'capas', ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
    }

    // 6. Persiste o projeto no banco via Model
    $id = Projeto::create([
        'usuario_id' => $this->currentUser()['id'],
        'titulo'     => $_POST['titulo'],
        'descricao'  => $_POST['descricao'],
        'area'       => $_POST['area'] ?? '',
        'status'     => $_POST['status'],
        'repositorio'=> $_POST['repositorio'] ?? '',
        'imagem_capa'=> $capa,
        'tecnologias'=> $this->parseTecnologias($_POST['tags'] ?? ''),
    ]);

    // 7. Processa uploads de arquivos anexos
    $this->handleFileUploads($id);

    // 8. Define mensagem de sucesso e redireciona para o projeto criado
    $this->flash('success', 'Projeto publicado com sucesso!');
    $this->redirect("projeto/{$id}");
}
```

#### Autorização por propriedade

Antes de qualquer edição ou exclusão, o sistema verifica se o projeto pertence ao usuário logado:

```php
private function findAndAuthorize(int $id): array
{
    $projeto = Projeto::find($id);
    if (!$projeto) {
        http_response_code(404);
        require VIEWS_PATH . '/errors/404.php';
        exit;
    }
    // Compara o dono do projeto com o usuário da sessão
    if ($projeto['usuario_id'] != $_SESSION['usuario_id']) {
        $this->flash('danger', 'Você não tem permissão para esta ação.');
        $this->redirect("projeto/{$id}");
    }
    return $projeto;
}
```

#### Moderação de comentários por papel

A exclusão de comentários implementa controle de acesso baseado em papel (*role-based access control*): o autor pode excluir seu próprio comentário, e professores podem excluir qualquer comentário da plataforma.

```php
public function deletarComentario(array $p = []): void
{
    $this->requireAuth();
    $this->verifyCsrf();

    $com  = Comentario::find((int) $p['id']);
    $uid  = (int) $_SESSION['usuario_id'];
    $tipo = $_SESSION['usuario_tipo'];

    if ($com['usuario_id'] == $uid || $tipo === 'professor') {
        Comentario::delete((int) $com['id']);
        $this->flash('success', 'Comentário removido.');
    }

    $this->redirect("projeto/{$com['projeto_id']}#comentarios");
}
```

#### Upload seguro de arquivos

O `helper` `upload_file` utilizado pelo controller aplica três camadas de validação antes de mover o arquivo para o servidor:

```php
function upload_file(array $file, string $dir, array $allowedTypes, int $maxSize): string|false
{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;  // 1. Verifica erro no upload
    if ($file['size']  > $maxSize)        return false;  // 2. Verifica tamanho máximo

    // 3. Verifica o tipo real pelo conteúdo do arquivo (não pela extensão)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowedTypes, true)) return false;

    // Gera nome aleatório para evitar conflitos e impedir acesso previsível
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;

    move_uploaded_file($file['tmp_name'], UPLOAD_PATH . "/{$dir}/{$filename}");
    return $dir . '/' . $filename;
}
```

A verificação pelo tipo MIME real (usando a biblioteca `finfo` do PHP) é mais segura do que confiar na extensão do arquivo, pois um usuário mal-intencionado poderia renomear um arquivo `.php` para `.jpg` e tentar executá-lo no servidor. Além disso, o diretório de uploads possui um arquivo `.htaccess` que bloqueia a execução de qualquer script PHP, adicionando uma segunda camada de proteção.

---

## Resumo das decisões técnicas

| Decisão | Justificativa |
|---|---|
| Front Controller (`index.php` único) | Centraliza bootstrap, sessão e roteamento |
| Singleton para conexão PDO | Evita múltiplas conexões desnecessárias por requisição |
| Roteador customizado sem framework | URLs limpas com parâmetros dinâmicos, sem dependências externas |
| Herança de Controller base | Evita repetição de código de autenticação e CSRF em cada controller |
| Prepared statements em todos os Models | Proteção contra injeção de SQL |
| Soft delete em projetos | Preserva integridade referencial e permite recuperação |
| Verificação MIME no upload | Impede execução de scripts disfarçados de imagens |
| Tokens CSRF com `hash_equals` | Proteção contra CSRF e timing attacks |
| `h()` em todas as saídas das views | Proteção contra XSS em todo o HTML gerado |
