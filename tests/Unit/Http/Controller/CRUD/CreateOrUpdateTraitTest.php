<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Http\Controller\BasicCrudController;
use App\Http\Controller\CRUD\CreateAction;
use App\Http\Controller\CRUD\UpdateAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreateOrUpdateTraitTest extends BaseCrudTestCase
{
    private FormFactoryInterface&Stub $formFactory;

    private FormInterface&MockObject $form;

    private RouterInterface&MockObject $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = $this->createMock(FormInterface::class);
        $this->form->method('handleRequest')->willReturnSelf();
        $this->form->method('createView')->willReturn($this->createStub(FormView::class));
        $this->formFactory = $this->createStub(FormFactoryInterface::class);
        $this->formFactory->method('create')->willReturn($this->form);
        $this->router = $this->createMock(RouterInterface::class);
    }

    public function testItRendersUpdateForm(): void
    {
        $this->expectAuthCheck('test_document.edit');

        $document = new TestDocument();
        $this->expectFindDocument('619b8d981bf8052772c3c326', $document);
        $this->form->method('isSubmitted')->willReturn(false);
        $this->expectTemplateExists('test_document/form.html.twig', true);
        $this->expectRender('test_document/form.html.twig', 'update form page', [
            'item' => $document,
            'form' => $this->form->createView(),
            'isCreate' => false,
            'title' => 'test_document.title.update',
            'backUrl' => '',
            'subTitle' => 'test_document.title.subtitle',
        ]);

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'update'));
        self::assertSame(
            'update form page',
            $controller->update($this->createStub(Request::class), '619b8d981bf8052772c3c326')->getContent()
        );
    }

    public function testItHandlesUpdateFormIfItIsValidAndRedirectsToList(): void
    {
        $this->expectAuthCheck('test_document.edit');
        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(true);
        $document = new TestDocument();
        $this->expectFindDocument('619b8d981bf8052772c3c326', $document);
        $this->dm->expects(self::once())->method('persist')->with($document);
        $this->dm->expects(self::once())->method('flush');

        $this->router->expects(self::once())->method('generate')->with('test_document_index')->willReturn('index_url');

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'update'));
        $response = $controller->update($this->createStub(Request::class), '619b8d981bf8052772c3c326');
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('index_url', $response->getTargetUrl());
        self::assertTrue(property_exists($controller, 'flashes'));
        self::assertSame([['type' => 'success', 'message' => 'successfullyUpdated']], $controller->flashes);
    }

    public function testItDoesNotSaveChangesIfFormIsInvalid(): void
    {
        $this->expectAuthCheck('test_document.edit');
        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(false);

        $document = new TestDocument();

        $this->expectTemplateExists('test_document/form.html.twig', true);
        $this->expectRender('test_document/form.html.twig', 'update form page', [
            'item' => $document,
            'form' => $this->form->createView(),
            'isCreate' => false,
            'title' => 'test_document.title.update',
            'backUrl' => '',
            'subTitle' => 'test_document.title.subtitle',
        ]);
        $this->expectFindDocument('619b8d981bf8052772c3c326', $document);
        $this->dm->expects(self::never())->method('persist');
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'update'));
        $response = $controller->update($this->createStub(Request::class), '619b8d981bf8052772c3c326');
        self::assertInstanceOf(Response::class, $response);
        self::assertSame('update form page', $response->getContent());
    }

    public function testItRendersCreateForm(): void
    {
        $this->expectAuthCheck('test_document.create');

        $document = new TestDocument();
        $this->form->method('isSubmitted')->willReturn(false);
        $this->expectTemplateExists('test_document/form.html.twig', true);
        $this->expectRender('test_document/form.html.twig', 'create form page', [
            'item' => $document,
            'form' => $this->form->createView(),
            'isCreate' => true,
            'title' => 'test_document.title.create',
            'backUrl' => '',
            'subTitle' => 'test_document.title.subtitle',
        ]);

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'create'));
        self::assertSame(
            'create form page',
            $controller->create($this->createStub(Request::class))->getContent()
        );
    }

    public function testItCreatesNewItemIfFormIsValidAndRedirectsToList(): void
    {
        $this->expectAuthCheck('test_document.create');
        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(true);
        $document = new TestDocument();
        $this->dm->expects(self::once())->method('persist')->with($document);
        $this->dm->expects(self::once())->method('flush');

        $this->router->expects(self::once())->method('generate')->with('test_document_index')->willReturn('index_url');

        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'create'));
        $response = $controller->create($this->createStub(Request::class));
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('index_url', $response->getTargetUrl());
        self::assertTrue(property_exists($controller, 'flashes'));
        self::assertSame([['type' => 'success', 'message' => 'successfullyCreated']], $controller->flashes);
    }

    public function testItDoesNotCreateNewItemIfFormIsInvalid(): void
    {
        $this->expectAuthCheck('test_document.create');
        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(false);

        $this->expectTemplateExists('test_document/form.html.twig', true);
        $this->expectRender('test_document/form.html.twig', 'create form page', [
            'item' => new TestDocument(),
            'form' => $this->form->createView(),
            'isCreate' => true,
            'title' => 'test_document.title.create',
            'backUrl' => '',
            'subTitle' => 'test_document.title.subtitle',
        ]);
        $this->dm->expects(self::never())->method('persist');
        $controller = $this->getCrudController($this->getContainer());

        self::assertTrue(method_exists($controller, 'create'));
        $response = $controller->create($this->createStub(Request::class));
        self::assertSame('create form page', $response->getContent());
    }

    public function testItDeniesAccessToEdit(): void
    {
        $controller = $this->getCrudController($this->getContainer());

        $document = new TestDocument();
        $this->expectFindDocument('619b8bcdb13c3391fb7707ed', $document);

        self::assertTrue(method_exists($controller, 'update'));

        $this->expectAuthCheck('test_document.edit', false);
        $this->expectException(AccessDeniedException::class);
        $controller->update($this->createStub(Request::class), '619b8bcdb13c3391fb7707ed');
    }

    public function testItDeniesAccessToCreate(): void
    {
        $controller = $this->getCrudController($this->getContainer());
        self::assertTrue(method_exists($controller, 'create'));

        $this->expectAuthCheck('test_document.create', false);
        $this->expectException(AccessDeniedException::class);
        $controller->create($this->createStub(Request::class));
    }

    protected function getContainer(): Container
    {
        $container = parent::getContainer();
        $container->set('form.factory', $this->formFactory);
        $container->set('router', $this->router);

        return $container;
    }

    protected function getCrudController(Container $container): BasicCrudController
    {
        /** @var BasicCrudController<TestDocument> $controller */
        $controller = new class() extends BasicCrudController {
            use UpdateAction;
            use CreateAction;

            /** @var array<array{type: string, message: string}> */
            public array $flashes = [];

            protected static function getItemClass(): string
            {
                return TestDocument::class;
            }

            protected function addFlash(string $type, mixed $message): void
            {
                $this->flashes[] = ['type' => $type, 'message' => (string) $message];
            }
        };

        $controller->setContainer($container);

        return $controller;
    }
}
