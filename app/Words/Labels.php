<?php

namespace App\Words;

class Labels
{
    // Category labels
    const category_name = "Category name";
    const category_title = "Category title";
    const category_description = "Category description";
    const category_meta_title = "Meta title";
    const category_meta_description = "Meta description";
    const hidden_category = "Hidden category";

    // Item labels
    const item_title = "Item title";
    const item_description = "Item description";
    const image_alt = "Alternate text";
    const convert_to_webp = "Convert image to webp";
    const link_anchor = "Anchor text";
    const link_url = "URL";
    const item_categories = "Add category";
    const hidden_item_categories = "Add hidden category";
   

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