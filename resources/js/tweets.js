"use strict";

const decodeContent = (element) => {
    const encoding = element.dataset.contentEncoding;
    const rawContent = element.dataset.content ?? '';

    if (encoding === 'base64') {
        try {
            return atob(rawContent);
        } catch (error) {
            console.warn('Unable to decode tweet content', error);
            return rawContent;
        }
    }

    return rawContent;
};

const toggleEditForm = (tweetId, show) => {
    const content = document.querySelector(`.tweet-content[data-tweet-id="${tweetId}"]`);
    const form = document.querySelector(`.tweet-edit-form[data-tweet-id="${tweetId}"]`);

    if (!content || !form) {
        return;
    }

    if (show) {
        content.classList.add('hidden');
        form.classList.remove('hidden');
        const textarea = form.querySelector('textarea');
        if (textarea) {
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }
    } else {
        content.classList.remove('hidden');
        form.classList.add('hidden');
    }
};

document.addEventListener('click', async (event) => {
    const copyButton = event.target.closest('.tweet-copy');
    if (copyButton) {
        const text = decodeContent(copyButton);

        try {
            await navigator.clipboard.writeText(text);
            const originalLabel = copyButton.dataset.originalLabel ?? copyButton.textContent;
            copyButton.dataset.originalLabel = originalLabel;
            copyButton.textContent = 'Copied!';

            setTimeout(() => {
                copyButton.textContent = copyButton.dataset.originalLabel;
            }, 2000);
        } catch (error) {
            console.error('Clipboard copy failed', error);
        }
        return;
    }

    const editToggle = event.target.closest('.tweet-edit-toggle');
    if (editToggle) {
        toggleEditForm(editToggle.dataset.tweetId, true);
        return;
    }

    const editCancel = event.target.closest('.tweet-edit-cancel');
    if (editCancel) {
        toggleEditForm(editCancel.dataset.tweetId, false);
    }
});

