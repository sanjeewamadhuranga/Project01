<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Rector\Class_\CommandDescriptionToPropertyRector;
use Rector\Symfony\Rector\Class_\CommandPropertyToAttributeRector;
use Rector\Symfony\Set\SymfonyLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    // register a single rule
    $rectorConfig->rules([
        InlineConstructorDefaultToPropertyRector::class,
        CommandPropertyToAttributeRector::class,
        TypedPropertyRector::class,
        FinalizePublicClassConstantRector::class,
    ]);

    $rectorConfig->skip([
        // Do not promote properties in Documents as it is less readable.
        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__.'/src/Domain/Document',
        ],
        // First class callables in twig filters are not easy to unit test. Skipping for now.
        FirstClassCallableRector::class => [
            __DIR__.'/src/Infrastructure/Twig',
        ],
        // This causes many false positives with PHPStan
        NullToStrictStringFuncCallArgRector::class,
        // Produces false positives like 'Locale' -> \Locale::class
        StringClassNameToClassConstantRector::class,
        // Deprecated in Symfony 6.2
        CommandDescriptionToPropertyRector::class,
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        DoctrineSetList::DOCTRINE_ODM_23,
        DoctrineSetList::GEDMO_ANNOTATIONS_TO_ATTRIBUTES,
        SymfonyLevelSetList::UP_TO_SYMFONY_60,
    ]);
};
