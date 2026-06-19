<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Labels Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to generate labels of everything related
    | specifically to items.
    |
    */


    "header" => [
        "create" => "Create item",
        "update" => "Edit item",
        "delete" => "Delete item"
    ],
    "message" => [
        "create" =>
            "Add new items to your website inventory.",
        "delete" => "Are you sure you want to delete item #:id (:title)?"
    ],
    "input" => [
        "title" => "Item title",
        "description" => "Item description",
    ],
    "properties" => [
        "timestamps" => "Created on :created | Last updated on :updated",
        "name" => "Item #:id (:title)",
    ],
    "relationships" => [
        "count" => "Has :count dependencies",
        "relationship_heading" => "The following items have a relationship with this item:",
        "warning_relationships" => "Doing so would remove the following relationships:",
        "relationship_format" => ":title (item #:id | applied as :type)",
    ]
];