<?php $pageTitle = 'Editar perfil'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="container page-main">
    <div class="form-page">
        <div class="form-page__header">
            <h1 class="form-page__title">Editar perfil</h1>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert--danger">
                <ul class="alert__list">
                    <?php foreach ($erros as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= url('perfil/editar') ?>" method="POST" enctype="multipart/form-data" class="form" novalidate>
            <?= csrf_field($csrf) ?>

            <!-- Foto de perfil -->
            <div class="form__group" style="text-align:center">
                <label class="form__label">Foto de perfil</label>
                <div class="avatar-upload">
                    <?php if (!empty($usuario['foto_perfil'])): ?>
                        <img src="<?= upload_url(h($usuario['foto_perfil'])) ?>"
                             alt="Foto atual" class="avatar-upload__preview" id="photoPreview">
                    <?php else: ?>
                        <span class="avatar-initials avatar-initials--xl" id="photoInitials">
                            <?= avatar_initials($usuario['nome']) ?>
                        </span>
                        <img id="photoPreview" class="avatar-upload__preview" style="display:none" alt="">
                    <?php endif; ?>
                    <label for="foto_perfil" class="btn btn--ghost btn--sm" style="margin-top:8px;cursor:pointer">
                        Trocar foto
                    </label>
                    <input type="file" id="foto_perfil" name="foto_perfil"
                           accept="image/*" class="sr-only">
                </div>
            </div>

            <div class="form__row">
                <div class="form__group form__group--grow">
                    <label class="form__label" for="nome">Nome completo *</label>
                    <input class="form__input" type="text" id="nome" name="nome"
                           value="<?= h($usuario['nome']) ?>" required>
                </div>

                <div class="form__group form__group--grow">
                    <label class="form__label" for="curso">Curso *</label>
                    <input class="form__input" type="text" id="curso" name="curso"
                           value="<?= h($usuario['curso']) ?>" required>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label" for="bio">Bio</label>
                <textarea class="form__textarea" id="bio" name="bio" rows="4"
                          placeholder="Conte um pouco sobre você, seus interesses e projetos..."><?= h($usuario['bio'] ?? '') ?></textarea>
            </div>

            <div class="form__row">
                <div class="form__group form__group--grow">
                    <label class="form__label" for="github">Perfil GitHub</label>
                    <input class="form__input" type="url" id="github" name="github"
                           value="<?= h($usuario['github'] ?? '') ?>"
                           placeholder="https://github.com/seuperfil">
                </div>

                <div class="form__group form__group--grow">
                    <label class="form__label" for="linkedin">Perfil LinkedIn</label>
                    <input class="form__input" type="url" id="linkedin" name="linkedin"
                           value="<?= h($usuario['linkedin'] ?? '') ?>"
                           placeholder="https://linkedin.com/in/seuperfil">
                </div>
            </div>

            <hr class="form__divider">

            <h2 class="form__section-title">Alterar senha <span class="form__hint">(opcional)</span></h2>

            <div class="form__row">
                <div class="form__group form__group--grow">
                    <label class="form__label" for="senha_atual">Senha atual</label>
                    <div class="form__password-wrap">
                        <input class="form__input" type="password" id="senha_atual" name="senha_atual"
                               placeholder="Senha atual" autocomplete="current-password">
                        <button type="button" class="form__password-toggle" data-target="senha_atual" aria-label="Mostrar">👁</button>
                    </div>
                </div>

                <div class="form__group form__group--grow">
                    <label class="form__label" for="nova_senha">Nova senha</label>
                    <div class="form__password-wrap">
                        <input class="form__input" type="password" id="nova_senha" name="nova_senha"
                               placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                        <button type="button" class="form__password-toggle" data-target="nova_senha" aria-label="Mostrar">👁</button>
                    </div>
                </div>

                <div class="form__group form__group--grow">
                    <label class="form__label" for="nova_senha2">Confirmar nova senha</label>
                    <div class="form__password-wrap">
                        <input class="form__input" type="password" id="nova_senha2" name="nova_senha2"
                               placeholder="Repita a nova senha" autocomplete="new-password">
                        <button type="button" class="form__password-toggle" data-target="nova_senha2" aria-label="Mostrar">👁</button>
                    </div>
                </div>
            </div>

            <div class="form__actions">
                <a href="<?= url('perfil/' . $usuario['id']) ?>" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
