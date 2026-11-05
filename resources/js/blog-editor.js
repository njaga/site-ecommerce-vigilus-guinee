// Simple Markdown aids: no external editor, no HTML injection, preserves
// native textarea editing and keyboard shortcuts.
const editor = document.querySelector('#blog-body');

if (editor) {
    const wrap = {
        heading: text => '\n## ' + (text || 'Sous-titre') + '\n',
        bold: text => '**' + (text || 'Texte en gras') + '**',
        list: text => '\n- ' + (text || 'Élément de liste') + '\n',
        link: text => '[' + (text || 'Texte du lien') + '](https://)',
        quote: text => '\n> ' + (text || 'Citation') + '\n',
    };

    for (const button of document.querySelectorAll('[data-blog-tool]')) {
        button.addEventListener('click', () => {
            const formatter = wrap[button.dataset.blogTool];
            if (!formatter) return;
            const start = editor.selectionStart;
            const end = editor.selectionEnd;
            const selected = editor.value.slice(start, end);
            editor.setRangeText(formatter(selected), start, end, 'end');
            editor.focus();
            editor.dispatchEvent(new Event('input', { bubbles: true }));
        });
    }
}
