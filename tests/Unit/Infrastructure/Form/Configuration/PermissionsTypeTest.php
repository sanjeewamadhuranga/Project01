<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration;

use App\Infrastructure\Form\Configuration\ManagerRole\PermissionsType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Core\Security;

class PermissionsTypeTest extends TypeTestCase
{
    private Security&Stub $security;

    protected function setUp(): void
    {
        $this->security = $this->createStub(Security::class);

        parent::setUp();
    }

    public function testItHasDisableAttrWhenPermissionIsNotGranted(): void
    {
        $this->security->method('isGranted')->willReturn(false);

        $form = $this->factory->create(PermissionsType::class);

        /** @var ChoiceView $choice */
        foreach ($form->getConfig()->getAttribute('choice_list_view')->choices as $choice) {
            self::assertTrue($choice->attr['disabled']);
        }
    }

    public function testItHasEnabledAttrWhenPermissionIsGranted(): void
    {
        $this->security->method('isGranted')->willReturn(true);

        $form = $this->factory->create(PermissionsType::class);

        /** @var ChoiceView $choice */
        foreach ($form->getConfig()->getAttribute('choice_list_view')->choices as $choice) {
            self::assertFalse($choice->attr['disabled']);
        }
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new PermissionsType($this->security)], []),
        ];
    }
}
