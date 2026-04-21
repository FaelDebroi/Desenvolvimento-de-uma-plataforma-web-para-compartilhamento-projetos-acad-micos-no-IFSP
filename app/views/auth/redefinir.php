<?php $pageTitle = 'Redefinir senha'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <span class="auth-card__icon">🔒</span>
            <h1 class="auth-card__title">Nova senha</h1>
            <p class="auth-card__sub">Escolha uma senha segura</p>
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

        <form action="<?= url('redefinir-senha') ?>" method="POST" class="form" novalidate>
            <?= csrf_field($csrf) ?>
            <input type="hidden" name="token" value="<?= h($token) ?>">

            <div class="form__group">
                <label class="form__label" for="senha">Nova senha</label>
                <div class="form__password-wrap">
                    <input class="form__input" type="password" id="senha" name="senha"
                           placeholder="Mínimo 6 caracteres" required autocomplete="new-password">
                    <button type="button" class="form__password-toggle" data-target="senha" aria-label="Mostrar">👁</button>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label" for="senha2">Confirmar nova senha</label>
                <div class="form__password-wrap">
                    <input class="form__input" type="password" id="senha2" name="senha2"
                           placeholder="Repita a nova senha" required autocomplete="new-password">
                    <button type="button" class="form__password-toggle" data-target="senha2" aria-label="Mostrar">👁</button>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full">Redefinir senha</button>
        </form>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
