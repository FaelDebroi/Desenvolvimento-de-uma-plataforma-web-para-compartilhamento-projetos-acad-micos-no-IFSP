<?php
declare(strict_types=1);

define('ROOT',       __DIR__);
define('APP_PATH',   ROOT . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('CORE_PATH',  ROOT . '/core');

require_once ROOT . '/config/config.php';
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Router.php';
require_once ROOT . '/helpers.php';

// Models
foreach (['Usuario','Projeto','Comentario','Arquivo','RecuperacaoSenha'] as $m) {
    require_once APP_PATH . "/models/{$m}.php";
}

// Controllers
foreach (['AuthController','HomeController','ProjetoController','PerfilController'] as $c) {
    require_once APP_PATH . "/controllers/{$c}.php";
}

session_start();

$router = new Router();
$router->dispatch();
