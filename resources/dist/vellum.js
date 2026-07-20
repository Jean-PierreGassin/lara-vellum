(function () {
    'use strict';

    const THEME_KEY = 'vellum.theme';
    const VIEW_KEY = 'vellum.editor.view';
    const PREVIEW_DEBOUNCE_MS = 350;
    const WORDS_PER_MINUTE = 200;

    function storage() {
        try {
            return window.localStorage;
        } catch (error) {
            return null;
        }
    }

    function read(key) {
        const store = storage();

        return store ? store.getItem(key) : null;
    }

    function write(key, value) {
        const store = storage();

        if (store) {
            store.setItem(key, value);
        }
    }

    /*
     * Applied before first paint (the stylesheet reads the attribute) so an
     * explicit theme choice never flashes the system one first.
     */
    function applyTheme(theme) {
        const root = document.documentElement;

        if (theme === 'light' || theme === 'dark') {
            root.setAttribute('data-vellum-theme', theme);
        } else {
            root.removeAttribute('data-vellum-theme');
        }
    }

    function prefersDark() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function initThemeToggle(toggle) {
        function sync() {
            const isDark = (read(THEME_KEY) || (prefersDark() ? 'dark' : 'light')) === 'dark';

            toggle.setAttribute('aria-pressed', String(isDark));
            toggle.querySelectorAll('[data-vellum-theme-icon]').forEach(function (icon) {
                icon.hidden = (icon.getAttribute('data-vellum-theme-icon') === 'dark') !== isDark;
            });
        }

        toggle.addEventListener('click', function () {
            const next = toggle.getAttribute('aria-pressed') === 'true' ? 'light' : 'dark';

            write(THEME_KEY, next);
            applyTheme(next);
            sync();
        });

        toggle.hidden = false;
        sync();
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');

        return meta ? meta.getAttribute('content') : '';
    }

    function countWords(text) {
        const trimmed = text.trim();

        return trimmed === '' ? 0 : trimmed.split(/\s+/).length;
    }

    function fill(template, replacements) {
        return Object.keys(replacements).reduce(function (result, token) {
            return result.split(':' + token).join(replacements[token]);
        }, template);
    }

    function initEditor(editor) {
        const form = editor.closest('form');
        const body = editor.querySelector('[data-vellum-body]');
        const preview = editor.querySelector('[data-vellum-preview]');
        const panes = editor.querySelector('[data-vellum-panes]');
        const writePane = editor.querySelector('[data-vellum-pane="write"]');
        const previewPane = editor.querySelector('[data-vellum-pane="preview"]');
        const tablist = editor.querySelector('[data-vellum-tablist]');
        const tabs = Array.from(editor.querySelectorAll('[data-vellum-tab]'));
        const stats = editor.querySelector('[data-vellum-stats]');
        const previewUrl = editor.getAttribute('data-preview-url');

        let renderedFor = null;
        let debounce = null;
        let request = null;
        let view = 'write';

        function showStatus(name, tone) {
            const paragraph = document.createElement('p');

            paragraph.className = tone;
            paragraph.textContent = preview.getAttribute('data-' + name + '-label') || '';
            preview.replaceChildren(paragraph);
        }

        function renderPreview() {
            const markdown = body.value;

            if (markdown.trim() === '') {
                renderedFor = markdown;
                showStatus('empty', 'text-stone-400');

                return;
            }

            if (markdown === renderedFor) {
                return;
            }

            if (request) {
                request.abort();
            }

            request = new AbortController();

            fetch(previewUrl, {
                method: 'POST',
                signal: request.signal,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: 'body=' + encodeURIComponent(markdown),
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Preview request failed.');
                    }

                    return response.text();
                })
                .then(function (html) {
                    renderedFor = markdown;
                    preview.innerHTML = html;
                })
                .catch(function (error) {
                    if (error.name !== 'AbortError') {
                        showStatus('error', 'text-red-600');
                    }
                });
        }

        function schedulePreview() {
            if (view === 'write') {
                return;
            }

            window.clearTimeout(debounce);
            debounce = window.setTimeout(renderPreview, PREVIEW_DEBOUNCE_MS);
        }

        function selectView(name) {
            view = name;

            tabs.forEach(function (tab) {
                const isActive = tab.getAttribute('data-vellum-tab') === name;

                tab.classList.toggle('vellum-tab-active', isActive);
                tab.setAttribute('aria-selected', String(isActive));
                tab.setAttribute('tabindex', isActive ? '0' : '-1');
            });

            writePane.hidden = name === 'preview';
            previewPane.hidden = name === 'write';
            panes.classList.toggle('lg:grid-cols-2', name === 'split');

            write(VIEW_KEY, name);

            if (name !== 'write') {
                renderPreview();
            }
        }

        function updateStats() {
            const words = countWords(body.value);

            stats.textContent = fill(stats.getAttribute('data-template') || '', {
                words: words.toLocaleString(),
                minutes: Math.max(1, Math.ceil(words / WORDS_PER_MINUTE)),
            });
        }

        function focusTab(offset) {
            const visible = tabs.filter(function (tab) {
                return tab.offsetParent !== null;
            });
            const current = visible.indexOf(document.activeElement);
            const target = visible[(current + offset + visible.length) % visible.length];

            target.focus();
            selectView(target.getAttribute('data-vellum-tab'));
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                selectView(tab.getAttribute('data-vellum-tab'));
            });

            tab.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
                    event.preventDefault();
                    focusTab(event.key === 'ArrowRight' ? 1 : -1);
                }
            });
        });

        body.addEventListener('input', function () {
            updateStats();
            schedulePreview();
        });

        tablist.hidden = false;

        const remembered = read(VIEW_KEY);
        const fitsSplit = window.matchMedia('(min-width: 1024px)').matches;

        selectView(remembered === 'split' && !fitsSplit ? 'write' : remembered || 'write');
        updateStats();

        return form;
    }

    /*
     * Warns before navigating away from edits that were never submitted. The
     * baseline is captured on load and cleared once the form is on its way.
     */
    function initUnsavedGuard(form) {
        function snapshot() {
            return new URLSearchParams(new FormData(form)).toString();
        }

        let baseline = snapshot();

        form.addEventListener('submit', function () {
            baseline = null;
        });

        window.addEventListener('beforeunload', function (event) {
            if (baseline !== null && snapshot() !== baseline) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
    }

    function initSaveShortcut(form) {
        document.addEventListener('keydown', function (event) {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 's') {
                event.preventDefault();
                form.requestSubmit();
            }
        });
    }

    /*
     * The destructive confirmation is a <details> element, so it still works
     * without JavaScript. This only layers the conveniences on top.
     */
    function initConfirm(confirmation) {
        confirmation.querySelectorAll('[data-vellum-confirm-cancel]').forEach(function (cancel) {
            cancel.addEventListener('click', function () {
                confirmation.open = false;
                confirmation.querySelector('summary').focus();
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && confirmation.open) {
                confirmation.open = false;
            }
        });

        document.addEventListener('click', function (event) {
            if (confirmation.open && !confirmation.contains(event.target)) {
                confirmation.open = false;
            }
        });
    }

    function initDismiss(button) {
        button.hidden = false;

        button.addEventListener('click', function () {
            button.closest('[data-vellum-flash]').remove();
        });
    }

    applyTheme(read(THEME_KEY));

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-vellum-theme-toggle]').forEach(initThemeToggle);
        document.querySelectorAll('[data-vellum-dismiss]').forEach(initDismiss);
        document.querySelectorAll('[data-vellum-confirm]').forEach(initConfirm);

        document.querySelectorAll('[data-vellum-editor]').forEach(function (editor) {
            initUnsavedGuard(initEditor(editor));
            initSaveShortcut(editor.closest('form'));
        });
    });
})();
