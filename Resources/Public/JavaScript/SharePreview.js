define([], function () {
    'use strict';

    document.addEventListener('click', async function (event) {
        const button = event.target.closest('[data-share-action]');
        if (!button) return;
        const panel = button.closest('.yoast-news-share');
        if (!panel) return;
        event.preventDefault();
        const status = panel.querySelector('[data-share-status]');
        const input = panel.querySelector('[data-share-url]');
        const action = button.dataset.shareAction;
        if (action === 'copy') {
            try {
                await navigator.clipboard.writeText(input.value);
                status.textContent = 'Link kopiert.';
            } catch (error) {
                input.focus();
                input.select();
                status.textContent = 'Bitte den markierten Link kopieren.';
            }
            return;
        }
        const buttons = panel.querySelectorAll('button');
        buttons.forEach(function (item) { item.disabled = true; });
        status.textContent = action === 'create' ? 'Link wird erstellt …' : 'Link wird widerrufen …';
        try {
            const response = await fetch(panel.dataset.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ uid: panel.dataset.uid, action: action })
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Der Link konnte nicht erstellt werden.');
            if (action === 'revoke') {
                input.value = '';
                panel.querySelector('[data-share-result]').hidden = true;
                panel.querySelector('[data-share-mail]').removeAttribute('href');
                panel.querySelector('[data-share-open]').removeAttribute('href');
                status.textContent = 'Alle bisherigen Freigabelinks für diese News wurden widerrufen.';
            } else {
                input.value = data.url;
                panel.querySelector('[data-share-result]').hidden = false;
                panel.querySelector('[data-share-expiry]').textContent = 'Gültig bis ' + new Date(data.expires * 1000).toLocaleString();
                panel.querySelector('[data-share-open]').href = data.url;
                panel.querySelector('[data-share-mail]').href = 'mailto:?subject='
                    + encodeURIComponent('News zur Abnahme: ' + panel.dataset.title)
                    + '&body=' + encodeURIComponent('Bitte diese News prüfen:\n\n' + data.url
                        + '\n\nGültig bis ' + new Date(data.expires * 1000).toLocaleString());
                status.textContent = 'Freigabelink erstellt. Bisherige Links sind jetzt ungültig.';
            }
        } catch (error) {
            status.textContent = error.message || 'Die Anfrage ist fehlgeschlagen.';
        } finally {
            buttons.forEach(function (item) { item.disabled = false; });
        }
    });
});

export {};
