<?php

namespace App\Infrastructure\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ConsolidatedSheetExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithTitle
{
    public function __construct(
        private readonly Collection $rows,
        private readonly int $pageNumber
    ) {}

    public function array(): array
    {
        return $this->rows->toArray();
    }

    public function title(): string
    {
        return "Pagina {$this->pageNumber}";
    }
}