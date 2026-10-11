#  Organic evolution of the datasphere: a case study
![screenshot](mermaids/DB_0_title.png)

## (1.) The SysInvAdmin database exists as a simple multitabular organism.
```mermaid
%%{
  init: {
    'er': {
      'diagramPadding': 40
    }
  }
}%%
erDiagram
    direction TB
    %% TABLES
    ITEMS {
        bigint id PK
        string title
        string description
        string? file_type "Type of media"
        bigint language_id FK
    }
    CATEGORIES {
        bigint id PK
        string name UK
        string? title
        string? description
        string? meta_title
        string? meta_description
        bool? hidden
    }
    LANGUAGES {
        bigint id PK
        string name "English name"
        string short_name
        string? native_name "Untranslated name"
    }
    MEDIA {
        bigint id PK
        string file_type "Arbitrary categorisation (image, thumbnail, ...)"
        string file_path
        string file_name "Original name of the file"
        string? alt
        bigint item_id FK
    }
    LINKS {
        bigint id PK
        string anchor
        string url
        bigint order "Importance of link"
        bigint item_id FK
    }
    CATEGORY_ITEM_PIVOT:::pivot {
        bigint id PK
        bigint item_id FK
        bigint category_id FK
    }

    %% RELATIONSHIPS
    %% Self-referential / Direct child links
    LANGUAGES ||--|{ ITEMS : "has"
    ITEMS ||--|{ LINKS : "has"
    ITEMS ||--|| MEDIA : "has"

    %% Pivots
    ITEMS ||--|{ CATEGORY_ITEM_PIVOT : "belongs to"
    CATEGORIES ||--|{ CATEGORY_ITEM_PIVOT : "belongs to"

    %% Styling
    classDef default fill:#ffeeee,stroke:#ff9f9f,stroke-width:4px
    classDef pivot fill:#eee,stroke:#bbb,stroke-width:3px
```
## (2.) A mutation occurs: items begin forming relationships with one another.
```mermaid
%%{
  init: {
    'er': {
      'diagramPadding': 40
    }
  }
}%%
erDiagram
    %% TABLES
    ITEMS {
        bigint id PK
        string title
        string description
        string type "Type of relationship"
        string? file_type "Type of media"
        bigint language_id FK
    }
    CATEGORIES {
        bigint id PK
        string name UK
        string? title
        string? description
        string? meta_title
        string? meta_description
        bool? hidden
    }
    LANGUAGES {
        bigint id PK
        string name "English name"
        string short_name
        string? native_name "Untranslated name"
    }
    MEDIA {
        bigint id PK
        string file_type "Arbitrary categorisation (image, thumbnail, ...)"
        string file_path
        string file_name "Original name of the file"
        string? alt
        bigint item_id FK
    }
    LINKS {
        bigint id PK
        string anchor
        string url
        bigint order "Importance of link"
        bigint item_id FK
    }
    CATEGORY_ITEM_PIVOT:::pivot {
        bigint id PK
        bigint item_id FK
        bigint category_id FK
    }
    ITEM_RELATIONSHIPS:::pivot {
        bigint id PK
        string relationship
        bigint subject_item_id FK
        bigint object_item_id FK
    }

    %% GROUPING
    subgraph Item_Core [" "]
        direction TB
        ITEMS
        ITEM_RELATIONSHIPS
        ITEMS ||--|{ ITEM_RELATIONSHIPS : "is subject"
        ITEMS ||--|{ ITEM_RELATIONSHIPS : "is object"
    end

    %% RELATIONSHIPS
    %% Self-referential / Direct child links
    LANGUAGES ||--|{ Item_Core : "has many"
    Item_Core ||--|{ LINKS : "has many"
    Item_Core ||--|| MEDIA : "has one"

    %% Pivots
    Item_Core ||--|{ CATEGORY_ITEM_PIVOT : "has many"
    CATEGORIES ||--|{ CATEGORY_ITEM_PIVOT : "has many"

    %% Styling
    classDef default fill:#ffeeee,stroke:#ff9f9f,stroke-width:4px
    classDef pivot fill:#eee,stroke:#bbb,stroke-width:3px
```
## (3.) Specialization increases: item descriptions become independent content blocks governed by a wysiwyg-editor.
```mermaid
%%{
  init: {
    'er': {
      'diagramPadding': 40
    }
  }
}%%
erDiagram
    %% TABLES
    ITEMS {
        bigint id PK
        string title
        string slug UK "Slug of title for SEO"
        string type "Type of relationship"
        string? file_type "Type of media"
        bigint language_id FK
    }
    CATEGORIES {
        bigint id PK
        string name UK
        string slug UK "Slug of name for SEO"
        string? title
        string? description
        string? meta_title
        string? meta_description
        bool? hidden
    }
    LANGUAGES {
        bigint id PK
        string name "English name"
        string short_name
        string? native_name "Untranslated name"
    }
    MEDIA {
        bigint id PK
        string file_type "Arbitrary categorisation (image, thumbnail, ...)"
        string file_path
        string file_name "Original name of the file"
        string? alt
        bigint item_id FK
    }
    LINKS {
        bigint id PK
        string anchor
        string url
        bigint order "Importance of link"
        bigint item_id FK
    }
    CATEGORY_ITEM_PIVOT ::: pivot {
        bigint id PK
        bigint item_id FK
        bigint category_id FK
    }
    ITEM_RELATIONSHIPS ::: pivot {
        bigint id PK
        string relationship
        bigint subject_item_id FK
        bigint object_item_id FK
    }
    CONTENT_BLOCKS {
        bigint id PK
        string type "Source code or quill"
        string content
        bigint contentable_id FK
        string contentable_type "Model that foreign key relates to"
    }
    
    %% GROUPING
    subgraph Item_Core [" "]
        direction TB
        ITEMS
        ITEM_RELATIONSHIPS
        ITEMS ||--|{ ITEM_RELATIONSHIPS : "is subject"
        ITEMS ||--|{ ITEM_RELATIONSHIPS : "is object"
    end

    %% RELATIONSHIPS
    %% Self-referential / Direct child links
    LANGUAGES ||--|{ Item_Core : "has many"
    Item_Core ||--o{ LINKS : "has zero or many"
    Item_Core ||--|| MEDIA : "has one"
    CONTENT_BLOCKS ||..|| Item_Core : "morphs to one"

    %% Pivots
    Item_Core ||--o{ CATEGORY_ITEM_PIVOT : "belongs to"
    CATEGORIES ||--o{ CATEGORY_ITEM_PIVOT : "belongs to"

    %% Styling
    classDef default fill:#ffeeee,stroke:#ff9f9f,stroke-width:4px
    classDef pivot fill:#eee,stroke:#bbb,stroke-width:3px
```
