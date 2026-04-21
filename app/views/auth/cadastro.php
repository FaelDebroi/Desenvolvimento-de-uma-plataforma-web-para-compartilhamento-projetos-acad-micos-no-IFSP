<?php $pageTitle = 'Criar conta'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="auth-page">
    <div class="auth-card auth-card--wide">
        <div class="auth-card__header">
            <span class="auth-card__icon">🎓</span>
            <h1 class="auth-card__title">Crie sua conta</h1>
            <p class="auth-card__sub">Compartilhe seus projetos acadêmicos</p>
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

        <form action="<?= url('cadastro') ?>" method="POST" class="form" novalidate>
            <?= csrf_field($csrf) ?>

            <div class="form__row">
                <div class="form__group">
                    <label class="form__label" for="nome">Nome completo</label>
                    <input class="form__input" type="text" id="nome" name="nome"
                           value="<?= h($old['nome'] ?? '') ?>"
                           placeholder="Rafael da Silva" required>
                </div>

                <div class="form__group">
                    <label class="form__label" for="tipo">Tipo de usuário</label>
                    <select class="form__select" id="tipo" name="tipo" required>
                        <option value="">Selecione...</option>
                        <option value="aluno"     <?= ($old['tipo'] ?? '') === 'aluno'     ? 'selected' : '' ?>>Aluno</option>
                        <option value="professor" <?= ($old['tipo'] ?? '') === 'professor' ? 'selected' : '' ?>>Professor</option>
                    </select>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label" for="email">E-mail institucional</label>
                <input class="form__input" type="email" id="email" name="email"
                       value="<?= h($old['email'] ?? '') ?>"
                       placeholder="seuemail@aluno.ifsp.edu.br" required autocomplete="email">
            </div>

            <div class="form__group">
                <label class="form__label" for="curso">Curso</label>
                <input class="form__input" type="text" id="curso" name="curso"
                       value="<?= h($old['curso'] ?? '') ?>"
                       placeholder="Tecnologia em Análise e Desenvolvimento de Sistemas" required>
            </div>

            <div class="form__row">
                <div class="form__group">
                    <label class="form__label" for="senha">Senha</label>
                    <div class="form__password-wrap">
                        <input class="form__input" type="password" id="senha" name="senha"
                               placeholder="Mínimo 6 caracteres" required autocomplete="new-password">
                        <button type="button" class="form__password-toggle" data-target="senha" aria-label="Mostrar">👁</button>
                    </div>
                </div>

                <div class="form__group">
                    <label class="form__label" for="senha2">Confirmar senha</label>
                    <div class="form__password-wrap">
                        <input class="form__input" type="password" id="senha2" name="senha2"
                               placeholder="Repita a senha" required autocomplete="new-password">
                        <button type="button" class="form__password-toggle" data-target="senha2" aria-label="Mostrar">👁</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full">Criar conta</button>
        </form>

        <div class="auth-card__footer">
            Já tem conta?
            <a href="<?= url('login') ?>" class="link">Entrar</a>
        </div>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
