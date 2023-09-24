<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Security;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Form\Security\UserType;
use App\Tests\Unit\Infrastructure\Form\TypeTestCaseWithManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCaseWithManagerRegistry
{
    private Security&Stub $security;

    protected function setUp(): void
    {
        $this->security = $this->createStub(Security::class);
        $this->security->method('getUser')->willReturn(new Administrator());

        parent::setUp();
    }

    /**
     * @dataProvider fieldsThatAreVisibleDuringCreation
     */
    public function testThereIsNoFieldWhenValidationGroupsDoNotContainsCreate(string $fieldName): void
    {
        $form = $this->factory->create(UserType::class);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(sprintf('Child "%s" does not exist.', $fieldName));

        $form->get($fieldName);
    }

    /**
     * @dataProvider fieldsThatAreVisibleDuringCreation
     */
    public function testThereIsFieldWhenValidationGroupsContainsCreate(string $fieldName): void
    {
        $form = $this->factory->create(UserType::class, options: ['validation_groups' => ['create']]);

        self::assertInstanceOf(Form::class, $form->get($fieldName));
    }

    public function testRolesAreDisabledWhenPermissionsAreNotGranted(): void
    {
        $this->security->method('isGranted')->willReturn(false);

        $form = $this->factory->create(UserType::class);
        $choices = $form->get('managerPortalRoles')->getConfig()->getAttribute('choice_list_view')->choices;

        /** @var ChoiceView $choice */
        foreach ($choices as $choice) {
            self::assertSame([
                'readonly' => true,
                'onclick' => 'return false;',
            ], $choice->attr);
        }
    }

    public function testRolesAreNotDisabledWhenPermissionIsGranted(): void
    {
        $this->security->method('isGranted')->willReturn(true);

        $form = $this->factory->create(UserType::class);
        $choices = $form->get('managerPortalRoles')->getConfig()->getAttribute('choice_list_view')->choices;

        /** @var ChoiceView $choice */
        foreach ($choices as $choice) {
            self::assertSame([], $choice->attr);
        }
    }

    /**
     * @return array<int, PreloadedExtension|ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new UserType($this->security)], []),
            new PreloadedExtension([new DocumentType($this->getManagerRegistry(
                $this->getRepository([
                    $this->getManagerPortalRole('TEST_1'),
                    $this->getManagerPortalRole('TEST_2'),
            ])
            ))], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    private function getManagerPortalRole(string $name): ManagerPortalRole
    {
        $role = new ManagerPortalRole();
        $role->setName($name);

        return $role;
    }

    /**
     * @return iterable<string, string[]>
     */
    private function fieldsThatAreVisibleDuringCreation(): iterable
    {
        yield 'Email' => ['email'];
        yield 'AccountExpirationDate' => ['accountExpirationDate'];
    }
}
