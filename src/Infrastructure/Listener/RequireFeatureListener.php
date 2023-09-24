<?php

declare(strict_types=1);

namespace App\Infrastructure\Listener;

use App\Domain\Settings\Features;
use App\Infrastructure\Http\Attribute\RequireFeature;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener]
class RequireFeatureListener
{
    public function __construct(private readonly Features $features)
    {
    }

    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $configurations = $event->getRequest()->attributes->get('_require_feature');

        if (!is_array($configurations)) {
            return;
        }

        foreach ($configurations as $configuration) {
            if (!$configuration instanceof RequireFeature) {
                continue;
            }

            if (!$this->features->isFeatureEnabled($configuration->name)) {
                throw new NotFoundHttpException(sprintf('Feature "%s" is not enabled.', $configuration->name));
            }
        }
    }
}
