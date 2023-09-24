<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Form\Compliance\CaseAssignType;
use App\Tests\Unit\Infrastructure\Form\TypeTestCaseWithManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaseAssignTypeTest extends TypeTestCaseWithManagerRegistry
{
    private readonly Security&Stub $security;

    private readonly TranslatorInterface&Stub $translator;

    private readonly PayoutBlock&Stub $case;

    protected function setUp(): void
    {
        $this->security = $this->createStub(Security::class);
        $this->translator = $this->createStub(TranslatorInterface::class);
        $this->case = $this->createStub(PayoutBlock::class);

        parent::setUp();
    }

    public function testApproverAndHandlerRenderingWhenHandlerIsSet(): void
    {
        $this->case->method('getHandler')->willReturn(new Administrator());
        $form = $this->factory->create(CaseAssignType::class, $this->case);
        self::assertTrue($form->has('approver'));
        self::assertInstanceOf(Form::class, $form->get('approver'));
        self::assertNotTrue($form->has('handler'));
    }

    public function testApproverAndHandlerRenderingWhenApproverIsSet(): void
    {
        $this->case->method('getApprover')->willReturn(new Administrator());
        $form = $this->factory->create(CaseAssignType::class, $this->case);
        self::assertTrue($form->has('handler'));
        self::assertInstanceOf(Form::class, $form->get('handler'));
        self::assertNotTrue($form->has('approver'));
    }

    /**
     * @return array<int, PreloadedExtension|ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new CaseAssignType($this->security, $this->translator)], []),
            new PreloadedExtension([new DocumentType($this->getManagerRegistry())], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
