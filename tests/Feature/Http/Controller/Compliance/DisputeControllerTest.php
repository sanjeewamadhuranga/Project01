<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Compliance;

use App\Application\Queue\Bus;
use App\Application\Queue\Message;
use App\Application\Queue\QueueName;
use App\Domain\Compliance\DisputeState;
use App\Domain\Document\Compliance\Dispute;
use App\Domain\Document\Transaction\Transaction;
use App\Domain\Transaction\Status;
use App\Domain\Transaction\TransactionCreateRequest;
use App\Infrastructure\Service\Client;
use App\Tests\Feature\BaseTestCase;
use Happyr\ServiceMocking\ServiceMock;
use Symfony\Component\HttpFoundation\Response;

class DisputeControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();
    }

    /**
     * @group smoke
     */
    public function testItShowsDisputeList(): void
    {
        self::$client->request('GET', '/compliance/disputes');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('dispute-list');
    }

    public function testWeCanNotDeleteDispute(): void
    {
        $dispute = $this->getTestProcessingDispute();
        self::$client->request('DELETE', sprintf('/compliance/disputes/%s/delete', $dispute->getId()));

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testListDisputes(): void
    {
        self::$client->request('GET', '/compliance/disputes');
        self::assertResponseIsSuccessful();
        self::$client->request('GET', '/compliance/disputes/list');
        self::assertResponseIsSuccessful();
        $this->assertGridResponse();
    }

    public function testCreatingDisputeForAuthorizedTransaction(): void
    {
        $transaction = $this->getTestAuthorizedTransaction();
        self::$client->request('GET', sprintf('/compliance/disputes/create/transaction/%s', $transaction->getId()));
        self::$client->submitForm('Submit', [
            'dispute' => [
                'comments' => 'test abc',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDisputeCanBeCreatedForATransaction(): void
    {
        $transaction = $this->getTestTransaction();
        self::$client->request('GET', sprintf('/compliance/disputes/create/transaction/%s', $transaction->getId()));
        self::assertResponseIsSuccessful();
        self::assertNotNull($transaction->getId());
        self::assertInputValueSame('dispute[transaction]', $transaction->getId());

        self::$client->submitForm('Submit', [
            'dispute' => [
                'comments' => 'test abc',
            ],
        ]);

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertRouteSame('compliance_dispute_show');
        self::assertSelectorTextContains('h5', 'Dispute Detail');
    }

    public function testDisputeCanBeClosed(): void
    {
        $this->markTouchesDb();
        $dispute = $this->getTestProcessingDispute();
        self::assertNotNull($dispute->getState());
        self::assertNotEquals(DisputeState::CLOSED, $dispute->getState());
        self::$client->request('GET ', sprintf('/compliance/disputes/%s', $dispute->getId()));
        self::$client->submitForm('Close Dispute');
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        $this->getDocumentManager()->clear();
        $dispute = $this->refresh($dispute);
        self::assertNotNull($dispute->getState());
        self::assertSame(DisputeState::CLOSED, $dispute->getState());
    }

    public function testItDoesNotAllowToCreateDisputeIfItAlreadyExistsForTheTransaction(): void
    {
        $transaction = $this->getTestProcessingDispute()->getTransaction();
        self::$client->request('GET', sprintf('/compliance/disputes/create/transaction/%s', $transaction?->getId()));

        self::assertSelectorTextContains('div.alert', 'Transaction is already a subject or a reconfirmation of another dispute');
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::$client->submitForm('Submit', [
            'dispute' => [
                'comments' => 'test abc',
            ],
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('div.alert', 'Transaction is already a subject or a reconfirmation of another dispute');
    }

    public function testItAllowsToCreateReconfirmationTransactionForDisputeWithAChargeback(): void
    {
        $dispute = $this->getTestProcessingDispute();
        $crawler = self::$client->request('GET', sprintf('/compliance/disputes/%s', $dispute->getId()));
        self::assertSelectorTextContains('a.btn-falcon-default', 'Create reconfirmation');
        $link = $crawler->selectLink('Create reconfirmation')->link();
        self::$client->click($link);

        ServiceMock::swap(self::$client->getContainer()->get(Client::class), $this->getClientWithNotifyExpectation());

        self::$client->submitForm('Submit', [
            'transaction_reconfirmation' => [
                'amount' => 7000,
                'adjustmentPurpose' => 'test',
            ],
        ]);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertRouteSame('compliance_dispute_index');
        $dispute = $this->refresh($dispute);

        /** @var Transaction $reconfirmation */
        $reconfirmation = $dispute->getReconfirmation();
        $reconfirmation = $this->refresh($reconfirmation);

        self::assertNotNull($dispute->getReconfirmation());
        self::assertSame(DisputeState::CLOSED, $dispute->getState());
        self::assertSame(0, $dispute->getTransaction()?->getAvailableBalance());
        self::assertNotTrue($reconfirmation->getIsDispute());
        self::assertSame(Status::CONFIRMED, $reconfirmation->getState());
        self::assertNotSame($dispute->getTransaction()->getId(), $reconfirmation->getId());
    }

    public function testItAllowsToCreateAChargeback(): void
    {
        $dispute = $this->getChargeableDispute();
        $crawler = self::$client->request('GET', sprintf('/compliance/disputes/%s', $dispute->getId()));
        self::assertSelectorTextContains('a.btn-falcon-default', 'Create chargeback transaction');
        $link = $crawler->selectLink('Create chargeback transaction')->link();
        self::$client->click($link);

        ServiceMock::swap(self::$client->getContainer()->get(Client::class), $this->getClientWithNotifyExpectation());

        $bus = $this->createMock(Bus::class);
        $bus->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(static function (Message $message) use ($dispute) {
                self::assertSame(QueueName::TRIGGER, $message->getQueueName());
                self::assertNotNull($dispute->getTransaction());
                self::assertSame($dispute->getTransaction()->getId(), $message->getBody()['transactionId']);
                self::assertSame($dispute->getReason(), $message->getBody()['reason']);
                self::assertSame($dispute->getDisputeFee(), $message->getBody()['disputeFee']);
                self::assertSame($dispute->getCompany()->getId(), $message->getBody()['companyId']);
                self::assertSame('DISPUTE_CHARGEBACK', $message->getBody()['trigger']);

                return true;
            }));
        ServiceMock::swap(self::$client->getContainer()->get(Bus::class), $bus);

        self::$client->submitForm('Submit', [
            'transaction_chargeback' => [
                'amount' => 200,
                'adjustmentPurpose' => 'test',
            ],
        ]);

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        $dispute = $this->refresh($dispute);
        /** @var Transaction $chargeback */
        $chargeback = $dispute->getChargeback();
        $chargeback = $this->refresh($chargeback);
        self::assertSame(0, $dispute->getTransaction()?->getAvailableBalance());
        self::assertSame(DisputeState::PROCESSING, $dispute->getState());
        self::assertTrue($chargeback->getIsDispute());
        self::assertSame(Status::REFUNDED, $chargeback->getState());
        self::assertNotSame($dispute->getTransaction()->getId(), $chargeback->getId());
    }

    protected function getTestProcessingDispute(): Dispute
    {
        return self::$fixtures['dispute_processing']; // @phpstan-ignore-line
    }

    protected function getTestAuthorizedTransaction(): Transaction
    {
        return self::$fixtures['transaction_authorized_test_company']; // @phpstan-ignore-line
    }

    protected function getChargeableDispute(): Dispute
    {
        return self::$fixtures['dispute_new']; // @phpstan-ignore-line
    }

    protected function getClosedDispute(): Dispute
    {
        return self::$fixtures['dispute_closed_reconfirmed']; // @phpstan-ignore-line
    }

    private function getClientWithNotifyExpectation(): Client
    {
        $Client = $this->createMock(Client::class);
        $Client->expects(self::once())
            ->method('notify')
            ->willReturnCallback(function (TransactionCreateRequest $createRequest) {
                $transaction = new Transaction();
                $transaction->setAmount((int) $createRequest->amount);
                $transaction->setCurrency((string) $createRequest->currency);
                $transaction->setState($createRequest->state ?? Status::CONFIRMED);
                $this->getDocumentManager()->persist($transaction);
                $this->getDocumentManager()->flush();

                return ['id' => $transaction->getId()];
            });

        return $Client;
    }
}
