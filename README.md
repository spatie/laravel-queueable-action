# Queueable actions in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-queueable-action.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-queueable-action)
[![StyleCI](https://github.styleci.io/repos/170877229/shield?branch=master)](https://github.styleci.io/repos/170877229)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-queueable-action/run-tests?label=tests)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-queueable-action.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-queueable-action)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-queueable-action.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-queueable-action)

Actions are a way of structuring your business logic in Laravel. 
This package adds easy support to make them queueable.

```php
$myAction->onQueue()->execute();
```

You can specify a queue name.

```php
$myAction->onQueue('my-favorite-queue')->execute();
```

## Support us

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us). 

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-queueable-action
```

## Usage

If you want to know about the reasoning behind actions and their asynchronous usage, 
you should read the dedicated blog post: [https://stitcher.io/blog/laravel-queueable-actions](https://stitcher.io/blog/laravel-queueable-actions).

You can use the following Artisan command to generate queueable and synchronous action classes on the fly.

```
php artisan make:action MyAction [--sync]
```

Here's an example of queueable actions in use:

``` php
class MyAction
{
    use QueueableAction;

    public function __construct(
        OtherAction $otherAction,
        ServiceFromTheContainer $service
    ) {
        // Constructor arguments can come from the container.
    
        $this->otherAction = $otherAction;
        $this->service = $service;
    }

    public function execute(
        MyModel $model,
        RequestData $requestData
    ) {
        // The business logic goes here, this can be executed in an async job.
    }
}
```

```php
class MyController
{
    public function store(
        MyRequest $request,
        MyModel $model,
        MyAction $action
    ) {
        $requestData = RequestData::fromRequest($myRequest);
        
        // Execute the action on the queue:
        $action->onQueue()->execute($model, $requestData);
        
        // Or right now:
        $action->execute($model, $requestData);
    }
}
```

### Chaining actions

You can chain actions by wrapping them in the `ActionJob`. 

Here's an example of two actions with the same arguments:

```php
use Spatie\QueueableAction\ActionJob;

$args = [$userId, $data];

app(MyAction::class)
    ->onQueue()
    ->execute(...$args)
    ->chain([
        new ActionJob(AnotherAction::class, $args),
    ]);
```

The `ActionJob` takes the action class *or* instance as the first argument followed by an array of the action's own arguments.

### Custom Tags

If you want to change what tags show up in Horizon for your custom actions you can override the `tags()` function.

``` php
class CustomTagsAction
{
    use QueueableAction;

    // ...

    public function tags() {
        return ['action', 'custom_tags'];
    }
}
```

### Job Middleware

Middleware where action job passes through can be added by overriding the `middleware()` function.

``` php
class CustomTagsAction
{
    use QueueableAction;

    // ...

    public function middleware() {
        return [new RateLimited()];
    }
}
```

### What is the difference between actions and jobs?

In short: constructor injection allows for much more flexibility. 
You can read an in-depth explanation here: [https://stitcher.io/blog/laravel-queueable-actions](https://stitcher.io/blog/laravel-queueable-actions#what's-the-difference-with-jobs?!?).

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Brent Roose](https://github.com/brendt)
- [Alex Vanderbist](https://github.com/alexvanderbist)
- [Sebastian De Deyne](https://github.com/sebdedeyne)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
