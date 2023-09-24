<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Liip\MonitorBundle\Helper\ArrayReporter;
use Liip\MonitorBundle\Runner;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/healthcheck', name: 'healthcheck_')]
class HealthCheckController extends BaseController
{
    #[Route('/', name: 'index')]
    public function index(Runner $publicRunner): Response
    {
        $reporter = new ArrayReporter();
        $publicRunner->addReporter($reporter);
        $publicRunner->run();

        return $this->json(
            $reporter,
            ArrayReporter::STATUS_OK === $reporter->getGlobalStatus() ? Response::HTTP_OK : Response::HTTP_FAILED_DEPENDENCY
        );
    }

    #[Route('/ping', name: 'ping')]
    public function ping(): Response
    {
        return $this->json(['status' => 'OK']);
    }
}
