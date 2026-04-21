
<!-- ── Footer ─────────────────────────────────────────────── -->
<footer class="footer">
    <div class="container footer__inner">
        <p class="footer__copy">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?> — IFSP Campinas
        </p>
        <p class="footer__links">
            <a href="<?= url('projetos') ?>">Projetos</a>
            <a href="<?= url('cadastro') ?>">Criar conta</a>
        </p>
    </div>
</footer>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
