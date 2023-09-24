<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Doctrine\Criteria;

use App\Infrastructure\Doctrine\Criteria\Regex;
use App\Tests\Unit\UnitTestCase;

class RegexTest extends UnitTestCase
{
    private const TEST_REGEX = '^My **** test string / \d+$';

    public function testItEscapesStringForContainsFilter(): void
    {
        $regex = Regex::contains(self::TEST_REGEX);
        self::assertSame('i', $regex->getFlags());
        self::assertSame('\^My \*\*\*\* test string / \\\\d\+\$', $regex->getPattern());
    }

    public function testItEscapesStringForStartsWithFilter(): void
    {
        $regex = Regex::startsWith(self::TEST_REGEX);
        self::assertSame('i', $regex->getFlags());
        self::assertSame('^\^My \*\*\*\* test string / \\\\d\+\$', $regex->getPattern());
    }

    public function testItEscapesStringForEndsWithFilter(): void
    {
        $regex = Regex::endsWith(self::TEST_REGEX);
        self::assertSame('i', $regex->getFlags());
        self::assertSame('\^My \*\*\*\* test string / \\\\d\+\$$', $regex->getPattern());
    }

    public function testItEscapesStringForEqualsFilter(): void
    {
        $regex = Regex::equals(self::TEST_REGEX);
        self::assertSame('i', $regex->getFlags());
        self::assertSame('^\^My \*\*\*\* test string / \\\\d\+\$$', $regex->getPattern());
    }

    public function testItHandlesFlags(): void
    {
        self::assertSame('m', Regex::equals('test', 'm')->getFlags());
        self::assertSame('x', Regex::contains('test', 'x')->getFlags());
        self::assertSame('s', Regex::startsWith('test', 's')->getFlags());
        self::assertSame('u', Regex::endsWith('test', 'u')->getFlags());
    }
}
