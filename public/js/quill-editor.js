export default class QuillBlock {
    // State
    mode = "quill";

    // Element IDs
    quillEditorId;
    quillToolbarId;
    sourceBlockId;
    contentInputId;
    typeInputId;

    // Elements
    quill;
    sourceContainer;
    sourceEditor;
    sourceButton;
    inputBody;
    inputType;

    constructor(elements) {
        // Keep IDs of important elements saved
        this.quillEditorId = elements.editor;
        this.quillToolbarId = elements.toolbar;
        this.sourceBlockId = elements.source;
        this.contentInputId = elements.content_input;
        this.typeInputId = elements.type_input;

        // Instantiate a quill editor
        this.quill = new Quill(this.quillEditorId, {
            theme: 'sysinvadmin',
            placeholder: 'Start typing here...',
            modules: {
                toolbar: this.quillToolbarId
            }
        });

        // Prepare source editor elements
        this.sourceContainer = document.querySelector(this.sourceBlockId);
        this.sourceContainer.classList.add("ql-source-code-container");
        this.sourceContainer.classList.add("ql-sysinvadmin");
        this.sourceEditor = document.createElement("textarea");
        this.sourceEditor.addEventListener("input", () => this.resizeSourceEditor());
        this.sourceEditor.placeholder = '<hint>Start typing here...</hint>';
        this.sourceContainer.appendChild(this.sourceEditor);
        this.sourceEditor.setAttribute('rows', '1');

        // Button to toggle between source and quill editor
        this.sourceButton = document.querySelector(this.quillToolbarId + ' button.source-code-button');
        this.toggleMode = this.toggleMode.bind(this);
        this.sourceButton.addEventListener('click', () => this.toggleMode());

        // Input elements to interface with database: type determines mode (quill / source), content is the html
        this.inputBody = document.querySelector(this.contentInputId);
        this.inputType = document.querySelector(this.typeInputId);

        // Preload input values (important when updating existing item)
        switch (this.inputType.value) {
            case "source":
                this.mode = "quill";
                this.toggleMode(this.inputBody.value);
                break;
            case "quill":
                this.mode = "source";
                this.toggleMode(this.inputBody.value);
                break;
            default:
                this.mode = "source";
                this.toggleMode();
        }

        // Intercept form submission to update relevant inputs
        this.sourceEditor.closest('form').addEventListener("submit", () => {
            this.submit();
        });
    }

    // UI feature: evaluate height of source textarea whenever change occurs (shrink or grow)
    resizeSourceEditor() {
        this.sourceEditor.style.height = 'auto';
        this.sourceEditor.style.height = this.sourceEditor.scrollHeight + 'px';
    }

    // Toggle between source and quill, with parameter for predefined html (when updating existing item)
    toggleMode(predefinedHtml = null) {
        const toolbarButtons = document.querySelectorAll(this.quillToolbarId + ' button');

        // Currently in source mode, switching to quill mode
        if (this.mode == "source") {
            Array.from(toolbarButtons).map((button) => button.disabled = false);

            this.sourceButton.classList.remove('ql-active');
            this.sourceContainer.classList.add("disabled");
            document.querySelector(this.quillEditorId).classList.remove("disabled");

            const html = predefinedHtml ?? this.sourceEditor.value;
            this.quill.root.innerHTML = this.formatSourceHTML(html);

            this.mode = "quill";

        // Currently in quill mode, switching to source mode
        } else {
            Array.from(toolbarButtons).map((button) => {
                if (button != this.sourceButton) button.disabled = true;
            });

            this.sourceButton.classList.add('ql-active');
            this.sourceContainer.classList.remove("disabled");
            document.querySelector(this.quillEditorId).classList.add("disabled");

            const html = beautifyHtml(predefinedHtml ?? this.cleanQuillHTML(this.quill.root.innerHTML));
            this.sourceEditor.value = html;
            this.resizeSourceEditor();

            this.mode = "source";
        }
    }

    // On form submission: update type & content inputs
    submit() {
        this.inputBody.value = (this.mode == "source") ? this.sourceEditor.value : this.cleanQuillHTML(this.quill.root.innerHTML);
        this.inputType.value = this.mode;
    }

    // LOGIC TO SWITCH FROM QUILL TO NORMAL HTML
    // Index function: takes html, parses it and returns cleaned version
    cleanQuillHTML(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        this.removeQuillArtifacts(doc);
        this.normalizeQuillLists(doc);

        return doc.body.innerHTML;
    }
    // Remove weird artifacts from quill html
    removeQuillArtifacts(doc) {
        doc.querySelectorAll('span.ql-ui').forEach(el => el.remove());
        doc.querySelectorAll('p > br:only-child').forEach(el => el.closest('p').remove());
    }
    // Normalize quill lists: ol & ul instead of always ul with weird param on li
    normalizeQuillLists(doc) {
        doc.querySelectorAll('ol, ul').forEach(list => {
            const items = Array.from(list.querySelectorAll(':scope > li'));
            if (!items.length) return;

            const fragment = document.createDocumentFragment();
            let currentList = null;
            let currentType = null;

            items.forEach(li => {
                const type = li.getAttribute('data-list') === 'ordered' ? 'ol' : 'ul';

                if (type !== currentType) {
                    currentList = document.createElement(type);
                    fragment.appendChild(currentList);
                    currentType = type;
                }

                const cleanLi = document.createElement('li');
                cleanLi.innerHTML = li.innerHTML;
                cleanLi.removeAttribute('data-list');
                currentList.appendChild(cleanLi);
            });

            list.replaceWith(fragment);
        });
    }

    // LOGIC TO SWITCH NORMAL HTML TO QUILL
    // Index function: takes html, returns dirty dirty quill html
    formatSourceHTML(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        this.restoreQuillLists(doc);

        return this.stripWhiteSpacesforQuill(doc.body.innerHTML);
    }
    // Restore quill lists to their weirdness
    restoreQuillLists(doc) {
        doc.querySelectorAll('li').forEach(li => {
            const container = li.parentElement.tagName;

            if (container === "UL") {
                li.setAttribute('data-list', 'bullet');
            } else if (container === "OL") {
                li.setAttribute('data-list', 'ordered');
            }
        });
        doc.querySelectorAll('ul').forEach(ul => {
            const ol = document.createElement('ol');
            ol.innerHTML = ul.innerHTML;
            ul.replaceWith(ol);
        });
    }
    // Take away white spaces (else quill tends to end <p><br></p> in random places)
    stripWhiteSpacesforQuill(html) {
        return html
            .replace(/>\s+</g, '><').trim();
    }
}