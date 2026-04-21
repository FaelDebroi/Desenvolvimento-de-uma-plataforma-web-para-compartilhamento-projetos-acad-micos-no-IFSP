<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="container page-main">
    <div class="profile-layout">

        <!-- Sidebar do perfil -->
        <aside class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-card__avatar">
                    <?php if (!empty($perfil['foto_perfil'])): ?>
                        <img src="<?= upload_url(h($perfil['foto_perfil'])) ?>"
                             alt="<?= h($perfil['nome']) ?>" class="profile-card__img">
                    <?php else: ?>
                        <span class="avatar-initials avatar-initials--xl">
                            <?= avatar_initials($perfil['nome']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="profile-card__name"><?= h($perfil['nome']) ?></h1>
                <p class="profile-card__role">
                    <span class="badge <?= $perfil['tipo'] === 'professor' ? 'badge-info' : 'badge-secondary' ?>">
                        <?= tipo_label($perfil['tipo']) ?>
                    </span>
                </p>
                <p class="profile-card__course"><?= h($perfil['curso']) ?></p>

                <?php if (!empty($perfil['bio'])): ?>
                    <p class="profile-card__bio"><?= nl2br(h($perfil['bio'])) ?></p>
                <?php endif; ?>

                <div class="profile-card__links">
                    <?php if (!empty($perfil['github'])): ?>
                        <a href="<?= h($perfil['github']) ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm btn--full">
                            🐙 GitHub
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($perfil['linkedin'])): ?>
                        <a href="<?= h($perfil['linkedin']) ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--sm btn--full">
                            💼 LinkedIn
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $perfil['id']): ?>
                    <a href="<?= url('perfil/editar') ?>" class="btn btn--secondary btn--sm btn--full" style="margin-top:12px">
                        ✏️ Editar perfil
                    </a>
                <?php endif; ?>

                <p class="profile-card__since">
                    Membro desde <?= date('M Y', strtotime($perfil['criado_em'])) ?>
                </p>
            </div>
        </aside>

        <!-- Projetos do usuário -->
        <section class="profile-content">
            <h2 class="section-title">
                Projetos publicados
                <span class="badge badge-secondary"><?= count($projetos) ?></span>
            </h2>

            <?php if (empty($projetos)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">📭</div>
                    <h3>Nenhum projeto ainda</h3>
                    <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $perfil['id']): ?>
                        <p>Publique seu primeiro projeto acadêmico!</p>
                        <a href="<?= url('projeto/novo') ?>" class="btn btn--primary">+ Publicar projeto</a>
                    <?php else: ?>
                        <p>Este usuário ainda não publicou projetos.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="project-grid">
                    <?php foreach ($projetos as $proj): ?>
                        <article class="card">
                            <a href="<?= url('projeto/' . $proj['id']) ?>" class="card__image-link">
                                <?php if ($proj['imagem_capa']): ?>
                                    <img src="<?= upload_url(h($proj['imagem_capa'])) ?>"
                                         alt="<?= h($proj['titulo']) ?>" class="card__image" loading="lazy">
                                <?php else: ?>
                                    <div class="card__image-placeholder"><span>📁</span></div>
                                <?php endif; ?>
                                <span class="card__status badge <?= status_class($proj['status']) ?>">
                                    <?= status_label($proj['status']) ?>
                                </span>
                            </a>
                            <div class="card__body">
                                <h3 class="card__title">
                                    <a href="<?= url('projeto/' . $proj['id']) ?>"><?= h($proj['titulo']) ?></a>
                                </h3>
                                <?php if (!empty($proj['tecnologias'])): ?>
                                    <div class="card__tags">
                                        <?php foreach (array_slice(explode(',', $proj['tecnologias']), 0, 4) as $tag): ?>
                                            <span class="tag"><?= h(trim($tag)) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="card__meta">
                                    <span>👁 <?= number_format($proj['visualizacoes']) ?></span>
                                    <span><?= time_ago($proj['criado_em']) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
