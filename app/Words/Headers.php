<?php

namespace App\Words;

class Headers
{
    // Headers for category forms
    const categories_create_header = "Create category";
    const categories_create_intro =
        "Categories (or tags) are used to link related items together. 
        A website might use this to build a filtering system, or to make seperate pages devoted to related items.";
    const categories_update_header = "Edit category";
    const categories_delete_header = "Delete category";

    // Headers for item forms
    const items_create_header = "Create item";
    const items_create_intro =
        "Add new items to your website inventory.";
    const items_update_header = "Edit item";
    const items_delete_header = "Delete item";


    public static function look_up($param_name)
    {
        try {
            return constant("self::$param_name");
        } catch (\Error $e) {
            $param_name = strtoupper($param_name);
            return "$param_name NOT FOUND";
        }
    }
}