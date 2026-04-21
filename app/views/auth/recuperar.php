<?php $pageTitle = 'Recuperar senha'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <span class="auth-card__icon">🔑</span>
            <h1 class="auth-card__title">Recuperar senha</h1>
            <p class="auth-card__sub">Informe seu e-mail para receber o link de redefinição</p>
        </div>

        <form action="<?= url('recuperar-senha') ?>" method="POST" class="form">
            <?= csrf_field($csrf) ?>

            <div class="form__group">
                <label class="form__label" for="email">E-mail cadastrado</label>
                <input class="form__input" type="email" id="email" name="email"
                       placeholder="seuemail@aluno.ifsp.edu.br" required autocomplete="email">
            </div>

            <button type="submit" class="btn btn--primary btn--full">Enviar link</button>
        </form>

        <div class="auth-card__footer">
            <a href="<?= url('login') ?>" class="link">← Voltar ao login</a>
        </div>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
