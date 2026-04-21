<?php $pageTitle = 'Editar projeto'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="container page-main">
    <div class="form-page">
        <div class="form-page__header">
            <h1 class="form-page__title">Editar projeto</h1>
            <p class="form-page__sub"><?= h($projeto['titulo']) ?></p>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert--danger">
                <ul class="alert__list">
                    <?php foreach ($erros as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= url('projeto/' . $projeto['id'] . '/editar') ?>"
              method="POST" enctype="multipart/form-data" class="form" novalidate>
            <?= csrf_field($csrf) ?>

            <div class="form__row">
                <div class="form__group form__group--grow">
                    <label class="form__label" for="titulo">Título do projeto *</label>
                    <input class="form__input" type="text" id="titulo" name="titulo"
                           value="<?= h($projeto['titulo']) ?>" required maxlength="200">
                </div>

                <div class="form__group">
                    <label class="form__label" for="status">Status *</label>
                    <select class="form__select" id="status" name="status" required>
                        <option value="em_desenvolvimento" <?= $projeto['status'] === 'em_desenvolvimento' ? 'selected' : '' ?>>Em desenvolvimento</option>
                        <option value="beta"               <?= $projeto['status'] === 'beta'               ? 'selected' : '' ?>>Beta</option>
                        <option value="concluido"          <?= $projeto['status'] === 'concluido'          ? 'selected' : '' ?>>Concluído</option>
                    </select>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label" for="area">Área</label>
                <input class="form__input" type="text" id="area" name="area"
                       value="<?= h($projeto['area'] ?? '') ?>"
                       placeholder="Ex: Web, Mobile, IA...">
            </div>

            <div class="form__group">
                <label class="form__label" for="descricao">Descrição *</label>
                <textarea class="form__textarea" id="descricao" name="descricao"
                          rows="6" required><?= h($projeto['descricao']) ?></textarea>
            </div>

            <div class="form__group">
                <label class="form__label" for="repositorio">Link do repositório</label>
                <input class="form__input" type="url" id="repositorio" name="repositorio"
                       value="<?= h($projeto['repositorio'] ?? '') ?>"
                       placeholder="https://github.com/usuario/projeto">
            </div>

            <div class="form__group">
                <label class="form__label" for="tags">Tecnologias</label>
                <input class="form__input" type="text" id="tags" name="tags"
                       value="<?= h($projeto['tecnologias'] ?? '') ?>"
                       placeholder="PHP, MySQL, JavaScript...">
                <span class="form__hint">Separe por vírgulas</span>
                <div id="tagsPreview" class="tag-list" style="margin-top:8px"></div>
            </div>

            <!-- Imagem de capa atual -->
            <div class="form__group">
                <label class="form__label">Imagem de capa</label>
                <?php if (!empty($projeto['imagem_capa'])): ?>
                    <div class="current-image">
                        <img src="<?= upload_url(h($projeto['imagem_capa'])) ?>" alt="Capa atual" class="current-image__img">
                        <span class="form__hint">Envie uma nova imagem para substituir</span>
                    </div>
                <?php endif; ?>
                <div class="upload-zone" id="coverZone">
                    <input type="file" id="imagem_capa" name="imagem_capa"
                           accept="image/*" class="upload-zone__input">
                    <div class="upload-zone__content" id="coverContent">
                        <span class="upload-zone__icon">🖼</span>
                        <p>Clique para trocar a imagem de capa</p>
                        <span class="form__hint">JPG, PNG, WebP — máx. 5 MB</span>
                    </div>
                    <img id="coverPreview" class="upload-zone__preview" style="display:none" alt="Preview">
                </div>
            </div>

            <!-- Arquivos existentes -->
            <?php if (!empty($arquivos)): ?>
                <div class="form__group">
                    <label class="form__label">Arquivos atuais</label>
                    <ul class="file-list">
                        <?php foreach ($arquivos as $arq): ?>
                            <li class="file-item">
                                <span class="file-item__icon">📄</span>
                                <div class="file-item__info">
                                    <span class="file-item__name"><?= h($arq['nome_original']) ?></span>
                                    <span class="file-item__meta"><?= format_bytes($arq['tamanho_kb'] * 1024) ?></span>
                                </div>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="remover_arquivo[]" value="<?= $arq['id'] ?>">
                                    Remover
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Novos arquivos -->
            <div class="form__group">
                <label class="form__label">Adicionar arquivos</label>
                <div class="upload-zone upload-zone--files">
                    <input type="file" name="arquivos[]" multiple
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt"
                           class="upload-zone__input">
                    <div class="upload-zone__content">
                        <span class="upload-zone__icon">📎</span>
                        <p>Clique para adicionar arquivos</p>
                        <span class="form__hint">PDF, DOC, DOCX, PPT, ZIP — máx. 10 MB cada</span>
                    </div>
                </div>
                <ul id="filesList" class="files-preview"></ul>
            </div>

            <div class="form__actions">
                <a href="<?= url('projeto/' . $projeto['id']) ?>" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
