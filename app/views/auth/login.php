<?php $pageTitle = 'Entrar'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <span class="auth-card__icon">🎓</span>
            <h1 class="auth-card__title">Bem-vindo(a) de volta</h1>
            <p class="auth-card__sub">Entre na sua conta <?= SITE_NAME ?></p>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert--danger">
                <ul class="alert__list">
                    <?php foreach ($erros as $e): ?>
                        <li><?= h($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= url('login') ?>" method="POST" class="form" novalidate>
            <?= csrf_field($csrf) ?>

            <div class="form__group">
                <label class="form__label" for="email">E-mail institucional</label>
                <input class="form__input" type="email" id="email" name="email"
                       value="<?= h($email ?? '') ?>"
                       placeholder="seuemail@aluno.ifsp.edu.br"
                       required autocomplete="email">
            </div>

            <div class="form__group">
                <label class="form__label" for="senha">Senha</label>
                <div class="form__password-wrap">
                    <input class="form__input" type="password" id="senha" name="senha"
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="form__password-toggle" data-target="senha" aria-label="Mostrar senha">
                        👁
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full">Entrar</button>
        </form>

        <div class="auth-card__footer">
            <a href="<?= url('recuperar-senha') ?>" class="link">Esqueci minha senha</a>
            <span class="auth-card__sep">·</span>
            <a href="<?= url('cadastro') ?>" class="link">Criar conta</a>
        </div>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
