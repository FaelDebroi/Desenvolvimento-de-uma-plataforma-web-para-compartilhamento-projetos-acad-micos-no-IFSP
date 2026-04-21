<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<header class="navbar">
    <div class="container navbar__inner">
        <a href="<?= url() ?>" class="navbar__brand">
            <span class="navbar__logo">🎓</span>
            <span class="navbar__title"><?= SITE_NAME ?></span>
        </a>

        <button class="navbar__toggle" id="navToggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <nav class="navbar__nav" id="navMenu">
            <a href="<?= url('projetos') ?>" class="navbar__link">Projetos</a>

            <?php if (!empty($_SESSION['usuario_id'])): ?>
                <a href="<?= url('projeto/novo') ?>" class="navbar__link navbar__link--cta">+ Publicar</a>

                <div class="navbar__dropdown">
                    <button class="navbar__user" id="userMenuBtn">
                        <?php if (!empty($usuario['foto_perfil'])): ?>
                            <img src="<?= upload_url(h($usuario['foto_perfil'])) ?>"
                                 alt="Foto" class="navbar__avatar">
                        <?php else: ?>
                            <span class="navbar__avatar navbar__avatar--initials">
                                <?= avatar_initials($_SESSION['usuario_nome']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="navbar__username"><?= h($_SESSION['usuario_nome']) ?></span>
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M6 8L1 3h10z"/>
                        </svg>
                    </button>
                    <ul class="dropdown__menu" id="userMenu">
                        <li><a href="<?= url('perfil/' . $_SESSION['usuario_id']) ?>">Meu perfil</a></li>
                        <li><a href="<?= url('perfil/editar') ?>">Editar perfil</a></li>
                        <li class="dropdown__divider"></li>
                        <li><a href="<?= url('logout') ?>" class="dropdown__danger">Sair</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= url('login') ?>"    class="navbar__link">Entrar</a>
                <a href="<?= url('cadastro') ?>" class="btn btn--primary btn--sm">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- ── Flash message ──────────────────────────────────────── -->
<?php
$flash = $flash ?? null;
if ($flash):
?>
<div class="flash flash--<?= h($flash['type']) ?>" id="flashMsg" role="alert">
    <span><?= h($flash['message']) ?></span>
    <button class="flash__close" onclick="this.parentElement.remove()" aria-label="Fechar">&times;</button>
</div>
<?php endif; ?>

<!-- ── Main ───────────────────────────────────────────────── -->
