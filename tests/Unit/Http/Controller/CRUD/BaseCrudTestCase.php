<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Interfaces\Activeable;
use App\Domain\Document\Traits\HasActive;
use App\Http\Controller\BasicCrudController;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

abstract class BaseCrudTestCase extends UnitTestCase
{
    protected readonly AuthorizationCheckerInterface&MockObject $authorizationChecker;

    protected readonly Environment&MockObject $twig;

    protected readonly DocumentManager&MockObject $dm;

    protected readonly TranslatorInterface&MockObject $translator;

    protected readonly LoaderInterface&MockObject $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->dm = $this->createMock(DocumentManager::class);
        $this->loader = $this->createMock(LoaderInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('trans')->willReturnCallback(static fn (string $message) => $message);
    }

    protected function expectAuthCheck(string $permission, bool $granted = true): void
    {
        $this->authorizationChecker->expects(self::atLeastOnce())
            ->method('isGranted')
            ->with($permission)
            ->willReturn($granted);
    }

    protected function expectFindDocument(string $id, ?TestDocument $document): void
    {
        $this->dm->expects(self::once())->method('find')->with(TestDocument::class, $id)->willReturn($document);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function expectRender(string $view, string $expectedResult, array $parameters = []): void
    {
        $this->twig->expects(self::once())->method('render')->with($view, $parameters)->willReturn($expectedResult);
    }

    protected function expectTemplateExists(string $view, bool $expectation): void
    {
        $this->twig->expects(self::once())->method('getLoader')->willReturn($this->loader);
        $this->loader->expects(self::once())->method('exists')->with($view)->willReturn($expectation);
    }

    /**
     * @return BasicCrudController<TestDocument>
     */
    abstract protected function getCrudController(Container $container): BasicCrudController;

    protected function getContainer(): Container
    {
        $managerRegistry = $this->createStub(ManagerRegistry::class);
        $managerRegistry->method('getManager')->willReturn($this->dm);

        $container = new Container();
        $container->set('security.authorization_checker', $this->authorizationChecker);
        $container->set('twig', $this->twig);
        $container->set('doctrine_mongodb', $managerRegistry);
        $container->set('translator', $this->translator);

        return $container;
    }
}

class TestDocument extends BaseDocument implements Activeable
{
    use HasActive;
}
