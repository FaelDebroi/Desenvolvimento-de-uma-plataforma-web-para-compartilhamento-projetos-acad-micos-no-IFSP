'use strict';

/* ── Mobile nav ─────────────────────────────────────────── */
(function () {
    const toggle = document.getElementById('navToggle');
    const menu   = document.getElementById('navMenu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        const open = menu.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open);
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && !toggle.contains(e.target)) {
            menu.classList.remove('is-open');
        }
    });
})();

/* ── User dropdown ───────────────────────────────────────── */
(function () {
    const btn  = document.getElementById('userMenuBtn');
    const drop = document.getElementById('userMenu');
    if (!btn || !drop) return;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        drop.classList.toggle('is-open');
    });

    document.addEventListener('click', () => drop.classList.remove('is-open'));
})();

/* ── Flash auto-dismiss ──────────────────────────────────── */
(function () {
    const flash = document.getElementById('flashMsg');
    if (!flash) return;
    setTimeout(() => flash.remove(), 5000);
})();

/* ── Password toggles ────────────────────────────────────── */
document.querySelectorAll('.form__password-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const input    = document.getElementById(targetId);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.textContent = input.type === 'password' ? '👁' : '🙈';
    });
});

/* ── Modal helpers ───────────────────────────────────────── */
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    }
}

// Close modal on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.is-open').forEach((m) => {
            m.classList.remove('is-open');
            document.body.style.overflow = '';
        });
    }
});

/* ── Tags (technology) input ─────────────────────────────── */
(function () {
    const input   = document.getElementById('tags');
    const preview = document.getElementById('tagsPreview');
    if (!input || !preview) return;

    function renderTags() {
        const tags = input.value.split(',').map(t => t.trim()).filter(Boolean);
        preview.innerHTML = tags.map(t =>
            `<span class="tag">${escHtml(t)}</span>`
        ).join('');
    }

    input.addEventListener('input', renderTags);
    renderTags(); // initial render (edit form)
})();

/* ── Cover image preview ─────────────────────────────────── */
(function () {
    const fileInput = document.getElementById('imagem_capa');
    const preview   = document.getElementById('coverPreview');
    const content   = document.getElementById('coverContent');
    if (!fileInput || !preview) return;

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (content) content.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
})();

/* ── Multiple file upload list ───────────────────────────── */
(function () {
    const fileInputs = document.querySelectorAll('input[name="arquivos[]"]');
    fileInputs.forEach((input) => {
        const list = document.getElementById('filesList');
        if (!list) return;

        input.addEventListener('change', () => {
            const newItems = Array.from(input.files).map((f) => {
                const li = document.createElement('li');
                li.textContent = `📄 ${f.name} (${formatBytes(f.size)})`;
                return li;
            });
            newItems.forEach(li => list.appendChild(li));
        });
    });
})();

/* ── Profile photo preview ───────────────────────────────── */
(function () {
    const input    = document.getElementById('foto_perfil');
    const preview  = document.getElementById('photoPreview');
    const initials = document.getElementById('photoInitials');
    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (initials) initials.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
})();

/* ── Utilities ───────────────────────────────────────────── */
function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatBytes(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
    if (bytes >= 1024)    return (bytes / 1024).toFixed(1) + ' KB';
    return bytes + ' B';
}
