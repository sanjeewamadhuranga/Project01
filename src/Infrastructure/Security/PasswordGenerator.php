<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\Generator\RequirementPasswordGenerator;
use RuntimeException;

final class PasswordGenerator
{
    public static function generate(int $length): string
    {
        $generator = (new RequirementPasswordGenerator())
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, true)
            ->setMinimumCount(ComputerPasswordGenerator::OPTION_UPPER_CASE, 2)
            ->setMinimumCount(ComputerPasswordGenerator::OPTION_LOWER_CASE, 2)
            ->setMinimumCount(ComputerPasswordGenerator::OPTION_NUMBERS, 2)
            ->setMinimumCount(ComputerPasswordGenerator::OPTION_SYMBOLS, 2)
            ->setLength($length)
        ;

        if (!$generator->validLimits()) {
            throw new RuntimeException('Invalid password length');
        }

        return $generator->generatePassword();
    }
}
