<?php $pageTitle = 'Novo projeto'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="container page-main">
    <div class="form-page">
        <div class="form-page__header">
            <h1 class="form-page__title">Publicar novo projeto</h1>
            <p class="form-page__sub">Preencha as informações do seu projeto acadêmico</p>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert--danger">
                <ul class="alert__list">
                    <?php foreach ($erros as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= url('projeto/novo') ?>" method="POST" enctype="multipart/form-data" class="form" novalidate>
            <?= csrf_field($csrf) ?>

            <div class="form__row">
                <div class="form__group form__group--grow">
                    <label class="form__label" for="titulo">Título do projeto *</label>
                    <input class="form__input" type="text" id="titulo" name="titulo"
                           value="<?= h($old['titulo'] ?? '') ?>"
                           placeholder="Ex: Sistema de gestão de biblioteca" required maxlength="200">
                </div>

                <div class="form__group">
                    <label class="form__label" for="status">Status *</label>
                    <select class="form__select" id="status" name="status" required>
                        <option value="">Selecione...</option>
                        <option value="em_desenvolvimento" <?= ($old['status'] ?? '') === 'em_desenvolvimento' ? 'selected' : '' ?>>Em desenvolvimento</option>
                        <option value="beta"               <?= ($old['status'] ?? '') === 'beta'               ? 'selected' : '' ?>>Beta</option>
                        <option value="concluido"          <?= ($old['status'] ?? '') === 'concluido'          ? 'selected' : '' ?>>Concluído</option>
                    </select>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label" for="area">Área</label>
                <input class="form__input" type="text" id="area" name="area"
                       value="<?= h($old['area'] ?? '') ?>"
                       placeholder="Ex: Web, Mobile, Inteligência Artificial, Hardware..." list="areas-list">
                <datalist id="areas-list">
                    <?php foreach ($tecnologias as $t): ?>
                        <option value="<?= h($t) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form__group">
                <label class="form__label" for="descricao">Descrição *</label>
                <textarea class="form__textarea" id="descricao" name="descricao"
                          rows="6" placeholder="Descreva seu projeto: objetivo, funcionalidades, como foi desenvolvido..." required><?= h($old['descricao'] ?? '') ?></textarea>
                <span class="form__hint">Mínimo 10 caracteres. Use linguagem clara e objetiva.</span>
            </div>

            <div class="form__group">
                <label class="form__label" for="repositorio">Link do repositório (GitHub, GitLab...)</label>
                <input class="form__input" type="url" id="repositorio" name="repositorio"
                       value="<?= h($old['repositorio'] ?? '') ?>"
                       placeholder="https://github.com/usuario/projeto">
            </div>

            <div class="form__group">
                <label class="form__label" for="tags">Tecnologias utilizadas</label>
                <input class="form__input" type="text" id="tags" name="tags"
                       value="<?= h($old['tags'] ?? '') ?>"
                       placeholder="PHP, MySQL, JavaScript, React...">
                <span class="form__hint">Separe por vírgulas. Ex: PHP, MySQL, CSS3</span>
                <div id="tagsPreview" class="tag-list" style="margin-top:8px"></div>
            </div>

            <!-- Upload de imagem de capa -->
            <div class="form__group">
                <label class="form__label">Imagem de capa</label>
                <div class="upload-zone" id="coverZone">
                    <input type="file" id="imagem_capa" name="imagem_capa"
                           accept="image/*" class="upload-zone__input">
                    <div class="upload-zone__content" id="coverContent">
                        <span class="upload-zone__icon">🖼</span>
                        <p>Clique ou arraste uma imagem</p>
                        <span class="form__hint">JPG, PNG, WebP — máx. 5 MB</span>
                    </div>
                    <img id="coverPreview" class="upload-zone__preview" style="display:none" alt="Preview">
                </div>
            </div>

            <!-- Upload de arquivos -->
            <div class="form__group">
                <label class="form__label">Arquivos do projeto (opcional)</label>
                <div class="upload-zone upload-zone--files" id="filesZone">
                    <input type="file" id="arquivos" name="arquivos[]"
                           multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt"
                           class="upload-zone__input">
                    <div class="upload-zone__content">
                        <span class="upload-zone__icon">📎</span>
                        <p>Clique para selecionar arquivos</p>
                        <span class="form__hint">PDF, DOC, DOCX, PPT, ZIP — máx. 10 MB cada</span>
                    </div>
                </div>
                <ul id="filesList" class="files-preview"></ul>
            </div>

            <div class="form__actions">
                <a href="<?= url('') ?>" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">Publicar projeto</button>
            </div>
        </form>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
