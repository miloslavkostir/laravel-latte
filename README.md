Laravel Latte
=============

Extends the Laravel framework with the templating engine [Latte](https://latte.nette.org)

## Installation

```
$ composer require miko/laravel-latte
```

Then the templating engine is used according to the file extension:
- `*.blade.php` - [Blade (Laravel default)](https://laravel.com/docs/blade)
- `*.latte` - [Latte](https://latte.nette.org)

## Configuration

Publish config file into `config/latte.php`:
```html
$ php artisan vendor:publish --provider="Miko\LaravelLatte\ServiceProvider"
```
Follow the instructions in the config file.

## Extension

See https://latte.nette.org/tags

And additional:

### Tags `link` and `n:href`

Similar to [tags in Nette](https://doc.nette.org/en/application/creating-links#toc-in-the-presenter-template)
except that the separator berween controller and method is not `:` but `@` and the default method is not `default` but `index` according to the Laravel conventions.
Basically this is a simplified call to Laravel's [action()](https://laravel.com/docs/urls#urls-for-controller-actions) helper when
there is no need to write the entire FQCN and the word `Controller`.
In addition, it is possible to use the keyword `this` for the current action - then there is no need to write unchanged parameters.

```html
{link User@detail}             {* shortcut for action([App\Http\Controllers\UserController::class, 'detail']) *}
{link User@}                   {* shortcut for action([App\Http\Controllers\UserController::class, 'index']) *}
{link detail}                  {* shortcut for action([$current_controller, 'detail']) *}
{link User@detail 123, 456}    {* shortcut for action([App\Http\Controllers\UserController::class, 'detail'], [123, 456]) *}
{link User@detail foo => bar}  {* shortcut for action([App\Http\Controllers\UserController::class, 'detail'], ['foo' => 'bar']) *}
{link "Admin\Article@detail"}  {* shortcut for action([App\Http\Controllers\Admin\ArticleController::class, 'detail']) *}
{link this}                    {* generates a link for the current action and current arguments (current URL) *}

{* n tag *}
<a n:href="User@detail 123, foo">

{* Named arguments can be used... *}
<a n:href="Product@show $product->id, lang: cs">product detail</a>
{link Product@show $product->id, lang: cs}

{* ...and (expand) as well because of Latte v2 BC *}
{var $args = [$product->id, lang => cs]}
<a n:href="Product@show (expand) $args">product detail</a>
{link Product@show (expand) $args}
```
> `(expand)` - the equivalent of `...$arr` operator https://latte.nette.org/en/syntax#toc-a-window-into-history

#### Usage of `this`
route:
```php
Route::get('/users/permissions/{user}/{permission}', [\App\Http\Controllers\UserController::class, 'permissions']);
```
template:
```html
<a n:href="this sort => date">Sort by date</a>
```
At address `mysite.com/users/permissions/1/2` generates link `mysite.com/users/permissions/1/2?sort=date`

### Tags `asset` and `n:src`

Adds the `m` parameter to the URL with the timestamp of the last file change. Every time the file is changed, it will be reloaded
and there is no need to clear the browser cache:
```html
<script n:src="/js/some-script.js"></script>
<link rel="stylesheet" href="{asset '/css/some-style.css'}">
<img n:src="/imgs/some-image.png">
```

### Tag `csrf`

Generates hidden input `_token` in form with CSRF token https://laravel.com/docs/csrf#preventing-csrf-requests
```html
<form method="POST" action="/profile">
    {csrf}

    <!-- Equivalent to... -->
    <input type="hidden" name="_token" value="{csrf_token()}" />
</form>
```

### Tag `method`

Generates a hidden input `_method` in the form for [Form Method Spoofing](https://laravel.com/docs/routing#form-method-spoofing)
```html
<form action="/example" method="POST">
    {method PUT}
</form>
```

### Tags `livewire`, `livewireStyles`, `livewireScripts` a `livewireScriptConfig`
**🧪 Experimental**   
Tags for [Livewire](https://livewire.laravel.com/). They are only available if livewire is implemented.
```html
<!doctype html>
<html lang="en">
    <head>
        <title></title>
        {livewireStyles}
    </head>
<body>
    {livewire component-name foo => bar}
    {livewireScripts}
    {livewireScriptConfig}
</body>
</html>
```

### Translation
There is implemented `Latte\Essential\TranslatorExtension`,
so it is possible to use tags `{_}` and `{translate}` with Laravel functions `trans()` and `trans_choice()`.

See doc:
- https://latte.nette.org/en/develop#toc-translatorextension
- https://latte.nette.org/en/tags#toc-translation
- https://laravel.com/docs/localization#retrieving-translation-strings

```html
{_'messages.welcome'}
{_'messages.welcome', [name: dayle]}
{_'messages.apples', 10}
{_'time.minutes_ago', 5, [value: 5]}
{_'time.minutes_ago', 5, [value: 5], de}
{_'messages.welcome', locale: de}
```
Above is equivalent to Laravel functions:
```php
echo __('messages.welcome');
echo __('messages.welcome', ['name' => 'dayle']);
echo trans_choice('messages.apples', 10);
echo trans_choice('time.minutes_ago', 5, ['value' => 5]);
echo trans_choice('time.minutes_ago', 5, ['value' => 5], 'de');
echo __('messages.welcome', locale: 'de');
```


### Components
An object implementing `Mike\LaravelLatte\IComponent` can be rendered in template:
```php
namespace App\View\Components;

use Illuminate\View\View;
use Miko\LaravelLatte\IComponent;

class Alert extends Component
{
    private array $params = [];

    /**
     * Get some services from service container
     */
    public function __construct(private SomeService $someService)
    {
    }

    /**
     * Get variables from template
     */
    public function init(...$params): void
    {
        $this->params = $params;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string
    {
        return view('components.alert', $this->params);
    }
}
```
View `components.alert`:
```html
{x alert type => error, message => $message}
```
Root namespace of components is set in config (`latte.components_namespace`) 
and is `App\View\Components` by default.
