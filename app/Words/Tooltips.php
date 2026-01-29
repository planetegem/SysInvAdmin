<?php

namespace App\Words;

class Tooltips
{
    // Category tooltips
    const category_general =
        "Categories (or tags) are used to link related items together. 
        A website might use this to build a filtering system, or to make seperate pages devoted to related items.";
    const category_name = 
        "The name of a category, used while filtering or as a tag on an item. 
        Don't make it too long.";
    const category_title = "
        The title of a category might appear on a page dedicated to all items of a specific category.
        Optional: if left blank, the API won't return it as a field.";
    const category_description = 
        "The description of a category might appear on a page dedicated to all items of a specific category. 
        Optional: if left blank, the API won't return it as a field.";
    const category_meta_title = 
        "The meta title of a page is displayed on the browser tab and can impact how your page appears on Google search results.
        Optional: if left blank, a website could typically take the category title as meta title.";
    const category_meta_description = 
        "The meta description determines how your page appears on Google's (or other search engine's) search results and promotes indexation.
        Optional: if left blank, a website could typically take the category description as meta description.";
    const hidden_category = 
        "Mark this category as hidden: can be used to make category pages that don't appear in your standard frontend.";



    // Item tooltips
    const item_title = "The title of your item. Typically appears in a header on your website.";
    const item_description = "The description of your item, some exposition on what it is and why it is. Can be multiple paragraphs. HTML allowed.";

    const file_type_dropdown = 
        "Select the type of file you would like to add as medium/media. Choose between:
        <ul>
        <li>Image - a standard image of any size</li>
        <li>Thumbnail - an image that's resized to thumbnail format [not implemented]</li>
        <li>Gallery - a series of images [not implemented]</li>
        <li>Video - a video file [not implemented]</li>
        </ul>";
    const image_alt = 
        "A simple, short description of your image, used for accessibility purposes. 
        Optional: if you don't provide an alternative text, the frontend can decide what to do with it.";
    const convert_to_webp = "Convert jpg, jpeg or png to webp format (quality 80). For files in gif or webp, this checkbox doesn't do anything.";
    const item_categories = 
        "Categories can be used to tag or filter items. 
        There are API endpoints available to get all items associated with a certain category.";
    const hidden_item_categories = 
        "Exactly like a category, but then hidden, so it can taken out of ordinary flow in the front-end"; 
    const item_links = 
        "Link to internal or external pages related to your item (for example a details page). 
        Anchor text corresponds to the text shown instead of the link, URL corresponds to the destination of your link and can be either relative or absolute.";
    const item_relationships = 
        "Express a relationship to another item. <br>
        Relationships can't be chained: if item A is an update to item B, item B cannot become an update to item C.";
    
    

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