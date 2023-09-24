<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\ManagerRole;

use App\Application\Security\Permissions\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PermissionsType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => true,
            'choices' => array_combine(Permission::getAllPermissions(), Permission::getAllPermissions()),
            'choice_attr' => fn (string $choice) => ['disabled' => !$this->security->isGranted('EDIT_ROLE_PERMISSIONS', $choice)],
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // prepare array
        $resultArray = [];
        foreach ($options['choices'] as $permission) {
            $resultArray[explode('.', $permission, 2)[0]][] = $permission;
        }
        $view->vars['groupedPermissions'] = $resultArray;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
