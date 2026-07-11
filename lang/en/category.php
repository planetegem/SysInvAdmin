<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Labels Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to generate labels for everything related to categories.
    | Used in the category manager and category forms
    |
    */


    "header" => [
        "create" => "Create category",
        "update" => "Edit category",
        "delete" => "Delete category"
    ],
    "message" => [
        "create" =>
            "Categories (or tags) are used to link related items together. 
            A website might use this to build a filtering system, or to make seperate pages devoted to related items.",
        "delete" => "Are you sure you want to delete category #:id (:name)?",
        "warning_attached_items" => "This category is currently attached to :count items.",
    ],
    "properties" => [
        "timestamps" => "Created on :created | Last updated on :updated",
        "name" => "Category #:id (:name)",
        "item_count" => "Attached to :count items",
    ],
    "input" => [
        "name" => "Category name",
        "title" => "Category title",
        "description" => "Category description",
        "hidden" => "Hidden category",
    ],
    "tooltip" => [
        "general" =>
            "Categories (or tags) are used to link related items together. 
            A website might use this to build a filtering system, or to make seperate pages devoted to related items.",
        "name" =>
            "The name of a category, used while filtering or as a tag on an item. 
            Don't make it too long.",
        "title" =>
            "The title of a category might appear on a page dedicated to all items of a specific category.
            Optional: if left blank, the API won't return it as a field.",
        "description" =>
            "The description of a category might appear on a page dedicated to all items of a specific category. 
            Optional: if left blank, the API won't return it as a field.",
        "hidden" =>
            "Mark this category as hidden: can be used to make category pages that don't appear in your standard frontend.",
    ],




    "add_category" => "Add category",
    "add_hidden_category" => "Add hidden category"

];