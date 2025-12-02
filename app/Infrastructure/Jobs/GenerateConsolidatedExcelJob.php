<?php

namespace App\Infrastructure\Jobs;

class GenerateConsolidatedExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $filters,
        private readonly string $sortBy,
        private readonly string $direction,
        private readonly string $requestId, // para idempotencia
        private readonly string $notifyEmail,
    ) {}

    public function handle(
        ConsolidatedReadModelInterface $readModel,
        ExcelReportNotifierInterface $notifier
    ): void {
        // Idempotencia básica: si ya generamos un report con este requestId, salimos.
        if (Storage::disk('local')->exists("reports/{$this->requestId}.xlsx")) {
            return;
        }

        $page = 1;
        $perPage = 500; // para el Excel no hace falta la misma paginación que la API

        $rowsCollection = collect();

        do {
            $pageData = $readModel->search(
                $this->filters,
                $this->sortBy,
                $this->direction,
                $perPage
            );
            $rowsCollection = $rowsCollection->merge($pageData->items());
            $page++;
        } while ($page <= $pageData->lastPage());

        // dividir en chunks de 50 para hojas
        $chunks = $rowsCollection->chunk(50);

        // Usando maatwebsite/excel (por ejemplo)
        $export = new ConsolidatedExport($chunks);

        Excel::store($export, "reports/{$this->requestId}.xlsx");

        $notifier->notifyReportReady($this->notifyEmail, "reports/{$this->requestId}.xlsx");
    }
}
