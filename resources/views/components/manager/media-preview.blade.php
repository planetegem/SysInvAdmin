<template class="{{ $type }}-preview">
    <div class="media-preview-container">
        <div class="media-inputs">
            {{ $slot }}
        </div>
        <fieldset class="media-properties">
            <legend>{{ __('media.file_details.header') }}</legend>
            <p class="name">{{ __('media.file_details.name') }}: <span></span></p>
            <p class="location">{{ __('media.file_details.location') }}: <span></span></p>
            <p class="size">{{ __('media.file_details.size') }}: <span></span></p>
            <p class="type">{{ __('media.file_details.type') }}: <span></span></p>
        </fieldset>
        <div class="media-preview"></div>
    </div>
</template>