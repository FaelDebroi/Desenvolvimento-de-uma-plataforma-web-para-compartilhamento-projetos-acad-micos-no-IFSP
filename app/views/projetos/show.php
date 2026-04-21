<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="container page-main">
    <div class="project-detail">

        <!-- Coluna principal -->
        <article class="project-content">

            <!-- Capa -->
            <?php if ($projeto['imagem_capa']): ?>
                <div class="project-cover">
                    <img src="<?= upload_url(h($projeto['imagem_capa'])) ?>"
                         alt="<?= h($projeto['titulo']) ?>" class="project-cover__img">
                </div>
            <?php endif; ?>

            <!-- Cabeçalho -->
            <div class="project-header">
                <div class="project-header__top">
                    <span class="badge <?= status_class($projeto['status']) ?>">
                        <?= status_label($projeto['status']) ?>
                    </span>
                    <?php if (!empty($projeto['area'])): ?>
                        <span class="badge badge-secondary"><?= h($projeto['area']) ?></span>
                    <?php endif; ?>
                </div>

                <h1 class="project-title"><?= h($projeto['titulo']) ?></h1>

                <div class="project-meta">
                    <span>👁 <?= number_format($projeto['visualizacoes']) ?> visualizações</span>
                    <span>🕐 <?= time_ago($projeto['criado_em']) ?></span>
                </div>

                <!-- Ações do autor -->
                <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $projeto['usuario_id']): ?>
                    <div class="project-actions">
                        <a href="<?= url('projeto/' . $projeto['id'] . '/editar') ?>" class="btn btn--secondary btn--sm">✏️ Editar</a>
                        <button class="btn btn--danger btn--sm" onclick="openModal('deleteModal')">🗑 Deletar</button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Descrição -->
            <section class="project-section">
                <h2 class="project-section__title">Sobre o projeto</h2>
                <div class="project-description">
                    <?= nl2br(h($projeto['descricao'])) ?>
                </div>
            </section>

            <!-- Tecnologias -->
            <?php if (!empty($projeto['tecnologias'])): ?>
                <section class="project-section">
                    <h2 class="project-section__title">Tecnologias</h2>
                    <div class="tag-list">
                        <?php foreach (explode(',', $projeto['tecnologias']) as $tag): ?>
                            <a href="<?= url('projetos?tecnologia=' . urlencode(trim($tag))) ?>" class="tag tag--link">
                                <?= h(trim($tag)) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Repositório -->
            <?php if (!empty($projeto['repositorio'])): ?>
                <section class="project-section">
                    <h2 class="project-section__title">Repositório</h2>
                    <a href="<?= h($projeto['repositorio']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--ghost">
                        🔗 <?= h($projeto['repositorio']) ?>
                    </a>
                </section>
            <?php endif; ?>

            <!-- Arquivos -->
            <?php if (!empty($arquivos)): ?>
                <section class="project-section">
                    <h2 class="project-section__title">Arquivos para download</h2>
                    <ul class="file-list">
                        <?php foreach ($arquivos as $arq): ?>
                            <li class="file-item">
                                <span class="file-item__icon">📄</span>
                                <div class="file-item__info">
                                    <span class="file-item__name"><?= h($arq['nome_original']) ?></span>
                                    <span class="file-item__meta"><?= format_bytes($arq['tamanho_kb'] * 1024) ?></span>
                                </div>
                                <a href="<?= upload_url(h($arq['caminho'])) ?>"
                                   download="<?= h($arq['nome_original']) ?>"
                                   class="btn btn--ghost btn--sm">⬇ Baixar</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <!-- Comentários -->
            <section class="project-section" id="comentarios">
                <h2 class="project-section__title">
                    Comentários <span class="badge badge-secondary"><?= count($comentarios) ?></span>
                </h2>

                <?php if (!empty($comentarios)): ?>
                    <div class="comments">
                        <?php foreach ($comentarios as $com): ?>
                            <div class="comment">
                                <div class="comment__avatar">
                                    <?php if ($com['autor_foto']): ?>
                                        <img src="<?= upload_url(h($com['autor_foto'])) ?>" alt="">
                                    <?php else: ?>
                                        <span class="avatar-initials"><?= avatar_initials($com['autor_nome']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="comment__body">
                                    <div class="comment__header">
                                        <strong><?= h($com['autor_nome']) ?></strong>
                                        <?php if ($com['autor_tipo'] === 'professor'): ?>
                                            <span class="badge badge-info badge--xs">Professor</span>
                                        <?php endif; ?>
                                        <span class="comment__time"><?= time_ago($com['criado_em']) ?></span>

                                        <?php
                                        $canDelete = !empty($_SESSION['usuario_id']) &&
                                            ($com['usuario_id'] == $_SESSION['usuario_id'] ||
                                             $_SESSION['usuario_tipo'] === 'professor');
                                        ?>
                                        <?php if ($canDelete): ?>
                                            <form action="<?= url('comentario/' . $com['id'] . '/deletar') ?>"
                                                  method="POST" class="comment__delete-form"
                                                  onsubmit="return confirm('Remover comentário?')">
                                                <?= csrf_field($csrf) ?>
                                                <button type="submit" class="btn-icon" title="Remover">🗑</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <p class="comment__text"><?= nl2br(h($com['conteudo'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum comentário ainda. Seja o primeiro!</p>
                <?php endif; ?>

                <!-- Formulário de comentário -->
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <form action="<?= url('projeto/' . $projeto['id'] . '/comentario') ?>"
                          method="POST" class="comment-form">
                        <?= csrf_field($csrf) ?>
                        <div class="form__group">
                            <label class="form__label" for="conteudo">Adicionar comentário</label>
                            <textarea class="form__textarea" id="conteudo" name="conteudo"
                                      rows="3" placeholder="Escreva seu comentário..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary btn--sm">Comentar</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">
                        <a href="<?= url('login') ?>" class="link">Faça login</a> para comentar.
                    </p>
                <?php endif; ?>
            </section>
        </article>

        <!-- Sidebar autor -->
        <aside class="project-sidebar">
            <div class="author-card">
                <div class="author-card__avatar">
                    <?php if ($projeto['autor_foto']): ?>
                        <img src="<?= upload_url(h($projeto['autor_foto'])) ?>"
                             alt="<?= h($projeto['autor_nome']) ?>">
                    <?php else: ?>
                        <span class="avatar-initials avatar-initials--lg">
                            <?= avatar_initials($projeto['autor_nome']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="author-card__name">
                    <a href="<?= url('perfil/' . $projeto['usuario_id']) ?>"><?= h($projeto['autor_nome']) ?></a>
                </h3>
                <p class="author-card__role">
                    <?= tipo_label($projeto['autor_tipo']) ?> · <?= h($projeto['autor_curso']) ?>
                </p>
                <?php if (!empty($projeto['autor_bio'])): ?>
                    <p class="author-card__bio"><?= h($projeto['autor_bio']) ?></p>
                <?php endif; ?>
                <div class="author-card__links">
                    <?php if (!empty($projeto['autor_github'])): ?>
                        <a href="<?= h($projeto['autor_github']) ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm btn--full">
                            GitHub
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($projeto['autor_linkedin'])): ?>
                        <a href="<?= h($projeto['autor_linkedin']) ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm btn--full">
                            LinkedIn
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('perfil/' . $projeto['usuario_id']) ?>" class="btn btn--secondary btn--sm btn--full">
                        Ver perfil completo
                    </a>
                </div>
            </div>
        </aside>
    </div>
</main>

<!-- Modal de confirmação de deleção -->
<?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $projeto['usuario_id']): ?>
<div class="modal" id="deleteModal" role="dialog" aria-modal="true">
    <div class="modal__overlay" onclick="closeModal('deleteModal')"></div>
    <div class="modal__content">
        <h2 class="modal__title">Confirmar exclusão</h2>
        <p>Tem certeza que deseja excluir <strong><?= h($projeto['titulo']) ?></strong>? Esta ação não pode ser desfeita.</p>
        <div class="modal__actions">
            <button class="btn btn--secondary" onclick="closeModal('deleteModal')">Cancelar</button>
            <form action="<?= url('projeto/' . $projeto['id'] . '/deletar') ?>" method="POST" style="display:inline">
                <?= csrf_field($csrf) ?>
                <button type="submit" class="btn btn--danger">Excluir projeto</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
