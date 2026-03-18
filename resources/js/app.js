import './bootstrap';

import $ from 'jquery';
import Alpine from 'alpinejs';

window.$ = $;
window.jQuery = $;
window.Alpine = Alpine;

Alpine.start();

async function copyTextToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(text);
        return;
    }

    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
}

$(document).on('click', '.js-copy-short-url', async function () {
    const button = $(this);
    const shortUrl = button.data('short-url');

    if (!shortUrl) {
        return;
    }

    const defaultText = button.data('default-text') || 'Copy';

    try {
        await copyTextToClipboard(shortUrl);
        button.text('Copied!');

        setTimeout(() => {
            button.text(defaultText);
        }, 1500);
    } catch (error) {
        button.text('Copy failed');

        setTimeout(() => {
            button.text(defaultText);
        }, 1500);
    }
});
