/**
 * Tiptap Editor - Admin (thay TinyMCE). Load với type="module".
 */
const editors = new Map();
const editorMeta = new Map();

window.getTiptapEditor = function (id) {
  return editors.get(id) || null;
};

window.setTiptapContent = function (id, html) {
  const editor = editors.get(id);
  const meta = editorMeta.get(id);
  const htmlContent = html || '';
  if (meta) {
    meta.originalHtml = htmlContent;
  }
  if (editor) {
    editor.commands.setContent(htmlContent, false);
  }
  if (meta && meta.htmlSourceTextarea) {
    meta.htmlSourceTextarea.value = htmlContent;
  }
  const textarea = document.getElementById(id);
  if (textarea) {
    textarea.value = htmlContent;
  }
};

window.initTiptapEditors = async function () {
  const { Editor } = await import('https://esm.sh/@tiptap/core@2.10.3');
  const StarterKit = (await import('https://esm.sh/@tiptap/starter-kit@2.10.3')).default;
  const Link = (await import('https://esm.sh/@tiptap/extension-link@2.10.3')).default;
  const Image = (await import('https://esm.sh/@tiptap/extension-image@2.10.3')).default;
  const Table = (await import('https://esm.sh/@tiptap/extension-table@2.10.3')).default;
  const TableRow = (await import('https://esm.sh/@tiptap/extension-table-row@2.10.3')).default;
  const TableCell = (await import('https://esm.sh/@tiptap/extension-table-cell@2.10.3')).default;
  const TableHeader = (await import('https://esm.sh/@tiptap/extension-table-header@2.10.3')).default;
  const Underline = (await import('https://esm.sh/@tiptap/extension-underline@2.10.3')).default;
  const TextAlign = (await import('https://esm.sh/@tiptap/extension-text-align@2.10.3')).default;
  const Placeholder = (await import('https://esm.sh/@tiptap/extension-placeholder@2.10.3')).default;

  document.querySelectorAll('.tiptap-editor-wrapper').forEach(function (wrapper) {
    const textarea = wrapper.querySelector('textarea.tiptap-textarea');
    if (!textarea) return;
    const id = textarea.id;
    if (!id || editors.has(id)) return;

    const editorEl = document.createElement('div');
    editorEl.className = 'tiptap-editor-box';
    editorEl.setAttribute('data-textarea-id', id);
    textarea.style.display = 'none';
    wrapper.appendChild(editorEl);

    const originalHtml = textarea.value || '';
    
    editorMeta.set(id, { editor: null, wrapper, isHtmlMode: false, htmlSourceTextarea: null, originalHtml: originalHtml });
    const meta = editorMeta.get(id);

    const editor = new Editor({
      element: editorEl,
      extensions: [
        StarterKit.configure({
          codeBlock: { HTMLAttributes: { class: 'tiptap-code-block' } },
        }),
        Link.configure({ 
          openOnClick: false, 
          HTMLAttributes: { target: '_blank', rel: 'noopener' },
        }),
        Image.configure({ 
          allowBase64: true,
        }),
        Table.configure({ resizable: true }),
        TableRow,
        TableHeader,
        TableCell,
        Underline,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Placeholder.configure({ placeholder: 'Nhập nội dung...' }),
      ],
      content: originalHtml,
      editorProps: {
        attributes: {
          class: 'tiptap-prose',
        },
      },
      onUpdate: ({ editor: ed }) => {
        const meta = editorMeta.get(id);
        if (meta && !meta.isHtmlMode) {
          const html = ed.getHTML();
          meta.originalHtml = html;
          textarea.value = html;
        }
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
      },
    });

    editors.set(id, editor);
    meta.editor = editor;

    /* Chế độ xem/sửa mã HTML */
    const htmlSourceWrap = document.createElement('div');
    htmlSourceWrap.className = 'tiptap-html-source-wrap';
    htmlSourceWrap.style.display = 'none';
    const htmlSourceTextarea = document.createElement('textarea');
    htmlSourceTextarea.className = 'tiptap-html-source-textarea';
    htmlSourceTextarea.rows = 20;
    htmlSourceTextarea.placeholder = 'Nhập hoặc dán mã HTML...';
    htmlSourceWrap.appendChild(htmlSourceTextarea);
    wrapper.appendChild(htmlSourceWrap);

    meta.htmlSourceTextarea = htmlSourceTextarea;

    /* Textarea đơn giản cho HTML source - không dùng overlay để tránh lỗi con trỏ */
    htmlSourceWrap.appendChild(htmlSourceTextarea);

    function toggleHtmlMode() {
      meta.isHtmlMode = !meta.isHtmlMode;
      if (meta.isHtmlMode) {
        const content = meta.originalHtml || textarea.value || editor.getHTML();
        editorEl.style.display = 'none';
        htmlSourceWrap.style.display = 'block';
        htmlSourceBtn.title = 'Thoát chế độ mã HTML';
        htmlSourceBtn.innerHTML = 'Soạn thảo';
        htmlSourceBtn.classList.add('tiptap-toolbar-btn--active');

        htmlSourceTextarea.value = content;
      } else {
        const htmlContent = htmlSourceTextarea.value || '';
        meta.originalHtml = htmlContent;
        textarea.value = htmlContent;
        editor.commands.setContent(htmlContent, false);
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        htmlSourceWrap.style.display = 'none';
        editorEl.style.display = '';
        htmlSourceBtn.title = 'Chỉnh sửa mã HTML';
        htmlSourceBtn.innerHTML = 'HTML';
        htmlSourceBtn.classList.remove('tiptap-toolbar-btn--active');
      }
    }

    const toolbar = document.createElement('div');
    toolbar.className = 'tiptap-toolbar';
    wrapper.insertBefore(toolbar, editorEl);

    function addBtn(title, onClick, icon) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.title = title;
      btn.className = 'tiptap-toolbar-btn';
      btn.innerHTML = icon || title.charAt(0);
      btn.addEventListener('click', (e) => { e.preventDefault(); onClick(); });
      toolbar.appendChild(btn);
      return btn;
    }

    function sep() {
      const s = document.createElement('span');
      s.className = 'tiptap-toolbar-sep';
      toolbar.appendChild(s);
    }

    const htmlSourceBtn = addBtn('Chỉnh sửa mã HTML', toggleHtmlMode, 'HTML');
    sep();
    addBtn('In đậm', () => editor.chain().focus().toggleBold().run(), '<b>B</b>');
    addBtn('In nghiêng', () => editor.chain().focus().toggleItalic().run(), '<i>I</i>');
    addBtn('Gạch chân', () => editor.chain().focus().toggleUnderline().run(), '<u>U</u>');
    addBtn('Gạch ngang', () => editor.chain().focus().toggleStrike().run(), '<s>S</s>');
    sep();
    addBtn('Tiêu đề 1', () => editor.chain().focus().toggleHeading({ level: 1 }).run(), 'H1');
    addBtn('Tiêu đề 2', () => editor.chain().focus().toggleHeading({ level: 2 }).run(), 'H2');
    addBtn('Tiêu đề 3', () => editor.chain().focus().toggleHeading({ level: 3 }).run(), 'H3');
    sep();
    addBtn('Danh sách bullet', () => editor.chain().focus().toggleBulletList().run(), '•');
    addBtn('Danh sách số', () => editor.chain().focus().toggleOrderedList().run(), '1.');
    addBtn('Trích dẫn', () => editor.chain().focus().toggleBlockquote().run(), '❝');
    sep();
    addBtn('Chèn link', () => {
      const url = window.prompt('URL:');
      if (url) editor.chain().focus().setLink({ href: url }).run();
    }, '🔗');
    addBtn('Chèn ảnh', () => {
      const url = window.prompt('URL ảnh:');
      if (url) editor.chain().focus().setImage({ src: url }).run();
    }, '🖼');
    addBtn('Chèn bảng', () => editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(), '▦');
    sep();
    addBtn('Căn trái', () => editor.chain().focus().setTextAlign('left').run(), '⬅');
    addBtn('Căn giữa', () => editor.chain().focus().setTextAlign('center').run(), '↔');
    addBtn('Căn phải', () => editor.chain().focus().setTextAlign('right').run(), '➡');
    sep();
    addBtn('Code', () => editor.chain().focus().toggleCode().run(), '</>');
    addBtn('Xóa định dạng', () => editor.chain().focus().clearNodes().unsetAllMarks().run(), '✕');
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', run);
} else {
  run();
}

function run() {
  if (document.querySelector('.tiptap-editor-wrapper')) {
    window.initTiptapEditors().catch(function (err) {
      console.error('Tiptap init error:', err);
    });
  }
}

document.addEventListener('submit', function (e) {
  editors.forEach(function (editor, id) {
    const ta = document.getElementById(id);
    if (!ta || ta.form !== e.target) return;
    const meta = editorMeta.get(id);
    if (meta && meta.isHtmlMode && meta.htmlSourceTextarea) {
      ta.value = meta.htmlSourceTextarea.value;
    } else if (meta && meta.originalHtml) {
      ta.value = meta.originalHtml;
    } else {
      ta.value = editor.getHTML();
    }
  });
}, true);
