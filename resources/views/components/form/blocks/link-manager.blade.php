<x-info-tooltip text="{!! App\Words\Tooltips::look_up('item_links') !!}" />
<main class="link-manager">
    <ol>
        <li>
            <div class="double-input">
                <x-form.input.text name="item_links[0][anchor]"
                    value="{{ isset($links[0]->anchor) ? $links[0]->anchor : '' }}" optional :tooltip="false"
                    label="link_anchor" />
                <x-form.input.text name="item_links[0][url]" value="{{ isset($links[0]->url) ? $links[0]->url : '' }}"
                    optional :tooltip="false" label="link_url" />
            </div>
        </li>
        <li>
            <div class="double-input">
                <x-form.input.text name="item_links[1][anchor]"
                    value="{{ isset($links[1]->anchor) ? $links[1]->anchor : '' }}" optional :tooltip="false"
                    label="link_anchor" />
                <x-form.input.text name="item_links[1][url]" value="{{ isset($links[1]->url) ? $links[1]->url : '' }}"
                    optional :tooltip="false" label="link_url" />
            </div>
        </li>
    </ol>
</main>