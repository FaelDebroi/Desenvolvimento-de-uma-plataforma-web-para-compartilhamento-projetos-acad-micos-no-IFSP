<?php
class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    private function add(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'path', 'controller', 'action');
    }

    private function registerRoutes(): void
    {
        // Público
        $this->add('GET',  '',                         'HomeController',    'index');
        $this->add('GET',  'projetos',                 'HomeController',    'index');

        // Auth
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
        $this->add('GET',  'projeto/novo',             'ProjetoController', 'create');
        $this->add('POST', 'projeto/novo',             'ProjetoController', 'store');
        $this->add('GET',  'projeto/{id}',             'ProjetoController', 'show');
        $this->add('GET',  'projeto/{id}/editar',      'ProjetoController', 'edit');
        $this->add('POST', 'projeto/{id}/editar',      'ProjetoController', 'update');
        $this->add('POST', 'projeto/{id}/deletar',     'ProjetoController', 'delete');
        $this->add('POST', 'projeto/{id}/comentario',  'ProjetoController', 'comentar');
        $this->add('POST', 'comentario/{id}/deletar',  'ProjetoController', 'deletarComentario');

        // Perfil
        $this->add('GET',  'perfil/editar',            'PerfilController',  'edit');
        $this->add('POST', 'perfil/editar',            'PerfilController',  'update');
        $this->add('GET',  'perfil/{id}',              'PerfilController',  'show');
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = $_SERVER['REQUEST_URI'];

        // Strip base path and query string
        $uri = preg_replace('#^' . preg_quote(BASE_PATH, '#') . '#', '', $uri);
        $uri = strtok($uri, '?');
        $uri = trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Build regex from route path
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            array_shift($matches);

            // Map param names
            preg_match_all('/\{([^}]+)\}/', $route['path'], $paramNames);
            $params = [];
            foreach ($paramNames[1] as $i => $name) {
                $params[$name] = $matches[$i] ?? null;
            }

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
