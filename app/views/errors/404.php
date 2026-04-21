<?php $pageTitle = 'Página não encontrada'; ?>
<?php require VIEWS_PATH . '/layout/header.php'; ?>
<main class="container" style="text-align:center;padding:80px 20px">
    <div style="font-size:80px">😕</div>
    <h1 style="font-size:2rem;margin:16px 0 8px">Página não encontrada</h1>
    <p style="color:var(--text-secondary);margin-bottom:32px">O recurso que você procura não existe ou foi removido.</p>
    <a href="<?= url() ?>" class="btn btn--primary">← Voltar para início</a>
</main>
<?php require VIEWS_PATH . '/layout/footer.php'; ?>
