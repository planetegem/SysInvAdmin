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
        "media_type" =>
            "Select the type of file you would like to add as medium/media. Choose between:
            <ul>
            <li>Image - a standard image of any size</li>
            <li>Thumbnail - an image that's resized to thumbnail format [not implemented]</li>
            <li>Gallery - a series of images [not implemented]</li>
            <li>Video - a video file [not implemented]</li>
            </ul>",
    ],
    "media" => [
        "image_alt" =>
            "A simple, short description of your image, used for accessibility purposes. 
            Optional: if you don't provide an alternative text, the frontend can decide what to do with it.",
        "webp" => "Convert jpg, jpeg or png to webp format (quality 80). For files in gif or webp, this checkbox doesn't do anything.",
    ],


];