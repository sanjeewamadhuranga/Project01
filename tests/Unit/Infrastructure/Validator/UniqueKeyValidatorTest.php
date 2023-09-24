<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Domain\Document\Flow\Screen;
use App\Domain\Document\Flow\Section;
use App\Infrastructure\Validator\UniqueKey;
use App\Infrastructure\Validator\UniqueKeyValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<UniqueKeyValidator>
 */
class UniqueKeyValidatorTest extends ConstraintValidatorTestCase
{
    public function testItBuildsViolationWhenSameKeyInSections(): void
    {
        $invalidValue = 'sectionKey';

        $section1 = $this->getSection($invalidValue);
        $section2 = $this->getSection($invalidValue);

        $constraint = new UniqueKey();
        $this->validator->validate([$section1, $section2], $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.1')
            ->setInvalidValue($invalidValue)
            ->assertRaised();
    }

    public function testItBuildsViolationWhenSameKeyInScreens(): void
    {
        $invalidValue = 'screenyKey';
        $constraint = new UniqueKey();

        $section = $this->getSection('someKey', [
           $this->getScreen($invalidValue),
           $this->getScreen($invalidValue),
        ]);

        $this->validator->validate([$section], $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.0.screens.1')
            ->setInvalidValue($invalidValue)
            ->assertRaised();
    }

    public function testItDoesNotBuildViolationsWhenSameKeyInDifferentScreens(): void
    {
        $sameValue = 'anotherScreenyKey';

        $section1 = $this->getSection('firstKey', [
            $this->getScreen($sameValue),
        ]);
        $section2 = $this->getSection('secondKey', [
            $this->getScreen($sameValue),
        ]);

        $this->validator->validate([$section1, $section2], new UniqueKey());

        $this->assertNoViolation();
    }

    protected function createValidator(): UniqueKeyValidator
    {
        return new UniqueKeyValidator();
    }

    /**
     * @param Screen[] $screens
     */
    private function getSection(string $key, array $screens = []): Section
    {
        $section = new Section();
        $section->setKey($key);
        foreach ($screens as $screen) {
            $section->addScreen($screen);
        }

        return $section;
    }

    private function getScreen(string $key): Screen
    {
        $screen = new Screen();
        $screen->setKey($key);

        return $screen;
    }
}
