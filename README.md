# LaravelResourceKit

[![Software License][ico-license]](LICENSE.md)

This provides an easy way for resource interfaces. Just create a controller and it works. Do not use in production.

## Why?
Wrote this for own use, now extracted it to be used in multiple projects of mine.
Posting it just to see if someone is interested, so send me a message dododedodonl@thor.edu.

## Overview
The package provides two controllers, `ResourceController` and `LinkedResourceController`. Extend those controllers like the examples. `LinkedResourceController` is not fully functional yet.
Do not use in production.

## Requirements
- PHP 7.0+
- [Laravel 5.5][laravel-5.5]
- [laracasts/flash][laracasts-flash]
- [Bootstrap][bootstrap-3] (for styling)

## Install

### Via Composer

``` bash
$ composer require dododedodonl/laravel-resource-kit
```

### Views and config
You can publish the views and config file.
``` bash
php artisan vendor:publish --tag=resourcekit
```

Or seperately
``` bash
php artisan vendor:publish --tag=resourcekit.views
php artisan vendor:publish --tag=resourcekit.config
```

### Bootstrap
It is assumed, bootstrap is installed.
``` bash
php artisan preset bootstrap
npm install
npm run dev
```

## Common examples

### Custom views
It is possible to create custom views. The package will search for `modelName.action` (for example `posts.show`) and prefers to use those.

### Available Traits

These self explanatory traits are available for `ResourceController` and `LinkedResourceController`.
- IsNotCreatable
- IsNotDeletable
- IsNotEditable

## ResourceController example

This example assumes you have a `Post` model extending `Dododedodonl\LaravelResourceKit\Model.php`. Also, `AdjustPostRequest` has to exist, this can be generated with `php artisan make:request AdjustPostRequest`. In this example, we do not want a post to be deletable.

Note: only fillable attributes will be saved, but for now the form displays all not filtered variables.

app/Http/Controllers/PostsController.php
``` php
<?php

namespace App\Http\Controllers;

use Dododedodonl\LaravelResourceKit\ResourceController;
use Dododedodonl\LaravelResourceKit\Traits\IsNotDeletable;

class PostsController extends ResourceController
{
    use IsNotDeletable;

    // By default, auth middleware is applied, overwrite to not do that
    protected $authenticated = false;

    //Link on Post model to any linked resources (replies in this case)
    protected $show_linked_resources = [
        'replies',
    ];
}
```

app/Http/Request/AdjustPostRequest.php
``` php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'          => 'required|string',
            'body'           => 'required|string',
        ];
    }
}
```

routes/web.php
``` php
<?php
// Route::get is not necessary in this case because it uses the IsNotDeletable trait, just here for documentation
Route::get('posts/{post}/confirm', 'PostsController@confirm')->name('posts.confirm');
Route::resource('posts', 'PostsController');
```

## LinkedResourceController example
Those are not fully implemented yet, but here is an example.

app/Http/Controllers/PostsWithReplyController.php
``` php
<?php

namespace App\Http\Controllers;

use Dododedodonl\LaravelResourceKit\LinkedResourceController;

class PostsWithReplyController extends LinkedResourceController
{
    // By default, auth middleware is applied, overwrite to not do that
    protected $authenticated = false;
}

```

app/Http/Request/AdjustPostRequest.php
``` php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustPostWithReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body'           => 'required|string',
        ];
    }
}
```

routes/web.php
``` php
<?php
Route::get('posts/{post}/repy/{reply}/confirm', 'PostsWithReplyController@confirm')->name('posts.reply.confirm');
Route::resource('posts.reply', 'PostsWithReplyController');
```

## Future
These things need to be fixed in approximately this order.

- --Fully implement linked resources-- done
- --Add linked resource to resource show view?-- done
- Add tests and clean up code
- Route helper
- Laracasts/Flash independent?

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[laracasts-flash]:https://github.com/Laracasts/Flash
[laravel-5.5]:https://laravel.com/docs/5.5
[bootstrap-3]:https://getbootstrap.com/docs/3.3/
