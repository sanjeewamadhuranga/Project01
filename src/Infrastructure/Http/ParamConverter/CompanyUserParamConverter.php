<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ParamConverter;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyUserParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $company = $request->attributes->get('company');

        if (!$company instanceof Company) {
            return false;
        }

        $userId = $request->attributes->get('userId');
        $user = $company->getUser($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf('Could not find user with sub: %s', $userId));
        }

        $request->attributes->set($configuration->getName(), $user);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), User::class, true);
    }
}
