<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Application\DataGrid\DataGridHandlerInterface;
use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\IndexAction;
use App\Http\Controller\CRUD\ListAction;
use App\Infrastructure\DataGrid\DynamicMongoDataGrid;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ListActionTest extends BaseCrudTestCase
{
    private DataGridHandlerInterface&MockObject $dataGridHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataGridHandler = $this->createMock(DataGridHandlerInterface::class);
    }

    public function testItShowsCommonIndexPage(): void
    {
        $this->expectAuthCheck('test_document.view');

        $this->expectTemplateExists('test_document/index.html.twig', false);
        $this->expectRender('common/crud/index.html.twig', 'index page', [
            'component' => 'test-document-list',
            'props' => [],
            'title' => 'test_document.title.list',
        ]);

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'index'));
        self::assertSame('index page', $controller->index($this->createStub(Request::class))->getContent());
    }

    public function testItShowsCustomIndexView(): void
    {
        $this->expectAuthCheck('test_document.view');

        $this->expectTemplateExists('test_document/index.html.twig', true);
        $this->expectRender('test_document/index.html.twig', 'index page', [
            'component' => 'test-document-list',
            'props' => [],
            'title' => 'test_document.title.list',
        ]);

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'index'));
        self::assertSame('index page', $controller->index($this->createStub(Request::class))->getContent());
    }

    public function testItDeniesAccessToIndex(): void
    {
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'index'));

        $this->expectAuthCheck('test_document.view', false);
        $this->expectException(AccessDeniedException::class);
        $controller->index($this->createStub(Request::class));
    }

    public function testItDeniesAccessToList(): void
    {
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'list'));

        $this->expectAuthCheck('test_document.view', false);
        $this->expectException(AccessDeniedException::class);
        $controller->list($this->createStub(Request::class));
    }

    public function testItListsDocumentsUsingDynamicDataGrid(): void
    {
        $container = $this->getContainer();
        $container->set(DataGridHandlerInterface::class, $this->dataGridHandler);

        $this->dataGridHandler->expects(self::once())->method('__invoke')
            ->with(
                self::isInstanceOf(Request::class),
                self::logicalAnd(
                    self::isInstanceOf(DynamicMongoDataGrid::class),
                    self::callback(function (DynamicMongoDataGrid $grid) {
                        $reflectionProperty = (new ReflectionClass($grid))->getProperty('documentClass');
                        $reflectionProperty->setAccessible(true);

                        self::assertSame(TestDocument::class, $reflectionProperty->getValue($grid));

                        return true;
                    })
                )
            )->willReturn(new Response('list output'));

        $this->expectAuthCheck('test_document.view');
        $controller = $this->getCrudController($container);

        self::assertTrue(method_exists($controller, 'list'));
        self::assertSame('list output', $controller->list($this->createStub(Request::class))->getContent());
    }

    public function testItHasDefaultIndexComponent(): void
    {
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'getIndexComponent'));
        self::assertSame('test-document-list', $controller->getIndexComponent());
    }

    public function testItHasDefaultIndexTitle(): void
    {
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'getIndexTitle'));
        self::assertSame('test_document.title.list', $controller->getIndexTitle());
    }

    protected function getCrudController(Container $container): BasicCrudController
    {
        /** @var BasicCrudController<TestDocument> $controller */
        $controller = new class() extends BasicCrudController {
            use IndexAction;
            use ListAction;

            protected static function getItemClass(): string
            {
                return TestDocument::class;
            }
        };

        $controller->setContainer($container);

        return $controller;
    }
}
