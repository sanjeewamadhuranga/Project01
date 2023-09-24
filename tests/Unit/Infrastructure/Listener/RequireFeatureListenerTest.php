<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Listener;

use App\Domain\Settings\Features;
use App\Infrastructure\Http\Attribute\RequireFeature;
use App\Infrastructure\Listener\RequireFeatureListener;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;

class RequireFeatureListenerTest extends UnitTestCase
{
    public function testItThrowsExceptionWhenFeatureIsRequiredAndNotEnabled(): void
    {
        $featureName = 'some_feature';
        $event = $this->getControllerArgumentsEvent([new RequireFeature($featureName)]);

        $features = $this->createMock(Features::class);
        $features->expects(self::once())->method('isFeatureEnabled')->with($featureName)->willReturn(false);

        $subscriber = new RequireFeatureListener($features);
        $this->expectException(NotFoundHttpException::class);
        $this->expectErrorMessage(sprintf('Feature "%s" is not enabled.', $featureName));
        $subscriber->__invoke($event);
    }

    public function testItDoNotThrowExceptionWhenFeatureIsRequiredAndIsEnabled(): void
    {
        $featureName = 'some_feature';
        $event = $this->getControllerArgumentsEvent([new RequireFeature($featureName)]);

        $features = $this->createMock(Features::class);
        $features->expects(self::once())->method('isFeatureEnabled')->with($featureName)->willReturn(true);

        $subscriber = new RequireFeatureListener($features);
        $subscriber->__invoke($event);
    }

    public function testItDoNotThrowsExceptionWhenFeatureIsNotRequiredAndIsNotEnabled(): void
    {
        $features = $this->createMock(Features::class);
        $features->expects(self::never())->method('isFeatureEnabled');

        $subscriber = new RequireFeatureListener($features);
        $subscriber->__invoke($this->getControllerArgumentsEvent());
    }

    /**
     * @param array<RequireFeature> $requiredFeatures
     */
    private function getControllerArgumentsEvent(array $requiredFeatures = []): ControllerArgumentsEvent
    {
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->expects(self::once())->method('get')->with('_require_feature')->willReturn($requiredFeatures);

        $request = $this->createMock(Request::class);
        $request->attributes = $parameterBag;

        return new ControllerArgumentsEvent(
            $this->createMock(HttpKernel::class),
            static function (): void {},
            [],
            $request,
            null
        );
    }
}
