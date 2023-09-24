<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Type;

use App\Application\DTO\Choiceable;
use App\Http\Controller\QuickSearchController;
use App\Infrastructure\Form\DataTransformer\IdToDocumentTransformer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides a form type for autocomplete Mongo documents.
 * The autocomplete endpoint should be secured, implemented in {@see QuickSearchController} and URL should be passed in options.
 * Documents should implement {@see Choiceable} interface.
 *
 * Make sure to disable validation in tests when using this form type (as it dynamically adds options on the fly).
 */
class AutocompleteDocumentType extends AbstractType
{
    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new IdToDocumentTransformer($this->managerRegistry, $options['class'], $options['multiple']);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'attr' => [
                'class' => 'tom-select form-select',
            ],
        ]);

        $resolver->setDefined(['class', 'multiple', 'autocomplete_url']);
        $resolver->setAllowedTypes('class', 'string');
        $resolver->setAllowedTypes('autocomplete_url', 'string');
        $resolver->setAllowedTypes('multiple', 'bool');
        $resolver->setRequired(['class', 'autocomplete_url']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (true === $options['multiple']) {
            $view->vars['full_name'] .= '[]';
        }

        $view->vars['attr']['multiple'] = $options['multiple'];
        $view->vars['attr']['name'] = $view->vars['full_name'];
        $view->vars['attr']['required'] = $options['required'];
        $view->vars['attr']['data-url'] = $options['autocomplete_url'];

        $data = $form->getData() ?? [];

        $view->vars['selected'] = $this->buildChoices(is_array($data) ? $data : [$data]);
    }

    /**
     * @param iterable<Choiceable> $data
     *
     * @return array<string, string>
     */
    protected function buildChoices(iterable $data): array
    {
        $choices = [];

        foreach ($data as $item) {
            $choices[$item->getId()] = (string) $item->getChoiceName();
        }

        return $choices;
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'document_autocomplete';
    }
}
