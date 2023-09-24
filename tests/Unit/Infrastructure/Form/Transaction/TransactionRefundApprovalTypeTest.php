<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Transaction;

use App\Domain\Document\Transaction\Transaction;
use App\Domain\Settings\Features;
use App\Domain\Transaction\TransactionRefundApproval;
use App\Infrastructure\Form\Transaction\TransactionRefundApprovalType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class TransactionRefundApprovalTypeTest extends TypeTestCase
{
    private Features&Stub $features;

    protected function setUp(): void
    {
        $this->features = $this->createStub(Features::class);

        parent::setUp();
    }

    public function testItDoesNotDisplaysCreateDoubleEntryCreditTransactionWhenIsNewRefundFlowDisabled(): void
    {
        $this->features->method('isNewRefundFlowEnabled')->willReturn(false);

        $form = $this->factory->create(TransactionRefundApprovalType::class, $this->getTransactionRefundApproval());

        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessage('Child "createDoubleEntryCreditTransaction" does not exist.');

        $form->get('createDoubleEntryCreditTransaction');
    }

    public function testItDoesNotDisplaysCreateDoubleEntryCreditTransactionWhenNoParentTransaction(): void
    {
        $this->features->method('isNewRefundFlowEnabled')->willReturn(true);

        $form = $this->factory->create(TransactionRefundApprovalType::class, $this->getTransactionRefundApproval(false));

        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessage('Child "createDoubleEntryCreditTransaction" does not exist.');

        $form->get('createDoubleEntryCreditTransaction');
    }

    public function testItDoesNotDisplaysCreateDoubleEntryCreditTransactionWhenIsParentTransactionWithoutRemittanceId(): void
    {
        $this->features->method('isNewRefundFlowEnabled')->willReturn(true);

        $form = $this->factory->create(TransactionRefundApprovalType::class, $this->getTransactionRefundApproval(parentTransactionWithRemittance: false));

        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessage('Child "createDoubleEntryCreditTransaction" does not exist.');

        $form->get('createDoubleEntryCreditTransaction');
    }

    public function testItDisplaysCreateDoubleEntryCreditTransactionWhenIsParentTransactionWithRemittanceId(): void
    {
        $this->features->method('isNewRefundFlowEnabled')->willReturn(true);

        $form = $this->factory->create(TransactionRefundApprovalType::class, $this->getTransactionRefundApproval());

        self::assertInstanceOf(Form::class, $form->get('createDoubleEntryCreditTransaction'));
    }

    private function getTransactionRefundApproval(bool $parentTransaction = true, bool $parentTransactionWithRemittance = true): TransactionRefundApproval
    {
        $transaction = new Transaction();
        $transaction->setId(uniqid());

        if ($parentTransaction) {
            $parentTransaction = new Transaction();
            if ($parentTransactionWithRemittance) {
                $parentTransaction->setRemittanceId(uniqid());
            }

            $transaction->setParentTransaction($parentTransaction);
        }

        return new TransactionRefundApproval($transaction, true);
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new TransactionRefundApprovalType($this->features)], []),
        ];
    }
}
