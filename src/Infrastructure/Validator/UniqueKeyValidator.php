<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Document\Flow\Screen;
use App\Domain\Document\Flow\Section;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueKeyValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueKey) {
            throw new UnexpectedTypeException($constraint, UniqueKey::class);
        }

        if (!is_iterable($value)) {
            throw new UnexpectedTypeException(get_debug_type($value), Collection::class);
        }

        $sectionKeys = [];
        foreach ($value as $sectionIndex => $section) {
            if (!$section instanceof Section) {
                throw new UnexpectedTypeException(get_debug_type($section), Section::class);
            }

            $sectionKey = $section->getKey();
            if (in_array($sectionKey, $sectionKeys, true)) {
                $this->context->buildViolation($constraint->message)
                    ->atPath((string) $sectionIndex)
                    ->setInvalidValue($sectionKey)
                    ->addViolation()
                ;
            }
            $sectionKeys[] = $sectionKey;

            $screenKeys = [];
            /** @var Screen $screen */
            foreach ($section->getScreens() as $screenIndex => $screen) {
                $screenKey = $screen->getKey();
                if (in_array($screenKey, $screenKeys, true)) {
                    $this->context->buildViolation($constraint->message)
                        ->atPath($sectionIndex.'.screens.'.$screenIndex)
                        ->setInvalidValue($screenKey)
                        ->addViolation()
                    ;
                }
                $screenKeys[] = $screenKey;
            }
        }
    }
}
