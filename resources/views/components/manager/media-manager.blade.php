<x-info-tooltip text="{!! __('tooltip.media.type') !!}" />
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
            <option value="video">
                <span>{{ __('media.types.video') }}</span>
            </option>
            <option value="gifv">
                <span>{{ __('media.types.gifv') }}</span>
            </option>
            <option value="3d-model">
                <span>{{ __('media.types.model') }}</span>
            </option>
        </x-input.dropdown>
        <input type="file" name="{{ $id }}[file]" id="{{ $id }}[file]" />
        <label for="{{ $id }}[file]" class="action-button passive">{{ __('button.select_image') }}</label>
    </div>

    <div class="simple-divider"></div>

    <div class="media-content" id="{{ $id }}[content]">
        <button type="button" class="cycle-button left">
            {!! file_get_contents("styles/icons/arrow_circle_icon.svg") !!}
        </button>
        <button type="button" class="cycle-button right">
            {!! file_get_contents("styles/icons/arrow_circle_icon.svg") !!}
        </button>

        <x-manager.media-preview type="image">
            <x-input.textarea tooltip="tooltip.media.image_alt" label="media.image_alt" maxlength="150" value=""
                class="image-alt-input" small optional />
            <x-input.checkbox tooltip="tooltip.media.webp" label="media.webp" class="image-webp-input" />
            <input type="hidden" class="image-path-input" />
            <input type="hidden" class="image-name-input" />
        </x-manager.media-preview>

        <x-manager.media-preview type="video">
            <x-input.text tooltip="tooltip.media.video_title" label="media.video_title" maxlength="150" value=""
                class="video-title-input" optional />
            <x-input.textarea tooltip="tooltip.media.video_description" label="media.video_description" maxlength="150"
                value="" class="video-description-input" small optional />
            <x-input.checkbox tooltip="tooltip.media.video_audio" label="media.video_audio" class="video-audio-input" />
            <input type="hidden" class="video-path-input" />
            <input type="hidden" class="video-name-input" />
            <input type="hidden" class="video-poster-input" />
        </x-manager.media-preview>

        <x-manager.media-preview type="model">
            <x-input.textarea tooltip="tooltip.media.model_alt" label="media.model_alt" maxlength="150" value=""
                class="model-alt-input" small optional />
            <input type="hidden" class="model-path-input" />
            <input type="hidden" class="model-name-input" />
            <input type="hidden" class="model-poster-input" />
        </x-manager.media-preview>

    </div>

    <script type="module">
        const mediaManager = new MediaManager("{{ $id }}")
            .prefill({
                type: @js($medium?->type ?? 'none'),
                files: @js($medium?->content ?? [])
            });
    </script>
</main>