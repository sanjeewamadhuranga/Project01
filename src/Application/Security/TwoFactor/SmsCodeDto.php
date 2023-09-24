<?php

declare(strict_types=1);

namespace App\Application\Security\TwoFactor;

use DateTime;

class SmsCodeDto
{
    private const NUMBER_OF_DIGITS_IN_SMS_CODE = 6;
    private const VALIDITY = '+3 minutes';

    public readonly string $code;

    public readonly DateTime $expiration;

    public function __construct()
    {
        $code = '';
        for ($i = 0; $i < self::NUMBER_OF_DIGITS_IN_SMS_CODE; ++$i) {
            $code .= random_int(0, 9);
        }

        $this->code = $code;

        $this->expiration = new DateTime(self::VALIDITY);
    }

    public function isCodeValid(string $code): bool
    {
        return hash_equals($this->code, $code) && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expiration < new DateTime();
    }
}
