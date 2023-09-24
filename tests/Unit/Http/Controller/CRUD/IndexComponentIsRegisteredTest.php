<?php

declare(strict_types=1);

namespace App\Tests\Unit\Http\Controller\CRUD;

use App\Tests\Unit\UnitTestCase;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class IndexComponentIsRegisteredTest extends UnitTestCase
{
    public function testThatComponentNameExistsInWebcomponents(): void
    {
        $componentsPath = __DIR__.'/../../../../../assets/components/webcomponents.tsx';
        self::assertFileExists($componentsPath);

        $componentsContent = file_get_contents($componentsPath);
        self::assertIsString($componentsContent);

        $files = (new Finder())->in(__DIR__.'/../../../../../src/Http/Controller')->files();

        foreach ($files as $controllerClass => $meta) {
            $clean = str_replace(__DIR__.'/../../../../../src/', '', $controllerClass);
            $phpless = str_replace('.php', '', $clean);
            $controllerClass = 'App\\'.str_replace('/', '\\', $phpless);
            // skip if not controller
            if (!str_ends_with($controllerClass, 'Controller')) {
                continue;
            }

            // skip if abstract @phpstan-ignore-next-line
            if ((new ReflectionClass($controllerClass))->isAbstract()) {
                continue;
            }

            // @phpstan-ignore-next-line
            $controller = $this->createPartialMock($controllerClass, []);

            // skip if there is no getIndexComponent
            if (!method_exists($controller, 'getIndexComponent')) {
                continue;
            }

            $componentName = '"'.$controller->getIndexComponent().'"';

            if (!str_contains($componentsContent, $componentName)) {
                self::fail(sprintf('%s is not registered in webcomponents.tsx for %s', $componentName, basename($controllerClass)));
            }
        }
    }
}
