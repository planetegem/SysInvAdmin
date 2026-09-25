@props(['medium'])

<section class="large-bordered-input-element">
    <x-manager.media-manager :medium="$medium" id="medium" />
</section>
@if($medium->mediable != null)
    <section class="large-bordered-input-element">
        <main class="mediable_block">
            <p>{!! __('media.mediable_description', ['type' => $medium->mediableName, 'id' => $medium->mediable->id]) !!}</p>
            <x-input.checkbox name="detach_medium" tooltip="tooltip.media.mediable_toggle" label="media.mediable_toggle" />
        </main>
    </section>
@endif