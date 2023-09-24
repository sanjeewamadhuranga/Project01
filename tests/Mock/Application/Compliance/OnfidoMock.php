<?php

declare(strict_types=1);

namespace App\Tests\Mock\Application\Compliance;

use App\Application\Compliance\OnfidoInterface;
use App\Application\Compliance\ResourceType;
use App\Domain\Company\OnfidoResourcesResponse;
use App\Domain\Document\Company\User;
use SplFileObject;

class OnfidoMock implements OnfidoInterface
{
    public function createApplicant(User $user): string
    {
        return 'test-applicant-id';
    }

    public function getKycResources(User $user): ?OnfidoResourcesResponse
    {
        return null;
    }

    public function getDownloadFile(ResourceType $type, string $resourceId): ?SplFileObject
    {
        return null;
    }
}
