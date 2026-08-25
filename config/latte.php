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
    | Template Loader
    |---------------------------------------------------------------------------
    |
    | The default "nette" loader keeps Latte's native filesystem behavior.
    | The optional "laravel" loader resolves dotted and namespaced view names
    | through Laravel's view finder, including registered view paths and hints.
    |
    | Supported values: "nette", "laravel", or a class implementing Latte\Loader.
    |
    */

    'loader' => 'nette',

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
    | Since Latte 3.1, strict types are enabled by default.
    |
    | https://latte.nette.org/en/develop#toc-strict-mode
    |
    */

    'strict_types' => true,

    /*
    |--------------------------------------------------------------------------
    | Scoped Loop Variables
    |--------------------------------------------------------------------------
    |
    | By default, variables defined in a {foreach} loop (like $key and $value)
    | remain accessible after the loop ends – just like in PHP itself.
    | This can lead to unintended variable overwrites when a loop variable
    | has the same name as an existing template variable.
    |
    | The ScopedLoopVariables feature limits the scope of loop variables
    | to the loop body. After the loop ends, the original variable value
    | is restored (if it existed before), or the variable is unset.
    |
    | https://latte.nette.org/en/develop#toc-scoped-loop-variables
    |
    */

    'scoped_loop_variables' => false,

    /*
    |--------------------------------------------------------------------------
    | Automatic Dedentation
    |--------------------------------------------------------------------------
    |
    | When using paired tags like {if}, {foreach}, or {block}, you often indent
    | the nested content for readability. However, this indentation is included
    | in the generated output by default. The Dedent feature automatically
    | removes it, so the output stays clean regardless of how deeply you nest
    | your Latte tags.
    |
    | https://latte.nette.org/en/develop#toc-dedent
    |
    */

    'dedent' => false,

    /*
    |--------------------------------------------------------------------------
    | Migration Warnings
    |--------------------------------------------------------------------------
    |
    | Latte 3.1 changes the behavior of some HTML attributes:
    | https://latte.nette.org/en/html-attributes.
    | For example, null values now drop the attribute instead of printing
    | an empty string. To easily find places where this change affects your
    | templates, you can enable migration warnings.
    |
    | When enabled, Latte checks rendered attributes and triggers
    | a user warning (E_USER_WARNING) if the output differs from what
    | Latte 3.0 would have produced.
    |
    | If "null", true is used for app.debug, otherwise false
    |
    | https://latte.nette.org/en/develop#toc-migration-warnings
    |
    */

    'migration_warnings' => env('LATTE_MIGRATION_WARNINGS', null),

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

];
