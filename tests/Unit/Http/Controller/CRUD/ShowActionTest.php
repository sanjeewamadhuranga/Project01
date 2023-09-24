<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\ShowAction;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ShowActionTest extends BaseCrudTestCase
{
    public function testItShowsItemDetails(): void
    {
        $this->expectAuthCheck('test_document.view');

        $document = new TestDocument();
        $this->expectFindDocument('619b8b8e2ca05e6f883e8e8e', $document);
        $this->expectRender('test_document/show.html.twig', 'details page', ['item' => $document]);

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'show'));
        self::assertSame('details page', $controller->show('619b8b8e2ca05e6f883e8e8e')->getContent());
    }

    public function testItDeniesAccessToShow(): void
    {
        $controller = $this->getCrudController($this->getContainer());

        $document = new TestDocument();
        $this->expectFindDocument('619b8bcdb13c3391fb7707ed', $document);

        self::assertTrue(method_exists($controller, 'show'));

        $this->expectAuthCheck('test_document.view', false);
        $this->expectException(AccessDeniedException::class);
        $controller->show('619b8bcdb13c3391fb7707ed');
    }

    protected function getCrudController(Container $container): BasicCrudController
    {
        /** @var BasicCrudController<TestDocument> $controller */
        $controller = new class() extends BasicCrudController {
            use ShowAction;

            protected static function getItemClass(): string
            {
                return TestDocument::class;
            }
        };

        $controller->setContainer($container);

        return $controller;
    }
}
