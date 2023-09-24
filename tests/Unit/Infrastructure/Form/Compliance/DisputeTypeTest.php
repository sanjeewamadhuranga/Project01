<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Compliance;

use App\Domain\Document\Compliance\Dispute;
use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\Form\Compliance\DisputeType;
use App\Infrastructure\Form\DataTransformer\IdToTransactionTransformer;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class DisputeTypeTest extends TypeTestCase
{
    private IdToTransactionTransformer&Stub $transformer;

    protected function setUp(): void
    {
        $this->transformer = $this->createStub(IdToTransactionTransformer::class);

        parent::setUp();
    }

    public function testItUsesProvidedDataTransformer(): void
    {
        $transaction = $this->createStub(Transaction::class);
        $transaction->method('getCurrency')->willReturn('USD');

        $dispute = new Dispute();
        $dispute->setTransaction($transaction);

        $form = $this->factory->create(DisputeType::class, $dispute);

        self::assertSame([$this->transformer], $form->get('transaction')->getConfig()->getModelTransformers());
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new DisputeType($this->transformer)], []),
        ];
    }
}
