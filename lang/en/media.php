<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to generate labels for
    | the media manager element and everything related to the Media type.
    |
    */

    "type" => "Media type",
    "name" => "Medium #:id (:type)",
    "types" => [
        "none" => "None",
        "image" => "Image",
        "list" => "Image List",
        "carousel" => "Image Carousel",
        "video" => "Video",
        "gifv" => "GIFv",
        "model" => "3D Model"
    ],
    "file_details" => [
        "header" => "File details",
        "name" => "Original name",
        "location" => "Location",
        "size" => "File size",
        "type" => "File type"
    ],
    "image_alt" => "Alternate text",
    "model_alt" => "Alternate text",
    "webp" => "Convert image to webp",
    "video_title" => "Video title",
    "video_description" => "Video description",
    "video_audio" => "Include audio track",
    "module" => [
        "header" => [
            "create" => "Create medium",
            "update" => "Edit medium",
            "delete" => "Delete medium"
        ],
        "message" => [
            "create" =>
                "Manually add new media to your website inventory. 
                These media will not be attached to items, but they will appear as results on the media API endpoints.",
            "created" => ":name has been succesfully created",
            "updated" => ":name has been succesfully updated",
            "delete" => "Are you sure you want to delete :name",
            "deleted" => ":name has been succesfully deleted",
        ],
        "file_selector" => "Select file(s)"
    ],
    "mediable_description" => "This medium belongs to :type #:id.",
    "mediable_toggle" => "Detach?"
];