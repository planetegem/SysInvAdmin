<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to generate custom validation messages.
    |
    */

    "item" => [
        "link" => [
            "missing_anchor" => "An anchor text is required when supplying a link URL.",
            "missing_url" => "An URL is required when supplying a link anchor.",
        ],
        "description_missing" => "Please provide an item desciption",
        "relationship" => [
            "missing_type" => "Tried to establish an item relationship without defining a type.",
            "missing_target" => "Tried to establish an item relationship without defining a target."
        ],
    ],
    "media" => [
        "path_missing" => "Selected image, but did not provide an image file.",
        "not_supported" => "File type not supported yet."
    ]

];
