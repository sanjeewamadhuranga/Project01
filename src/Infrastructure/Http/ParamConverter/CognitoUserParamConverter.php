<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ParamConverter;

use App\Application\Security\CognitoUser;
use App\Infrastructure\Security\CognitoUserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CognitoUserParamConverter implements ParamConverterInterface
{
    public function __construct(private readonly CognitoUserManagerInterface $cognitoUserManager)
    {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $sub = $request->attributes->get('sub');
        $user = $this->cognitoUserManager->getUserBySub($sub);

        if (!$user instanceof CognitoUser && !$configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('Could not find user with sub: %s', $sub));
        }

        $request->attributes->set($configuration->getName(), $user);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), CognitoUser::class, true);
    }
}
