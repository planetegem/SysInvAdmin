<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Relationships Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to generate labels for everything related to relationships.
    | Used in the relationship manager and in relationship forms
    |
    */


    "header" => [
        "create" => "Create relationship",
        "labels" => "Relationship labels",
        "descriptors" => "Relationship descriptors",
        "update" => "Edit relationship",
        "delete" => "Delete relationship"
    ],
    "message" => [
        "create" =>
            "Relationships (or tags) establish links between individual items. 
            Examples include items that are updates to other items (hierarchical relationship) or translations to other items (lateral relationship).",
        "create_ok" => "Relationship #:id (:name) has been succesfully created.",
        "update_ok" => "Relationship #:id (:name) has been succesfully updated.",
        "delete" => "Are you sure you want to delete relationship #:id (:name)?",
        "warning_attached_items" => 
            "{0} This relationship is not used to link any items. 
            |{1} This relationship is used :count time to link items together. 
            |[2,*] This relationship is used :count times to link items together.",
        "delete_ok" => "Relationship #:id (:name) has been succesfully deleted."
    ],
    "properties" => [
        "timestamps" => "Created on :created | Last updated on :updated",
        "name" => "Relationship #:id (:name)",
        "usage_count" => 
            "{0} Not used to link any items 
            |{1} Used :count time to link items together
            |[2,*] Used :count times to link items together",
    ],
    "input" => [
        "name" => "Relationship name",
        "type" => "Relationship type",
        "subject_label" => "Subject label",
        "object_label" => "Object label",
        "subject_descriptor" => "Subject descriptor",
        "object_descriptor" => "Object descriptor"
    ],
    "types" => [
        "hierarchical" => "Hierarchical",
        "lateral" => "Lateral"
    ],
    "manager" => [
        "header" => "Item relationships",
        "set_as" => "This&nbsp;item",
        "nothing_selected" => "Nothing",
        "add_new" => "Add new relationship",
        "placeholder" => "This item doesn't have any relationships yet."
    ],
    "tooltip" => [
        "name" => "A unique name that expresses the meaning of the relationship.",
        "type" =>
            "The type of relationship being created. 
            Can be hierarchical (a parent/child relationship) or lateral (a sibling relationship).",
        "subject_label" => "The name of the relationship from the viewpoint of the relationship subject.",
        "object_label" =>
            "The name of the relationship from the viewpoint of the relationship object.
            Identical to the subject label in case of a lateral relationship.",
        "subject_descriptor" => "Descriptive label for the relationship from the viewpoint of the relationship subject.",
        "object_descriptor" =>
            "Descriptive label for the relationship from the viewpoint of the relationship subject.
            Identical to the subject descriptor in case of a lateral relationship.",
        "item_manager" => 
            "Express a relationship to another item. 
            Items can have multiple related items, but only one relationship to each item.",
    ],


];