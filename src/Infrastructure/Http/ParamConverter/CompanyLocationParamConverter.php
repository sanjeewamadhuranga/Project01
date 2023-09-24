<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ParamConverter;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyLocationParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $company = $request->attributes->get('company');

        if (!$company instanceof Company) {
            return false;
        }

        $locationId = $request->attributes->get('locationId');
        $location = $company->getLocation($locationId);

        if (!$location instanceof Location) {
            throw new NotFoundHttpException(sprintf('Could not find location with id: %s', $locationId));
        }

        $request->attributes->set($configuration->getName(), $location);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), Location::class, true);
    }
}
