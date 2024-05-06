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

### Filter `nl2br` <small>(_bool_ $xhtml = `null`)</small>

```html
{$text|nl2br}
{$text|nl2br: true}  <!-- xhtml: true -->
```
> Default rendering as xhtml or html can be configured in the config file.

### Tags `{link}` and `n:href`

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

### Tags `{asset}` and `n:src`

Adds the `m` parameter to the URL with the timestamp of the last file change. Every time the file is changed, it will be reloaded
and there is no need to clear the browser cache:
```html
<script n:src="/js/some-script.js"></script>
<link rel="stylesheet" href="{asset '/css/some-style.css'}">
<img n:src="/imgs/some-image.png">
```

### Tags `{csrf}` and `{method}`

Generate hidden inputs `_token` and `_method` in form.
See [Preventing CSRF Requests](https://laravel.com/docs/csrf#preventing-csrf-requests)
and [Form Method Spoofing](https://laravel.com/docs/routing#form-method-spoofing).
Rendering as xhtml or html can be configured in the config file.
```html
<form action="/example" method="POST">
    
    {csrf}
    {method PUT}
    
    <!-- Equivalent to... -->
    
    <input type="hidden" name="_token" value="{csrf_token()}" autocomplete="off">
    <input type="hidden" name="_method" value="PUT">
    
</form>
```

### Tags `{livewire}`, `{livewireStyles}`, `{livewireScripts}` a `{livewireScriptConfig}`

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
Since Livewire renders its templates itself, the template can be a latte or a blade file.

⚠️ **WARNING:** if the component view is a latte file and the default layout is set in config (`latte.layout`), the component view **must have** `{layout none}` at the beginning.
Otherwise, Latte engine will try to render the layout again for this component.

`resources/views/livewire/component-name.latte`:
```html
{layout none}
<div>
    ...
</div>
```

### Translation `{_}`

Laravel provides functions `__()` and `trans()` respectively, see [retrieving translation strings](https://laravel.com/docs/localization#retrieving-translation-strings),
and `trans_choice()`, see [Pluralization](https://laravel.com/docs/localization#pluralization).
The tag `{_}` can handle both, it depends on the second parameter, whether it is an integer or an array.

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

If you want to implement `Latte\Essential\TranslatorExtension` as described in [Latte doc](https://latte.nette.org/en/develop#toc-translatorextension),
you can do so in `ServiceProvider` e.g. like this:
```php
use Latte\Essential\TranslatorExtension;
use Miko\LaravelLatte\Runtime\Translator;

public function boot(): void
{
    $latte = $this->app->get(\Latte\Engine::class);
    $latte->addExtension(new TranslatorExtension([Translator::class, 'translate']));
}
```
Why is it not implemented by default?
- `{translate}{/translate}` and `n:translate` tag are tempting to use 
[translation strings as keys](https://laravel.com/docs/localization#using-translation-strings-as-keys)
for an entire paragraph, which seems like hell and leads to error rates.
- `n:translate` doesn't work (v3.0.13) 
- setting the `lang` parameter as a cache key for precompiling static texts does not work if the language is set at runtime with `App::setLocale()`

### Components `{x}`
An object implementing `Miko\LaravelLatte\IComponent` can be rendered in template:
```php
namespace App\View\Components;

use Illuminate\View\View;
use Miko\LaravelLatte\IComponent;

class Alert implements IComponent
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
View:
```html
{x alert type => error, message => $message}
```
Root namespace of components is set in config (`latte.components_namespace`) 
and is `App\View\Components` by default.

⚠️ **WARNING:** if the default layout is set in config (`latte.layout`), the component view **must have** `{layout none}` at the beginning.
Otherwise, Latte engine will try to render the layout again for this component.
