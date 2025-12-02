<?php

namespace App\Infrastructure\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConsolidatedExport implements WithMultipleSheets
{
    public function __construct(private readonly Collection $chunks) {}

    public function sheets(): array
    {
        return $this->chunks->map(
            fn ($chunk, $index) => new ConsolidatedSheetExport($chunk, $index + 1)
        )->all();
    }
}
