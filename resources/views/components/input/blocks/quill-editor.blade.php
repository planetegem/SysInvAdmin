<section class="quill-editor-block">
    <h5 class="label">
        {!! __($label) !!}
        @if($attributes->has('required')) (*) @endif
    </h5>
    @if($tooltip)
        <x-info-tooltip text="{!! __($tooltip) !!}" />
    @endif
    <div id="{{ $id }}-toolbar">
        <span class="ql-formats">
            <button type="button" class="ql-bold">
                {!! file_get_contents("styles/icons/bold_icon.svg") !!}
            </button>
            <button type="button" class="ql-italic">
                {!! file_get_contents("styles/icons/italic_icon.svg") !!}
            </button>
            <button type="button" class="ql-underline">
                {!! file_get_contents("styles/icons/underline_icon.svg") !!}
            </button>
            <button type="button" class="ql-link">
                {!! file_get_contents("styles/icons/link_icon.svg") !!}
            </button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-list" value="ordered">
                {!! file_get_contents("styles/icons/ol_icon.svg") !!}
            </button>
            <button type="button" class="ql-list" value="bullet">
                {!! file_get_contents("styles/icons/ul_icon.svg") !!}
            </button>
        </span>
        <span class="ql-formats">
            <button type="button" class="source-code-button">
                {!! file_get_contents("styles/icons/source_code_icon.svg") !!}
            </button>
        </span>
    </div>
    
    <div id="{{ $id }}-editor"></div>

    <div id="{{ $id }}-source"></div>

    <input type="hidden" name="{{ $name }}[content]" id="{{ $id }}-content" value="{!! str_replace('"', "'", $content_body) !!}">
    <input type="hidden" name="{{ $name }}[type]" id="{{ $id }}-type" value="{{ $content_type }}">

    <script type="module">
        import QuillBlock from "/js/quill-editor.js";

        document.addEventListener('DOMContentLoaded', () => {
            const quillBlock = new QuillBlock({
                editor: '#{{ $id }}-editor',
                toolbar: '#{{ $id }}-toolbar',
                source: '#{{ $id }}-source',
                content_input: '#{{ $id }}-content',
                type_input: '#{{ $id }}-type'
            });
        });
    </script>
</section>