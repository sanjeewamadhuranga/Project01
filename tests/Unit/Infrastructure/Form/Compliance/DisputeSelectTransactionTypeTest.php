<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Compliance;

use App\Infrastructure\Form\Compliance\DisputeSelectTransactionType;
use App\Infrastructure\Form\DataTransformer\IdToTransactionTransformer;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class DisputeSelectTransactionTypeTest extends TypeTestCase
{
    private IdToTransactionTransformer&Stub $transformer;

    protected function setUp(): void
    {
        $this->transformer = $this->createStub(IdToTransactionTransformer::class);

        parent::setUp();
    }

    public function testItUsesProvidedDataTransformer(): void
    {
        $form = $this->factory->create(DisputeSelectTransactionType::class);

        self::assertSame([$this->transformer], $form->get('transaction')->getConfig()->getModelTransformers());
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new DisputeSelectTransactionType($this->transformer)], []),
        ];
    }
}
