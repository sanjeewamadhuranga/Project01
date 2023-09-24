<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\Company\AccountManagerNote;
use App\Tests\Feature\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class NoteControllerTest extends BaseTestCase
{
    public function testItCreatesNoteForValidRequest(): void
    {
        $this->markTouchesDb();
        $testCompany = $this->getTestCompany();
        self::$client->jsonRequest('POST', sprintf('/merchants/%s/note/create', $testCompany->getId()), [
            'note' => 'test note',
            'followUpAction' => 'my action',
            'completed' => false,
            'title' => 'test title',
            'tag' => 'test tag',
        ]);

        self::assertResponseIsSuccessful();
        $testCompany = $this->refresh($testCompany);
        self::assertNotEmpty($testCompany->getNotes()->filter(fn (AccountManagerNote $note) => 'test note' === $note->getNote()));
    }

    public function testItFailsWhenMessageIsTooLong(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->jsonRequest('POST', sprintf('/merchants/%s/note/create', $testCompany->getId()), [
            'note' => str_repeat('1', 120),
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSame(
            'This value is too long. It should have 100 characters or less.',
            $this->getJsonResponse()['violations'][0]['title'] ?? null
        );
    }

    public function testItGetCompanyNote(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/note/list', $testCompany->getId()));

        $plans = $this->getJsonResponse();
        self::assertCount(1, $plans);
    }

    public function testItMarksNotesCompleted(): void
    {
        $this->markTouchesDb();
        /** @var AccountManagerNote $note */
        foreach ($this->getTestCompany()->getNotes() as $note) {
            self::$client->request('POST', sprintf(
                '/merchants/%s/note/%s/complete',
                $this->getTestCompany()->getId(),
                $note->getId()
            ));
            self::assertTrue($this->refresh($note)->isCompleted());
        }
    }

    public function testItMarksNoteDeleted(): void
    {
        $this->markTouchesDb();
        /** @var AccountManagerNote $note */
        foreach ($this->getTestCompany()->getNotes() as $note) {
            self::$client->request('DELETE', sprintf(
                '/merchants/%s/note/%s',
                $this->getTestCompany()->getId(),
                $note->getId()
            ));
            self::assertTrue($this->refresh($note)->isDeleted());
        }
    }
}
