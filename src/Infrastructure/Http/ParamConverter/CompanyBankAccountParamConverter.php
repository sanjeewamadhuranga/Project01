<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ParamConverter;

use App\Domain\Document\Company\BankAccount;
use App\Domain\Document\Company\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyBankAccountParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $company = $request->attributes->get('company');

        if (!$company instanceof Company) {
            return false;
        }

        $bankAccountId = $request->attributes->get('bankAccountId');
        $bankAccount = $company->getBankAccount($bankAccountId);

        if (!$bankAccount instanceof BankAccount) {
            throw new NotFoundHttpException(sprintf('Could not find bankAccount with id: %s', $bankAccountId));
        }

        $request->attributes->set($configuration->getName(), $bankAccount);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), BankAccount::class, true);
    }
}
