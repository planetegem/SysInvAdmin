
<main class="media-manager">
    <div class="media-selector">
        <x-form.input.dropdown id="file_type_dropdown" label="File type" tooltip>
            <option value="none" @if($item->file_type == null || $item->file_type == 'none') selected @endif >
                <span>None</span>
            </option>
            <option value="image" @if($item->file_type == "image") selected @endif >
                <span>Image</span>
            </option>
            <option value="gallery" @if($item->file_type == "gallery") selected @endif >
                <span>Gallery</span>
            </option>
            <option value="video" @if($item->file_type == "video") selected @endif >
                <span>Video</span>
            </option>
        </x-form.input.dropdown>
        <input type="file" name="media_file" id="media-file-input" @if($item->file_type == null || $item->file_type == 'none') disabled @endif/>
        <label for="media-file-input" class="action-button passive">Select&nbsp;file</label>
    </div>

    <div class="media-options" id="media-options-image">
        <x-form.input.text name="image_alt" value="{{ $item->media->first()->alt ?? '' }}" optional />
        <x-form.input.checkbox name="convert_to_webp" />
    </div>

    <div class="media-preview">
        <figure id="media-preview" class="{{ $item->file_type ? 'something-selected' : 'nothing-selected' }}">
            @switch($item->file_type)
                @case("image")
                    <img src="/storage/{{ $item->media->first()->file_path ?? '' }}" />
                    @break
            @endswitch
        </figure>
        @if($item->file_type && $item->file_type != 'none' && $item->media->first()->file_name)
            <span id="file-selected-label" class="something-selected">Previewing {{ $item->media->first()->file_name ?? '' }}</span>
        @else
            <span id="file-selected-label" class="nothing-selected">File preview:<br>no file selected</span>
        @endif
    </div>
</main>
