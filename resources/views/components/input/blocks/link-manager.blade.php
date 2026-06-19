<x-info-tooltip text="{!! __('tooltip.item.links') !!}" />
<main class="link-manager">
    <ol>
        <li>
            <div class="double-input">
                <x-input.text name="item_links[0][anchor]"
                    value="{{ isset($links[0]->anchor) ? $links[0]->anchor : '' }}" optional
                    label="label.link.anchor" />
                <x-input.text name="item_links[0][url]" value="{{ isset($links[0]->url) ? $links[0]->url : '' }}"
                    optional label="label.link.url" />
            </div>
        </li>
        <li>
            <div class="double-input">
                <x-input.text name="item_links[1][anchor]"
                    value="{{ isset($links[1]->anchor) ? $links[1]->anchor : '' }}" optional
                    label="label.link.anchor" />
                <x-input.text name="item_links[1][url]" value="{{ isset($links[1]->url) ? $links[1]->url : '' }}"
                    optional label="label.link.url" />
            </div>
        </li>
    </ol>
</main>