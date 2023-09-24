<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\ManagerRole;

use App\Domain\Document\Security\ManagerPortalRole;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class ManagerRoleType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->security->isGranted('PROTECT_ROLE_BY_OTHER_ROLE', $builder->getData())) {
            throw new AccessDeniedException();
        }

        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
        ;

        if ($this->security->isGranted('all_permissions')) {
            $builder
                ->add('protectedByRole', DocumentType::class, [
                    'required' => false,
                    'class' => ManagerPortalRole::class,
                    'label' => 'manager_role.formLabels.protectedByRole',
                ])
            ;
        }

        $builder->add('newPermissions', PermissionsType::class, [
            'label' => 'manager_role.formLabels.newPermissions',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ManagerPortalRole::class,
        ]);
    }
}
