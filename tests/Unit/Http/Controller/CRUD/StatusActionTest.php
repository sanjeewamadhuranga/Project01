<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\StatusAction;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StatusActionTest extends BaseCrudTestCase
{
    /**
     * @testWith [true]
     *           [false]
     */
    public function testItChangeStatus(bool $newStatus): void
    {
        $this->expectAuthCheck('test_document.edit');
        $controller = $this->getCrudController($this->getContainer());
        $document = new TestDocument();
        $document->setActive(!$newStatus);
        $this->expectFindDocument('619b8d981bf8052772c3c326', $document);

        $this->dm->expects(self::once())->method('persist')->with($document);
        $this->dm->expects(self::once())->method('flush');

        self::assertTrue(method_exists($controller, 'changeStatus'));
        self::assertSame(204, $controller->changeStatus('619b8d981bf8052772c3c326', $newStatus)->getStatusCode());
        self::assertSame($newStatus, $document->isActive());
    }

    /**
     * @testWith [true]
     *           [false]
     */
    public function testItDoesNotChangeStatusIfItIsSame(bool $newStatus): void
    {
        $this->expectAuthCheck('test_document.edit');
        $controller = $this->getCrudController($this->getContainer());
        $document = new TestDocument();
        $document->setActive($newStatus);
        $this->expectFindDocument('619b8d981bf8052772c3c326', $document);

        $this->dm->expects(self::once())->method('persist')->with($document);
        $this->dm->expects(self::once())->method('flush');

        self::assertTrue(method_exists($controller, 'changeStatus'));
        self::assertSame(204, $controller->changeStatus('619b8d981bf8052772c3c326', $newStatus)->getStatusCode());
        self::assertSame($newStatus, $document->isActive());
    }

    public function testItThrows404IfObjectIsNotFound(): void
    {
        $controller = $this->getCrudController($this->getContainer());
        $this->expectFindDocument('619b8d981bf8052772c3c326', null);

        self::assertTrue(method_exists($controller, 'changeStatus'));
        $this->expectException(NotFoundHttpException::class);
        $controller->changeStatus('619b8d981bf8052772c3c326', true);
    }

    public function testItDeniesAccess(): void
    {
        $this->expectAuthCheck('test_document.edit', false);
        $this->expectFindDocument('619b8d981bf8052772c3c326', new TestDocument());
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'changeStatus'));

        $this->expectException(AccessDeniedException::class);
        $controller->changeStatus('619b8d981bf8052772c3c326', true);
    }

    protected function getCrudController(Container $container): BasicCrudController
    {
        /** @var BasicCrudController<TestDocument> $controller */
        $controller = new class() extends BasicCrudController {
            use StatusAction;

            protected static function getItemClass(): string
            {
                return TestDocument::class;
            }
        };

        $controller->setContainer($container);

        return $controller;
    }
}
