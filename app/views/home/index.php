<?php require VIEWS_PATH . '/layout/header.php'; ?>

<main class="container page-main">

    <!-- Hero -->
    <section class="hero">
        <h1 class="hero__title">Projetos Acadêmicos</h1>
        <p class="hero__sub">Explore, aprenda e compartilhe o que foi desenvolvido no IFSP Campinas</p>

        <form class="hero__search" action="<?= url('projetos') ?>" method="GET">
            <input class="hero__search-input" type="search" name="busca"
                   value="<?= h($filters['busca']) ?>"
                   placeholder="Buscar projetos por título ou descrição...">
            <?php foreach (['area','status','tecnologia','ordem'] as $k): ?>
                <?php if (!empty($filters[$k])): ?>
                    <input type="hidden" name="<?= $k ?>" value="<?= h($filters[$k]) ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit" class="btn btn--primary">Buscar</button>
        </form>
    </section>

    <div class="projects-layout">

        <!-- Sidebar filtros -->
        <aside class="sidebar">
            <form action="<?= url('projetos') ?>" method="GET" id="filterForm">
                <?php if (!empty($filters['busca'])): ?>
                    <input type="hidden" name="busca" value="<?= h($filters['busca']) ?>">
                <?php endif; ?>

                <div class="sidebar__section">
                    <h3 class="sidebar__title">Ordenar por</h3>
                    <label class="radio-label">
                        <input type="radio" name="ordem" value=""
                               <?= empty($filters['ordem']) ? 'checked' : '' ?> onchange="this.form.submit()">
                        Mais recentes
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="ordem" value="visualizacoes"
                               <?= $filters['ordem'] === 'visualizacoes' ? 'checked' : '' ?> onchange="this.form.submit()">
                        Mais vistos
                    </label>
                </div>

                <div class="sidebar__section">
                    <h3 class="sidebar__title">Status</h3>
                    <select class="form__select form__select--sm" name="status" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="em_desenvolvimento" <?= $filters['status'] === 'em_desenvolvimento' ? 'selected' : '' ?>>Em desenvolvimento</option>
                        <option value="beta"               <?= $filters['status'] === 'beta'               ? 'selected' : '' ?>>Beta</option>
                        <option value="concluido"          <?= $filters['status'] === 'concluido'          ? 'selected' : '' ?>>Concluído</option>
                    </select>
                </div>

                <?php if (!empty($areas)): ?>
                <div class="sidebar__section">
                    <h3 class="sidebar__title">Área</h3>
                    <select class="form__select form__select--sm" name="area" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?= h($a) ?>" <?= $filters['area'] === $a ? 'selected' : '' ?>><?= h($a) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (!empty($tecnologias)): ?>
                <div class="sidebar__section">
                    <h3 class="sidebar__title">Tecnologia</h3>
                    <select class="form__select form__select--sm" name="tecnologia" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <?php foreach ($tecnologias as $t): ?>
                            <option value="<?= h($t) ?>" <?= $filters['tecnologia'] === $t ? 'selected' : '' ?>><?= h($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php $hasFilters = array_filter($filters); ?>
                <?php if ($hasFilters): ?>
                    <a href="<?= url('projetos') ?>" class="btn btn--ghost btn--sm btn--full">Limpar filtros</a>
                <?php endif; ?>
            </form>
        </aside>

        <!-- Grade de projetos -->
        <section class="projects-section">
            <div class="projects-header">
                <p class="projects-count">
                    <?= $pagination['total'] ?> projeto<?= $pagination['total'] !== 1 ? 's' : '' ?> encontrado<?= $pagination['total'] !== 1 ? 's' : '' ?>
                </p>
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <a href="<?= url('projeto/novo') ?>" class="btn btn--primary btn--sm">+ Publicar projeto</a>
                <?php endif; ?>
            </div>

            <?php if (empty($projetos)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">🔍</div>
                    <h3>Nenhum projeto encontrado</h3>
                    <p>Tente outros filtros ou seja o primeiro a publicar!</p>
                    <?php if (!empty($_SESSION['usuario_id'])): ?>
                        <a href="<?= url('projeto/novo') ?>" class="btn btn--primary">Publicar projeto</a>
                    <?php else: ?>
                        <a href="<?= url('cadastro') ?>" class="btn btn--primary">Criar conta</a>
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
                                    <div class="card__image-placeholder">
                                        <span>📁</span>
                                    </div>
                                <?php endif; ?>
                                <span class="card__status badge <?= status_class($proj['status']) ?>">
                                    <?= status_label($proj['status']) ?>
                                </span>
                            </a>

                            <div class="card__body">
                                <h2 class="card__title">
                                    <a href="<?= url('projeto/' . $proj['id']) ?>"><?= h($proj['titulo']) ?></a>
                                </h2>

                                <a href="<?= url('perfil/' . $proj['usuario_id']) ?>" class="card__author">
                                    <?php if ($proj['autor_foto']): ?>
                                        <img src="<?= upload_url(h($proj['autor_foto'])) ?>" alt="" class="card__author-avatar">
                                    <?php else: ?>
                                        <span class="card__author-avatar card__author-avatar--initials">
                                            <?= avatar_initials($proj['autor_nome']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span><?= h($proj['autor_nome']) ?></span>
                                    <?php if ($proj['autor_tipo'] === 'professor'): ?>
                                        <span class="badge badge-info badge--xs">Prof.</span>
                                    <?php endif; ?>
                                </a>

                                <?php if ($proj['tecnologias']): ?>
                                    <div class="card__tags">
                                        <?php foreach (array_slice(explode(',', $proj['tecnologias']), 0, 4) as $tag): ?>
                                            <span class="tag"><?= h(trim($tag)) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="card__meta">
                                    <span title="Visualizações">👁 <?= number_format($proj['visualizacoes']) ?></span>
                                    <span title="Publicado"><?= time_ago($proj['criado_em']) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav class="pagination" aria-label="Paginação">
                        <?php
                        $queryBase = http_build_query(array_filter(array_merge($filters, ['pagina' => ''])));
                        $queryBase = $queryBase ? '&' . $queryBase : '';
                        ?>

                        <?php if ($pagination['has_prev']): ?>
                            <a href="<?= url('projetos?pagina=' . $pagination['prev'] . $queryBase) ?>" class="pagination__btn">← Anterior</a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['total_pages'], $pagination['current'] + 2); $i++): ?>
                            <a href="<?= url('projetos?pagina=' . $i . $queryBase) ?>"
                               class="pagination__btn <?= $i === $pagination['current'] ? 'pagination__btn--active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagination['has_next']): ?>
                            <a href="<?= url('projetos?pagina=' . $pagination['next'] . $queryBase) ?>" class="pagination__btn">Próxima →</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
