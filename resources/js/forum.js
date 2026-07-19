import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import '../css/forum.css';

const toolbar = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'code-block'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['link'],
    ['clean'],
];

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-quill-form]').forEach((form) => {
        const editorElement = form.querySelector('[data-quill-editor]');
        const input = form.querySelector('[data-quill-input]');
        if (!editorElement || !input) return;

        const quill = new Quill(editorElement, {
            theme: 'snow',
            placeholder: editorElement.dataset.placeholder || 'İletinizi yazın...',
            modules: { toolbar },
        });
        form._forumQuill = quill;

        form.addEventListener('submit', (event) => {
            input.value = quill.root.innerHTML;
            if (!quill.getText().trim()) {
                event.preventDefault();
                editorElement.classList.add('forum-editor-error');
                quill.focus();
            }
        });
    });

    const composer = document.querySelector('[data-reply-composer]');
    if (!composer) return;

    const openComposer = (postId = '', name = '') => {
        composer.classList.add('is-open');
        const parentInput = composer.querySelector('[data-parent-input]');
        const context = composer.querySelector('[data-reply-context]');
        if (parentInput) parentInput.value = postId;
        if (context) context.textContent = name ? `${name} kullanıcısına yanıt` : 'Yanıt yazıyorsunuz';
        composer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        window.setTimeout(() => composer.querySelector('form')._forumQuill?.focus(), 350);
    };

    document.querySelectorAll('[data-reply-open]').forEach((button) => button.addEventListener('click', () => openComposer()));
    document.querySelectorAll('[data-reply-to]').forEach((button) => button.addEventListener('click', () => {
        openComposer(button.dataset.replyTo, button.dataset.replyName);
    }));
    document.querySelectorAll('[data-reply-close]').forEach((button) => button.addEventListener('click', () => composer.classList.remove('is-open')));
});
