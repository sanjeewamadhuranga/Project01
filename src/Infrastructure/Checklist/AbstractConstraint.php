<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist;

use App\Infrastructure\Checklist\Validator\ValidatorInterface;
use App\Infrastructure\ProviderOnboarding\Field;
use App\Infrastructure\ProviderOnboarding\Scope;
use Doctrine\Inflector\InflectorFactory;

abstract class AbstractConstraint
{
    protected bool $checked = false;

    /** @var array<string, string> */
    public array $translationParameters;

    protected function getTranslationPrefix(): string
    {
        return 'provider_onboarding.'.InflectorFactory::create()->build()->tableize(basename(str_replace('\\', '/', $this::class)));
    }

    public function getDescriptionTranslationKey(): string
    {
        return $this->getTranslationPrefix().'.description';
    }

    public function getHelpTranslationKey(): string
    {
        return $this->getTranslationPrefix().'.help';
    }

    public function handle(ValidatorInterface $validator, CompanyAwareValidationContext $context): AbstractConstraint
    {
        if ($validator->isValid($context)) {
            $this->checked = true;
        }

        $this->translationParameters = $this->provideTranslationParameters($context);

        return $this;
    }

    /**
     * @return array<string, string>
     */
    protected function provideTranslationParameters(CompanyAwareValidationContext $context): array
    {
        return [];
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function getValidatorClass(): string
    {
        return $this::class;
    }

    abstract public function getScope(): Scope;

    abstract public function getField(): Field|string;
}
