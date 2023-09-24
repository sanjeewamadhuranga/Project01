<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Domain\Document\Company\Company;
use App\Infrastructure\Repository\Company\CompanyRepository;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Company::class,
            'choice_label' => static fn (Company $company): string => sprintf('%s (%s)', $company->getTradingName(), $company->getId()),
            'query_builder' => static fn (CompanyRepository $repository) => $repository->findForCompanyChoiceList(),
            'attr' => ['class' => 'tom-select'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return DocumentType::class;
    }
}
