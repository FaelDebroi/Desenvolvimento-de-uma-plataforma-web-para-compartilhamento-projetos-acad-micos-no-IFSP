<?php
declare(strict_types=1);

// Configurações de exibição de erros (desativar em produção)
define('ROOT', __DIR__);
define('APP_PATH', ROOT . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('CORE_PATH', ROOT . '/core');

// Carrega as dependências essenciais
require_once ROOT . '/config/config.php';
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Router.php';
require_once ROOT . '/helpers.php';

// Models
// Carrega os modelos essenciais
foreach (['Usuario', 'Projeto', 'Comentario', 'Arquivo', 'RecuperacaoSenha'] as $m) {
    require_once APP_PATH . "/models/{$m}.php";
}

// Controllers
// Carrega os controladores essenciais
foreach (['AuthController', 'HomeController', 'ProjetoController', 'PerfilController'] as $c) {
    require_once APP_PATH . "/controllers/{$c}.php";
}

// Inicia a sessão para gerenciamento de autenticação e outras funcionalidades
session_start();

// Instancia o roteador e despacha a requisição
$router = new Router();
$router->dispatch();
