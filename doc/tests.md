# Tests

This project aims to be tested as much as possible, as long as it makes sense - an actual test business value
is more important than arbitrary coverage numbers.

## Structure

Tests are split into two groups: unit and feature tests. Each test should extend appropriate base class:

 - `App\Tests\Unit\UnitTestCase` for unit tests
 - `App\Tests\Feature\BaseTestCase` for feature tests

Both classes provide useful helpers and tools to make testing easier.

## Naming and style convention

Test class should be named after the class it tests, with `Test` suffix. For example, `App\SomeClass` should be tested
in `App\SomeClassTest` class.

Each test case should be named in a way that describes what it does as a sentence. For example, `testItCanDoSomething`.
This is because we are using `testdox` output format, which produces human-readable output,
understandable by non-technical personnel. For example, `ShowAction::testItShowsItemDetails()` produces:

```
Show Action (App\Tests\Unit\Http\Controller\CRUD\ShowAction)
 ✔ It shows item details
```

## Data providers

When testing same method with different inputs, use data providers. For example:

```php
    /**
     * @dataProvider countryCodeProvider
     */
    public function testItConvertsCountryCodeToFlagEmoji(string $code, string $flag): void
    {
        self::assertSame($flag, Country::countryCodeToFlag($code));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function countryCodeProvider(): iterable
    {
        yield 'SG -> 🇸🇬' => ['SG', '🇸🇬'];
        yield 'MM -> 🇲🇲' => ['MM', '🇲🇲'];
    }
```

Please note the data provider uses indexes when yielding data, so that it's easier to identify which test case failed:

```
Country (App\Tests\Unit\Domain\Document\Country)
 ✔ It converts country code to flag emoji with SG·->·🇸🇬
 ✔ It converts country code to flag emoji with MM·->·🇲🇲
```

## Unit tests

When writing tests and implementing the functionalities, keep in mind that unit tests are preferred over feature tests
because they are faster and easier to write. Unit tests should be used for testing business logic, while feature tests
should be used for testing the whole user flow.

Do note that unit tests can also be used to verify some framework-specific functionality, like Symfony Validators and
Form Types, as long as they don't explicitly require a HTTP Kernel to be booted.

### Stub vs Mock

When writing a test, it's important to understand the difference between stub and mock. Mock should **only** be used
when adding expectations, when it's important to guarantee that certain method has been (or has not been) called on the mock.
Stub should be used when you do not require such expectation.

## Feature tests

Feature tests should extend `App\Tests\Feature\BaseTestCase` class. This class provides some additional methods that
are helpful during feature testing. Please refer to the class documentation for more information.
