<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Provides the Cognito identifier type based on the user input - SUB if UUID is provided, email/phone number if anything else is passed.
 */
class CognitoIdentifierGuesser
{
    public function __construct(private readonly ValidatorInterface $validator, private readonly SystemSettings $systemSettings)
    {
    }

    public function getIdentifierType(string $identifier): string
    {
        if ($this->isValid($identifier, [new Uuid()])) {
            return CognitoUser::ATTRIBUTE_SUB;
        }

        if (FederatedIdentityType::EMAIL === $this->systemSettings->getFederatedIdentityType()) {
            return CognitoUser::ATTRIBUTE_EMAIL;
        }

        return CognitoUser::ATTRIBUTE_PHONE_NUMBER;
    }

    /**
     * @param Constraint[] $constraints
     */
    private function isValid(string $identifier, array $constraints): bool
    {
        return 0 === $this->validator->validate($identifier, $constraints)->count();
    }
}
