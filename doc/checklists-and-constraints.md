# Checklists

Checklists are used for validate a list of constraints against some resource. Mainly used before enable payment
provider.

# ChecklistFinder

ChecklistFinder is a service for finding applicable checklist. It has a list of tagged checklists with
implemented `ApplicableChecklistInterface`. It iterates on checklists and returns first which return true for
it's `isApplicable` method.

# Constraints

Constraint is a class which holds metadata like scope and field for displaying some information on the UI. Constraint
could implement `ValidatorInterface` for self validating or return name of class for custom validator. Checklist should
have at least one constraint. Each constraint should extend `AbstractConstraint` class which handle logic for validate

# AbstractChecklist

This class holds locator for services tagged with `ValidatorInterface` which is used in `validate` method and abstract
method `getConstraints` which should the list of checklist constraints to validate. Each checklist should extend
`AbstractChecklist` class.

# Creating new Checklist and Constraint

In order to create new checklist we need to extend `AbstractChecklist` class like in the following example:

```php
<?php

use App\Infrastructure\Checklist\AbstractChecklist;

class ExampleChecklist extends AbstractChecklist
{
    public function getConstraints(): iterable
    {
        yield new Constraint\ExampleConstraint();
    }
}

```

Checklist from above have `ExampleConstraint` which looks like this:

```php
<?php

use App\Infrastructure\Checklist\AbstractConstraint;
use App\Infrastructure\Checklist\CompanyAwareValidationContext;
use App\Infrastructure\Checklist\Validator\ValidatorInterface;
use App\Infrastructure\ProviderOnboarding\Field;
use App\Infrastructure\ProviderOnboarding\Scope;

class ExampleConstraint extends AbstractConstraint implements ValidatorInterface
{
    public function getScope(): Scope
    {
        return Scope::COMPANY;
    }

    public function getField(): Field
    {
        return Field::currency;
    }

    public function isValid(CompanyAwareValidationContext $context): bool
    {
        return $context->getCompany()->hasContractId();
    }
}

```

Usage of the simple `ExampleChecklist` (without `ChecklistFinder` because that checklist does not
implement `ApplicableChecklistInterface`):

```php
#[Route('/{providerId}/checks', name: 'checks')]
public function checks(
    Company $company,
    BasicChecklist $basicChecklist,
): Response {
    $checks = $basicChecklist->validate(new CompanyAwareValidationContext($company));

    return $this->render(
        'company/payment_method/checks.html.twig',
        [
            'company' => $company,
            'checklist' => $checks,
        ]
    );
}
```

If checklist should be findable it should implement `ApplicableChecklistInterface`. With that interface the checklist
will be available in `ChecklistFinder` like that:

```php
<?php

use App\Infrastructure\Checklist\AbstractChecklist;
use App\Infrastructure\Checklist\ApplicableChecklistInterface;

class ExampleChecklist extends AbstractChecklist implements ApplicableChecklistInterface
{
    public function getConstraints(): iterable
    {
        yield new Constraint\ExampleConstraint();
    }
    
    public function isApplicable(string $name): bool
    {
        return $name === 'example';
    }
}

```

```php
#[Route('/{providerId}/checks', name: 'checks')]
public function checks(
    Company $company,
    ChecklistFinder $checklistFinder,
): Response {
    $checks = $checklistFinder->find('example')->validate(new CompanyAwareValidationContext($company));

    return $this->render(
        'company/payment_method/checks.html.twig',
        [
            'company' => $company,
            'checklist' => $checks,
        ]
    );
}
```
