<?php

declare(strict_types=1);

namespace App\Domain\DataGrid\Filters;

use App\Application\DataGrid\Filters\Filters;
use Symfony\Component\Validator\Constraints as Assert;

class PregeneratedReportFilter implements Filters
{
    #[Assert\NotBlank]
    private ?string $reportId = null;

    public function getReportId(): ?string
    {
        return $this->reportId;
    }

    public function setReportId(?string $reportId): void
    {
        $this->reportId = $reportId;
    }
}
