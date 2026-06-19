<x-info-tooltip text="{!! __('tooltip.item.media_type') !!}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="media-manager" id="{{ $id }}">
    <div class="media-selector">
        <x-input.dropdown name="{{ $id }}[type]" label="media.type">
            <option value="none">
                <span>{{ __('media.types.none') }}</span>
            </option>
            <option value="image">
                <span>{{ __('media.types.image') }}</span>
            </option>
            <option value="image-list">
                <span>{{ __('media.types.list') }}</span>
            </option>
            <option value="carousel">
                <span>{{ __('media.types.carousel') }}</span>
            </option>
        </x-input.dropdown>
        <input type="file" name="{{ $id }}[file]" id="{{ $id }}[file]" />
        <label for="{{ $id }}[file]" class="action-button passive">{{ __('button.select_image') }}</label>
    </div>

    <div class="media-content" id="{{ $id }}[content]">
        <div class="simple-divider"></div>

        <button type="button" class="cycle-button left">
            {!! file_get_contents("styles/icons/arrow_circle_icon.svg") !!}
        </button>
        <template class="image-preview">
            <section class="media-preview-container">
                <div class="media-options">
                    <x-input.textarea name="{{ $id }}[alt]" tooltip="tooltip.media.image_alt" label="media.image_alt"
                        maxlength="150" value="" small optional />
                    <div class="image-description"></div>
                    <x-input.checkbox name="{{ $id }}[convert_to_webp]" tooltip="tooltip.media.webp"
                        label="media.webp" />
                    <input type="hidden" name="{{ $id }}[path]" class="image_path" />
                </div>
                <div class="media-preview something-selected">
                    <figure class="preview-figure"></figure>
                    <span class="preview-label"></span>
                    <input type="hidden" name="{{ $id }}[name]" class="image_name" </div>
            </section>
        </template>
        <button type="button" class="cycle-button right">
            {!! file_get_contents("styles/icons/arrow_circle_icon.svg") !!}
        </button>
    </div>

    <script type="module">
        import MediaManager from "/js/media-manager.js";
        const mediaManager = new MediaManager(
            "{{ $id }}",
            @json(__('media.manager'))
        ).prefill({
                type: "{{ $item->file_type ?? 'none' }}",
                files: [
                    @foreach ($item->media as $medium)
                                                {
                            path: "{{ $medium->file_path }}",
                            name: "{{ $medium->file_name }}",
                            alt: "{!! $medium->alt !!}"
                        },
                    @endforeach
                ]
            });
    </script>
</main>