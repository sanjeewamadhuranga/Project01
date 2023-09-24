<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

final class Gravatar
{
    public static function getAvatar(string $email): string
    {
        return sprintf('https://secure.gravatar.com/avatar/%s', self::createEmailHash($email));
    }

    private static function createEmailHash(string $email): string
    {
        return md5(strtolower(trim($email)));
    }
}
