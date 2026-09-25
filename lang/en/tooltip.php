<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tooltip Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to generate tooltips for input elements.
    |
    */

    "item" => [
        "title" => "The title of your item. Typically appears in a header on your website.",
        "description" => "The description of your item, some exposition on what it is and why it is. Can be multiple paragraphs. HTML allowed.",
        "categories" =>
            "Categories can be used to tag or filter items. 
            There are API endpoints available to get all items associated with a certain category.",
        "hidden_categories" => "Exactly like a category, but then hidden, so it can taken out of ordinary flow in the front-end",
        "links" =>
            "Link to internal or external pages related to your item (for example a details page). 
            Anchor text corresponds to the text shown instead of the link, URL corresponds to the destination of your link and can be either relative or absolute.",
    ],
    "media" => [
        "type" => "Select the type of file you would like to add as medium/media. Choose between:
            <ul>
            <li>Image - a standard image of any size</li>
            <li>Image List - a collection of images</li>
            <li>Image Carousel - a collection of images that might be rendered as a carousel</li>
            <li>Video - a simple video file</li>
            <li>GIFv - a video file pretending to be a gif (looping, no video controls)</li>
            <li>3D Model - a 3D asset to be rendered (GLB)</li>
            </ul>",
        "image_alt" => "A simple, short description of your image, used for accessibility purposes. Optional.",
        "webp" => "Convert jpg, jpeg or png to webp format (quality 80).",
        "video_title" => "The title of your video, typically displayed as structured data for SEO purposes. Optional.",
        "video_description" => "The description of your video, typically displayed as structured data for SEO purposes. Optional.",
        "video_audio" => "Include the audio track of this video. If unchecked, audio is stripped in the backend [TO DO]",
        "model_alt" => "A simple, short description of your 3D asset, used for accessibility purposes. Optional.",
        "mediable_toggle" => 
            "Detaching this medium means that it will exist independently from its parent. 
            Do this if you intend to delete the parent, but you don't want to lose the medium."
    ],


];