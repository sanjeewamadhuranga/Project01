<?php

declare(strict_types=1);

namespace App\Http\Model\Request;

use App\Domain\Document\Flow\Section;
use Symfony\Component\Validator\Constraints as Assert;

class FlowRequest
{
    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotBlank]
    public string $key = '';

    public bool $default = false;

    /** @var string[] */
    public array $locales = [];

    /**
     * @var Section[]
     */
    #[Assert\NotBlank]
    #[Assert\Valid]
    public array $sections = [];
}
