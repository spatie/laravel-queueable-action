# Changelog

All notable changes to `laravel-queueable-actions` will be documented in this file

## 2.13.1 - 2022-01-14

- Fix for running middleware twice (#66)

## 2.13.0 - 2021-11-24

- Drop support for PHP 7.4
- Add support for PHP 8.1

## 2.12.0 - 2021-06-01

- add `Batchable` trait

## 2.11.0 - 2021-04-21

- Require `php:^7.4|^8.0`
- Require `laravel/framework:^8.0`
- Support for backoff (#53)

## 2.10.3 - 2021-02-25

- Fix for broken actions (#49)

## 2.10.2 - 2020-02-02

- Fix (de)serialization of Models for queued action jobs (#48)

## 2.10.1 - 2020-02-02

- Add new QueueFake methods(`assertPushedWithChain`, `assertPushedWithoutChain`) (#47)

## 2.10.0 - 2020-02-02

- Add a configurable action job class (#46)

## 2.9.1 - 2020-12-20

- update `failed` method to accept `Throwable` (#44)

## 2.9.0 - 2020-12-02

- add support for PHP 8

## 2.8.0 - 2020-10-22

- Add support for invokeable actions (#36)

## 2.7.1 - 2020-10-02

- Add support for job middleware

## 2.7.0 - 2020-09-29

- Add `Spatie\QueueableAction\Testing\QueueableActionFake` class with test helpers (#27)

## 2.6.1 - 2020-09-09

- Add support for Laravel 8

## 2.6.0 - 2020-07-22

- Add `failed` support (#29)

## 2.5.0 - 2020-03-03

- Add Laravel 7.0 support

## 2.4.0 - 2020-01-01

- Allow custom tags to be set

## 2.3.0 - 2019-09-24

- Add `make:action` command

## 2.2.0 - 2019-09-04

- Add Laravel 6.0 support

## 2.1.1 - 2019-08-30

- add 'tries' and 'timeout' as queueable properties

## 2.1.0 - 2019-06-19

- pass the action class name directly to the ActionJob

## 2.0.0 - 2019-06-14

- allow the queue name to be specified

## 1.0.0 - 2019-01-05

- initial release
