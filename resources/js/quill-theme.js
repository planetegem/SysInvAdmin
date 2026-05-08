import Quill from 'quill';
import BaseTheme, { BaseTooltip } from 'quill/themes/base';
import Emitter from 'quill/core/emitter';
import LinkBlot from 'quill/formats/link';
import SnowTheme from 'quill/themes/snow';


// Own version of a tooltip, used when editing anchors; 
// copied code and css from the Snow tooltip and worked from there
class SnowlikeTooltip extends BaseTooltip {
    static TEMPLATE = [
        '<a class="ql-preview" rel="noopener noreferrer" target="_blank" href="about:blank"></a>',
        '<input type="text" data-formula="e=mc^2" data-link="https://planetegem.be" data-video="Embed URL">',
        '<a class="ql-action"></a>',
        '<a class="ql-remove"></a>'
    ].join('');

    preview = this.root.querySelector('a.ql-preview');

    listen() {
        super.listen();
        this.root.querySelector('a.ql-action').addEventListener('click', event => {
            if (this.root.classList.contains('ql-editing')) {
                this.save();
            } else {
                this.edit('link', this.preview.textContent);
            }
            event.preventDefault();
        });

        this.root.querySelector('a.ql-remove').addEventListener('click', event => {
            if (this.linkRange != null) {
                const range = this.linkRange;
                this.restoreFocus();
                this.quill.formatText(range, 'link', false, Emitter.sources.USER);
                delete this.linkRange;
            }
            event.preventDefault();
            this.hide();
        });

        this.quill.on(Emitter.events.SELECTION_CHANGE, (range, oldRange, source) => {
            if (range == null) return;
            if (range.length === 0 && source === Emitter.sources.USER) {
                const [link, offset] = this.quill.scroll.descendant(LinkBlot, range.index);
                if (link != null) {
                    this.linkRange = { index: range.index - offset, length: link.length() };
                    const preview = LinkBlot.formats(link.domNode);
                    this.preview.textContent = preview;
                    this.preview.setAttribute('href', preview);
                    this.show();

                    const bounds = this.quill.getBounds(this.linkRange);
                    if (bounds != null) {
                        this.position(bounds);
                    }
                    return;
                }
            } else {
                delete this.linkRange;
            }
            this.hide();
        });
    }
    show() {
        super.show();
        this.root.removeAttribute('data-mode');
    }
}

// Custom theme for the quill editor
export default class SysInvAdminTheme extends BaseTheme {
    constructor(quill, options) {
        super(quill, options);
        this.quill.container.classList.add('ql-sysinvadmin');
    }
    extendToolbar(toolbar) {
        toolbar.container.classList.add('ql-sysinvadmin');

        this.tooltip = new SnowlikeTooltip(this.quill, this.options.bounds);

        this.quill.getModule('toolbar').addHandler('link', () => {
            const range = this.quill.getSelection();
            if (range == null || range.length === 0) {
                console.log('no range"');
                return;
            }
            this.tooltip.edit('link', '');
        });
    }
}

