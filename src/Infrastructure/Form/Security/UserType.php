<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Security;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (in_array('create', $options['validation_groups'] ?? [], true)) {
            $builder->add('email', EmailType::class, [
                'label' => 'administrators.label.email',
            ]);

            $builder->add('accountExpirationDate', DateType::class, [
                'label' => 'administrators.label.accountExpirationDate',
                'widget' => 'single_text',
                'required' => false,
            ]);
        }

        $builder
            ->add('managerPortalRoles', DocumentType::class, [
                'label' => 'administrators.label.roles',
                'class' => ManagerPortalRole::class,
                'multiple' => true,
                'expanded' => true,
                'choice_attr' => fn (ManagerPortalRole $role) => $this->security->isGranted('PROTECT_ROLE_BY_OTHER_ROLE', $role) ? [] : ['readonly' => true, 'onclick' => 'return false;'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Administrator::class,
        ]);
    }
}
