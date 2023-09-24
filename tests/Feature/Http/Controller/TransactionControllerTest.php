<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Application\Queue\Bus;
use App\Application\Queue\Message;
use App\Application\Queue\QueueName;
use App\Domain\Transaction\Status;
use App\Tests\Feature\BaseTestCase;
use Happyr\ServiceMocking\ServiceMock;
use Symfony\Component\HttpFoundation\Response;

class TransactionControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsTransactionListPage(): void
    {
        self::$client->request('GET', '/transactions');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('transaction-listing');
    }

    /**
     * @group smoke
     */
    public function testItListsTransactions(): void
    {
        self::$client->request('GET', '/transactions/list');
        self::assertResponseIsSuccessful();
        $this->assertGridResponse();
    }

    public function testTransactionFilterFields(): void
    {
        $transaction = $this->getTestTransaction();
        self::$client->request('GET', '/transactions/list', [
            'filters' => [
                'search' => $transaction->getId(),
                'status' => [$transaction->getState()->value],
                'method' => [$transaction->getProvider()],
                'merchant' => [$transaction->getMerchant()?->getId()],
            ],
        ]);
        $this->assertGridResponse();

        $initiatorDetails = $transaction->getInitiatorDetails();

        $this->assertJsonResponseEquals([
            'data' => [
                [
                    'id' => $transaction->getId(),
                    'amount' => $transaction->getAmount(),
                    'provider' => $transaction->getProvider(),
                    'currency' => $transaction->getCurrency(),
                    'status' => $transaction->getState()->value,
                    'fxOrder' => null,
                    'tradingName' => $transaction->getMerchant()?->getTradingName(),
                    'createdAt' => $transaction->getCreatedAt()?->format('c'),
                    'updatedAt' => $transaction->getUpdatedAt()?->format('c'),
                    'confirmed' => $transaction->getConfirmedStateDate()?->format('c'),
                    'companyId' => $transaction->getMerchant()?->getId(),
                    'payCurrency' => $transaction->getPayCurrency(),
                    'payAmount' => $transaction->getPayAmount(),
                    'created' => $transaction->getCreatedAt()?->format('c'),
                    'deleted' => $transaction->isDeleted(),
                    'processingDate' => $transaction->getCreatedAt()?->format('c'),
                    'rate' => $transaction->getRateFee(),
                    'costStructureCurrency' => $transaction->getCostStructure()?->getCurrency(),
                    'commission' => $transaction->getCostStructure()?->getFee(),
                    'netAmount' => $transaction->getCostStructure()?->getPayable(),
                    'initiator' => "{$initiatorDetails?->getContactName()} {$initiatorDetails?->getContactEmail()}",
                ],
            ],
            'draw' => 0,
            'pagination' => [
                'perPage' => 25,
                'nextPage' => false,
                'previousPage' => false,
                'type' => 'simple',
            ],
        ]);
    }

    public function testTheNewStatusMustBeDifferent(): void
    {
        $testTransaction = $this->getTestTransaction();
        self::$client->request('GET', sprintf('transactions/%s/state', $testTransaction->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Transaction State');
        self::$client->submitForm('Submit', [
           'transaction_status' => [
               'state' => 'CONFIRMED',
           ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('.invalid-feedback', 'New status must be different from the current one');
    }

    public function testTransactionStatusPushToTheMessageQueue(): void
    {
        $testTransaction = $this->getTestTransaction();
        self::$client->request('GET', sprintf('transactions/%s/state', $testTransaction->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Transaction State');

        $bus = $this->createMock(Bus::class);
        $bus->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(static function (Message $message) use ($testTransaction) {
                self::assertSame(QueueName::TRANSACTION, $message->getQueueName());
                self::assertSame($testTransaction->getId(), $message->getBody()['transactionId']);
                self::assertSame(Status::REFUND_REQUESTED->value, $message->getBody()['state']);

                return true;
            }));

        ServiceMock::swap(self::$client->getContainer()->get(Bus::class), $bus);

        self::$client->submitForm('Submit', [
            'transaction_status' => [
                'state' => 'REFUND_REQUESTED',
            ],
        ]);
    }

    /**
     * @group smoke
     */
    public function testTransactionDetailPage(): void
    {
        $testTransaction = $this->getTestTransaction();
        self::$client->request('GET', sprintf('/transactions/%s', $testTransaction->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Transaction detail');
    }

    /**
     * @group smoke
     */
    public function testTransactionStateChangePage(): void
    {
        $testTransaction = $this->getTestTransaction();
        self::$client->request('GET', sprintf('transactions/%s/state', $testTransaction->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Transaction State');
    }

    /**
     * @group smoke
     */
    public function testCreateManualFxPage(): void
    {
        $testTransaction = $this->getTestTransaction();
        self::$client->request('GET', sprintf('transactions/%s/manual-fx-trade', $testTransaction->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Create FX');
    }
}
