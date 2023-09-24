<?php

declare(strict_types=1);

namespace App\Infrastructure\DataCollector;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\Filters\GridRequest;
use App\Infrastructure\DataGrid\Debug\DataGridCall;
use App\Infrastructure\DataGrid\Debug\TraceableDataGridHandler;
use Symfony\Bundle\FrameworkBundle\DataCollector\TemplateAwareDataCollectorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\StubCaster;
use Symfony\Component\VarDumper\Cloner\Data;
use Throwable;

#[When('dev')]
#[AutoconfigureTag('data_collector', ['id' => 'data_grid'])]
class DataGridDataCollector extends DataCollector implements TemplateAwareDataCollectorInterface
{
    public function __construct(private readonly TraceableDataGridHandler $dataGridHandler)
    {
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
        $this->data = ['calls' => $this->dataGridHandler->getCalls()];
        $this->data = $this->cloneVar($this->data);
    }

    public function reset(): void
    {
        $this->data = ['calls' => []];
        $this->data = $this->cloneVar($this->data);
        $this->dataGridHandler->clearCalls();
    }

    protected function getCasters(): array
    {
        return [
            GridRequest::class => fn (GridRequest $f): array => [
                Caster::PREFIX_VIRTUAL.'sorting' => $f->getSorting(),
                Caster::PREFIX_VIRTUAL.'limit' => $f->getLimit(),
                Caster::PREFIX_VIRTUAL.'offset' => $f->getOffset(),
                Caster::PREFIX_VIRTUAL.'filters' => $f->getFilters(),
            ],
            DataGrid::class => StubCaster::cutInternals(...),
        ];
    }

    /**
     * @return Data<DataGridCall>
     */
    public function getCalls(): Data
    {
        return $this->data['calls'];
    }

    public function getName(): string
    {
        return 'data_grid';
    }

    public static function getTemplate(): ?string
    {
        return 'data_collector/data_grid.html.twig';
    }
}
