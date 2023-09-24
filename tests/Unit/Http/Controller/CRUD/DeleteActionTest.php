<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\DeleteAction;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeleteActionTest extends BaseCrudTestCase
{
    public function testItDeletesItem(): void
    {
        $this->expectAuthCheck('test_document.delete');
        $controller = $this->getCrudController($this->getContainer());
        $document = new TestDocument();
        $this->expectFindDocument('619b8758305118fd64f0ab18', $document);

        $this->dm->expects(self::once())->method('persist')->with($document);
        $this->dm->expects(self::once())->method('flush');

        self::assertTrue(method_exists($controller, 'delete'));
        self::assertSame(204, $controller->delete('619b8758305118fd64f0ab18')->getStatusCode());
        self::assertTrue($document->isDeleted());
    }

    public function testItThrows404IfObjectIsNotFound(): void
    {
        $controller = $this->getCrudController($this->getContainer());
        $this->expectFindDocument('619b8758305118fd64f0ab18', null);

        self::assertTrue(method_exists($controller, 'delete'));
        $this->expectException(NotFoundHttpException::class);
        $controller->delete('619b8758305118fd64f0ab18');
    }

    public function testItDeniesAccess(): void
    {
        $this->expectAuthCheck('test_document.delete', false);
        $this->expectFindDocument('619b8758305118fd64f0ab18', new TestDocument());
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'delete'));

        $this->expectException(AccessDeniedException::class);
        $controller->delete('619b8758305118fd64f0ab18');
    }

    protected function getCrudController(Container $container): BasicCrudController
    {
        /** @var BasicCrudController<TestDocument> $controller */
        $controller = new class() extends BasicCrudController {
            use DeleteAction;

            protected static function getItemClass(): string
            {
                return TestDocument::class;
            }
        };

        $controller->setContainer($container);

        return $controller;
    }
}
