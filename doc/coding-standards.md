# Coding Standards

This project uses [PHPStan][phpstan] on level 8 with many strict rules, including typehints and [generics][generics] docblocks enforced.
Strict typing is enforced by `declare_strict_types` rule in PHP-CS-Fixer.

PHP code must follow PSR-1, PSR-2, PSR-4, PSR-12 standards, SOLID principles and rely on PSR or other opinionated interfaces (Symfony, Doctrine) instead of concrete implementations.

To run CS fixer, PHPStan and PHPUnit, simply run:

```shell
composer test
composer style
composer stan
```

## PHP features

The application is running on PHP 8.1, hence it is recommended to use any latest PHP syntax or features where convenient.
This includes usage of `enum`s, typed properties, constructor property promotion, generators, intersection and union types, attributes, `readonly` properties and more.

## Opening a PR

- Each PR must have a meaningful description that explains the purpose of the change.
- You may add a link to the task and/or screenshots showing the new functionality.
- Database changes/migrations must be documented in the description.
- Any new environment variables, feature flags or configs must be documented in the description.
- Any potential conflicts or dependencies must be mentioned in the description.

## Security

Even though the application works behind a strict NAT, security must be a priority when contributing to this project.
When working on a new feature, one should make sure it does not introduce any new vulnerability nor expose any sensitive or restricted files publicly.

Any change that may have some side effects or potential workarounds that may lead to permission elevation must be reported immediately.

## Configuration files

In this project we decided on following configuration style:

- DependencyInjection is configured via PHP files (`config/services.php`). This is to make use of better IDE suggestions, static analysis and type-safety.
- Some DI features are configured via PHP 8 Attributes where possible (`#[AutoconfigureTag]`, `#[TaggedLocator]`, `#[When]`, etc) to reduce the amount of code in `services.php`
- Bundles and Framework configuration remains in YAML files to allow Flex recipes to update them with ease.
- Node packages' configs are stored in separate files in project root

## Dependency management

Composer v2.2+ and NPM 8+ is used for dependency management in this project. Before adding a new dependency, make sure that the package:

- is actively maintained
- has security vulnerabilities resolved in the past
- does not prevent us from upgrading to future versions of PHP, NodeJS or Symfony
- is actually being used (avoid adding big packages while using only small piece of it)

Dependencies should be upgraded on weekly basis to make sure any potential security vulnerabilities are patched and we are not blocked from any other upgrades.

A GitHub action is executed for each `composer.lock` change that presents the changes in PR comment for developers convenience.

[phpstan]: https://phpstan.org/
[generics]: https://phpstan.org/blog/generics-in-php-using-phpdocs

## Commands

To define a command name, use PHP 8 attributes:

```php
#[AsCommand(name: 'app:migrations:generate')]
class GenerateCommand extends Command
{
    ......
 }
```

## Event listeners/subscribers

Listening to domain or infrastructure events should happen by registering an event listener using `#[AsEventListener]` attribute:

```php
#[AsEventListener]
class MyListener
{
    public function __invoke(Event $event)
    {}
 }
```

The attribute can be places on class-level if it's invokable, or on the method level if it should listen to multiple events.
Specifying additional parameters like event name, priority or method is possible too.

The only exceptions are Doctrine EventSubscribers which should still implement `Doctrine\Bundle\MongoDBBundle\EventSubscriber\EventSubscriberInterface`.
That's because Doctrine uses its own event dispatcher.
