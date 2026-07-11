

# About SysInvAdmin
SysInvAdmin (System Inventory Administrator) is a custom CMS that provides a headless backend for websites. It is specifically suited for sites that are structured as a sort of inventory. For example: a portfolio site might be considered an inventory of previous projects; a company site might be an inventory of products, a gaming site might be an inventory of different webgames or articles about games.

The backend is built in Laravel, with a UI to access it made with blade components and custom css. Items are added and categorized in this backend. These items are then fetched by querying an API, which lists them in JSON-format. This API response is what you use as a basis for your website: it is the content onto which you build your own presentation layer (the website frontend).

__Why create this when there are already so many similar products?__
In 1 word: style. SysInvAdmin is a stylish CMS, not consisting of bland grey-on-grey menu's, but smooth pink-on-white. Making it custom with my specific use-cases in mind, also means it is less bloated.

![screenshot](.github/images/sysinvadmin_login.webp)

## Examples of sites using SysInvAdmin
### Planetegem

![screenshot](.github/images/planetegem_logo.webp)

My own site, https://planetegem.be, runs on SysInvAdmin (in fact, this is why I started building it). The backend lives at https://inventory.planetegem.be. The site is uses the following calls:
- GET api/categories/index is used to create a menu where you can filter on category
- GET api/items/all (with optional query parameters to filter) is then used to make the homepage (a simple list of all my projects)
- GET api/categories/slug is used to make SEO friendly pages for each category
- GET api/items/slug is used to make a page dedicated to a single 'master' item with all of its 'updates' included. Planetegem only uses the master/update relationship between items.

The items shown on the website then link to custom pages / projects / webgames.

## Setup
### Artisan Commands To Get You Started
1) Run 'php artisan migrate' to create database. I chose an SQlite db, because I was not expecting a lot of concurrent write operations, but you're free to select any type of database you want. Laravel should handle the difference in setup.
2) Run 'php artisan db:seed' to create some defaults. At the moment, these are just languages, which can't be created any other way.
3) Run 'php artisan make:admin {mail} {password}' to create the first admin user, which you can use as login. Once inside, you can use the interface to invite additional users/admins.
4) [TO DO: explain how invitations work]

### Frontend starter kit
I'm working on including a simple starter kit to build a php frontend that easily integrates with SysInvAdmin. It will take care of routing, API calls, and php templates. For a WIP, take a look at the [github page for planetegem.be](https://github.com/planetegem/planetegem-homepage).

### Working with the API
When SysInvAdmin is installed, it automatically comes with OpenAPI / Swagger documentation. For an example, take a look at https://inventory.planetegem.be/api/documentation.

### Image conversion
[TO DO]

## Features
### Current features
- Login system, with support for a 'remember me' token.
- Add items to the database. They have a at least a title and a description.
- Item description consists of html. The backend integrates quill as wysiwyg editor. You can switch between quill and a free format source view to simply write your own html / inline css.
- Add multiple categories to items. These can be used as tags to clarify what they are about, or to implement a filter in the frontend.
- Categories can be seperately managed: fields are available to add a simple description, meta title and meta description.
- Hidden categories can be added: these work the same as regular categories, but allows you to make lists of items without the category showing up as a filter.
- Media can be added to an item. Currently, this is only a single image, but more media types will be added.
- When uploading an image, optionally convert it to webp on the fly.
- Add links to an item. A link consists of an anchor text and a url.
- Establish relationships between items. An item can be designated as an update to another item.
- UI to browse existing items and categories to update them. The UI shows all relevant information and a preview of any media that was added.
- Items and categories can be sorted by name or last updated.
- Possibility to delete items or categories.

### Roadmap
- Finish registration system: new users can be invited. On accepting the invitation, they can create their username / password.
- Provide UI for user access management: list all current users, or quickly add new ones (prompting an invitation to be sent).
- Provide a user action log.
- Differentiate between a true admin and a regular user.
- Add more available media types: a gallery consisting of multiple images.
- Add more available media types: a 3D model, possibly with three.js
- Add more available media types: video.
- Add more available media types: a thumbnail or resized image.
- Add UI to manage media.
- Add products: a wrapper for an item, adding stock numbers and pricing.
- Add pages: add multiple content blocks to one page and save it.
- Add templates for pages.
- Add settings page, where an admin can check/uncheck certain fields to clean up the UI (avoid clutter from unused fields).
- Add API key to shield the API. API key can be generated by an admin.
- (...)


## Change log
### 0.4: Relationships module
Added a relationships module, where you can define custom relationships. An improvement relationship manager in the items module now allows you to add as many relationships as you want to any item.
Also installed pulse and integrated a link to the dashboard: to be used later on when performance testing becomes a thing.

Refactoring:
- Cleaned Media validation logic: validation and actual media processing have now been properly seperated out.
- Logic to generate API responses containing items has been moved to the Item model. Will do this for all other models as well.
- Logic to generate timestamp strings now lives in a trait that can be applied to models
- Made names a bit less murky. Interacting with SysInvAdmin resources is now done via:
    1) Modules: classic ResourceController with create, edit & index views. These correspond to the buttons on the left side of the UI (Items, Categories, etc)
    2) Managers: a block within a form to create or edit seperate resources (like the CategoryManager in the Items module)


### 0.3: Localisation files & component refactoring
Started using Laravel localisation files for labels and tooltips: as I go through all components and views, I'll move everything over.
Also refactored some components:
- Now have an Input component as common ancestor for the various inputs (checkbox, textbox, etc)
- Now have a FormTemplate component as common ancestor for the various form types (wrapper)
- Reworked Category & Item form templates to have less repeating code

### 0.2: Media manager update
Updated the media-manager block to:
- work as js module that's imported on the spot. Can now have multiple media managers without issue.
- added support for multiple media files on one item (image list or carousel)
- reworked validation logic, save logic, and api response logic as a trait that can be used on any model
- turned media table polymorphic so categories can also get media items in the futures (for SEO or category landing pages)
- webp conversion reworked: now tries imagick driver, then gd driver, then returns error. To efficiently convert gifs, your server needs imagick.

### 0.1: The beginning
Basic version running planetegem.be. 
Has items with wysiwyg editor for description, image uploader, categories and API with documentation.
