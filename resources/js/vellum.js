(function () {
    'use strict';

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function initEditor(editor) {
        const body = editor.querySelector('[data-vellum-body]');
        const preview = editor.querySelector('[data-vellum-preview]');
        const tabs = editor.querySelectorAll('[data-vellum-tab]');
        const previewUrl = editor.getAttribute('data-preview-url');

        function selectTab(name) {
            tabs.forEach(function (tab) {
                const isActive = tab.getAttribute('data-vellum-tab') === name;
                tab.classList.toggle('border-stone-900', isActive);
                tab.classList.toggle('dark:border-stone-100', isActive);
                tab.classList.toggle('border-transparent', !isActive);
                tab.classList.toggle('text-stone-400', !isActive);
            });

            const showPreview = name === 'preview';
            preview.hidden = !showPreview;
            body.hidden = showPreview;

            if (showPreview) {
                renderPreview();
            }
        }

        function renderPreview() {
            preview.innerHTML = '<p class="text-stone-400">…</p>';

            fetch(previewUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: 'body=' + encodeURIComponent(body.value),
            })
                .then(function (response) {
                    return response.text();
                })
                .then(function (html) {
                    preview.innerHTML = html;
                })
                .catch(function () {
                    preview.innerHTML = '<p class="text-red-600">Preview unavailable.</p>';
                });
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                selectTab(tab.getAttribute('data-vellum-tab'));
            });
        });
    }

    function initConfirms() {
        document.querySelectorAll('[data-vellum-confirm]').forEach(function (trigger) {
            trigger.addEventListener('click', function (event) {
                if (!window.confirm(trigger.getAttribute('data-vellum-confirm'))) {
                    event.preventDefault();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-vellum-editor]').forEach(initEditor);
        initConfirms();
    });
})();
