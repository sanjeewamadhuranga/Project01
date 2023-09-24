<?php

declare(strict_types=1);

namespace App\Infrastructure\Checklist;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['app.applicable_checklist'])]
interface ApplicableChecklistInterface extends ChecklistInterface
{
    public function isApplicable(string $name): bool;
}
