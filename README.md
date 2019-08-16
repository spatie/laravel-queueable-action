# Queueable actions in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-queueable-action.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-queueable-action)
[![StyleCI](https://github.styleci.io/repos/170877229/shield?branch=master)](https://github.styleci.io/repos/170877229)
[![Build Status](https://img.shields.io/travis/spatie/laravel-queueable-action/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-queueable-action)
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

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-queueable-action
```

## Usage

If you want to know about the reasoning behind actions and their asynchronous usage, 
you should read the dedicated blog post: [https://stitcher.io/blog/laravel-queueable-actions](https://stitcher.io/blog/laravel-queueable-actions).

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

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Brent Roose](https://github.com/brendt)
- [Alex Vanderbist](https://github.com/alexvanderbist)
- [Sebastian De Deyne](https://github.com/sebdedeyne)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
