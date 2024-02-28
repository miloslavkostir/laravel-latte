Laravel Latte
=============

Extends the Laravel framework with the templating engine [Latte](https://latte.nette.org)

## Installation

```
$ composer require miko/laravel-latte
```

## Implementation

`config/app.php`:
```php

    'providers' => [

        /*
         * Package Service Providers...
         */
        Miko\LaravelLatte\ServiceProvider::class

    ],

]
```

Then the templating engine is used according to the file extension:
- `*.blade.php` - [Blade (Laravel default)](https://laravel.com/docs/blade)
- `*.latte` - [Latte](https://latte.nette.org)
