<?php
class Router
{   
    // Roteamento simples baseado em array
    private array $routes = [];
    // Exemplo de rota: ['method' => 'GET', 'path' => 'projeto/{id}', 'controller' => 'ProjetoController', 'action' => 'show']
    public function __construct()
    {
        $this->registerRoutes();
    }
    // Adiciona uma rota ao roteador
    private function add(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'path', 'controller', 'action');
    }

    // Registra todas as rotas da aplicação
    private function registerRoutes(): void
    {
        // Público
        // Home e listagem de projetos
        $this->add('GET',  '',                         'HomeController',    'index');
        $this->add('GET',  'projetos',                 'HomeController',    'index');

        // Auth
        // Rotas de autenticação e gerenciamento de conta
        $this->add('GET',  'login',                    'AuthController',    'loginForm');
        $this->add('POST', 'login',                    'AuthController',    'login');
        $this->add('GET',  'cadastro',                 'AuthController',    'cadastroForm');
        $this->add('POST', 'cadastro',                 'AuthController',    'cadastro');
        $this->add('GET',  'logout',                   'AuthController',    'logout');
        $this->add('GET',  'recuperar-senha',          'AuthController',    'recuperarForm');
        $this->add('POST', 'recuperar-senha',          'AuthController',    'recuperar');
        $this->add('GET',  'redefinir-senha',          'AuthController',    'redefinirForm');
        $this->add('POST', 'redefinir-senha',          'AuthController',    'redefinir');

        // Projetos
        // Rotas de CRUD para projetos e comentários
        $this->add('GET',  'projeto/novo',             'ProjetoController', 'create');
        $this->add('POST', 'projeto/novo',             'ProjetoController', 'store');
        $this->add('GET',  'projeto/{id}',             'ProjetoController', 'show');
        $this->add('GET',  'projeto/{id}/editar',      'ProjetoController', 'edit');
        $this->add('POST', 'projeto/{id}/editar',      'ProjetoController', 'update');
        $this->add('POST', 'projeto/{id}/deletar',     'ProjetoController', 'delete');
        $this->add('POST', 'projeto/{id}/comentario',  'ProjetoController', 'comentar');
        $this->add('POST', 'comentario/{id}/deletar',  'ProjetoController', 'deletarComentario');

        // Perfil
        // Rotas para visualização e edição de perfil
        $this->add('GET',  'perfil/editar',            'PerfilController',  'edit');
        $this->add('POST', 'perfil/editar',            'PerfilController',  'update');
        $this->add('GET',  'perfil/{id}',              'PerfilController',  'show');
    }


    // Despacha a requisição para o controlador e ação corretos
    public function dispatch(): void
    {
        // Obtém o método HTTP e a URI da requisição
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = $_SERVER['REQUEST_URI'];

        // Strip base path and query string
        $uri = preg_replace('#^' . preg_quote(BASE_PATH, '#') . '#', '', $uri);
        $uri = strtok($uri, '?');
        $uri = trim($uri, '/');

        // Percorre as rotas registradas para encontrar uma correspondência
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Build regex from route path
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';
            
            // Verifica se a URI corresponde ao padrão da rota
            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }
            // Remove o primeiro elemento (URI completa) dos matches
            array_shift($matches);

            // Map param names
            preg_match_all('/\{([^}]+)\}/', $route['path'], $paramNames);
            $params = [];
            foreach ($paramNames[1] as $i => $name) {
                $params[$name] = $matches[$i] ?? null;
            }
            // Instancia o controlador e chama a ação correspondente
            $ctrl = new $route['controller']();
            $action = $route['action'];
            $ctrl->$action($params);
            return;
        }

        // 404
        http_response_code(404);
        require VIEWS_PATH . '/errors/404.php';
    }
}
