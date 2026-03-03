<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Latte templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    | If "null", the value from view.compiled will be used.
    | "false" means no template caching, which is strongly discouraged and
    | only applicable for testing purposes
    |
    */

    'compiled' => null,

    /*
    |---------------------------------------------------------------------------
    | XHTML or HTML
    |---------------------------------------------------------------------------
    |
    | This value affects the rendering of (x)html snippets. Basically renders
    | or doesn't render trailing slashes.
    |
    */

    'xhtml' => false,

    /*
    |--------------------------------------------------------------------------
    | Automatic Layout Lookup
    |--------------------------------------------------------------------------
    |
    | Using the tag {layout}, the template determines its parent template.
    | It's also possible to have the layout searched automatically, which will
    | simplify writing templates since they won't need to include the {layout}
    | tag. If the template should not have a layout, it will indicate this
    | with the {layout none} tag.
    |
    | !! The {layout none} tag is required for all livewire views !!
    |
    | The path should be absolute.
    |
    | https://latte.nette.org/en/develop#toc-automatic-layout-lookup
    |
    */

    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Auto Refresh Compiled Files
    |--------------------------------------------------------------------------
    |
    | The cache is automatically regenerated every time you change the source
    | file. So you can conveniently edit your Latte templates during
    | development and see the changes immediately in the browser. You can
    | disable this feature in a production environment and save a little
    | performance.
    |
    | If "null", true is used for app.debug, otherwise false
    |
    | https://latte.nette.org/en/develop#toc-performance-and-caching
    |
    */

    'auto_refresh' => env('LATTE_AUTO_REFRESH', null),

    /*
    |--------------------------------------------------------------------------
    | Strict Parsing
    |--------------------------------------------------------------------------
    |
    | In strict parsing mode, Latte checks for missing closing HTML tags
    | and also disables the use of the "$this" variable.
    |
    | https://latte.nette.org/en/develop#toc-strict-mode
    |
    */

    'strict_parsing' => false,

    /*
    |--------------------------------------------------------------------------
    | Strict Types
    |--------------------------------------------------------------------------
    |
    | To generate templates with the "declare(strict_types=1)" header.
    | In Latte 3.1+, this is enabled by default.
    |
    | https://latte.nette.org/en/develop#toc-strict-mode
    |
    */

    'strict_types' => true,

    /*
    |---------------------------------------------------------------------------
    | Components Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root class namespace for component classes in
    | your application.
    |
    */

    'components_namespace' => 'App\\View\\Components',

    /*
    |--------------------------------------------------------------------------
    | Migration Warnings (Latte 3.1+)
    |--------------------------------------------------------------------------
    |
    | Latte 3.1 introduced Smart HTML Attributes. To help with migration,
    | you can enable migration warnings which will trigger E_USER_WARNING
    | when the output would differ from Latte 3.0.
    |
    | https://blog.nette.org/en/latte-3-1-is-here-smarter-fairer-and-strict
    |
    */

    'migration_warnings' => env('LATTE_MIGRATION_WARNINGS', false),

    /*
    |--------------------------------------------------------------------------
    | Smart HTML Attributes (Latte 3.1+)
    |--------------------------------------------------------------------------
    |
    | Enables smarter handling of HTML attributes (null removes attribute,
    | boolean attributes, automatic JSON serialization for data attributes).
    |
    */

    'smart_attributes' => true,

];
