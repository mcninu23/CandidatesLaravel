<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exports\ConsolidatedCandidatesExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportConsolidatedCandidatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Por ahora ignoramos email y notificación
     */
    public function __construct(
        private readonly ?string $emailToNotify = null,
        private readonly array $filters = [],
        private readonly string $orderBy = 'years_experience',
        private readonly string $orderDir = 'desc'
    ) {
    }

    public function handle(): void
    {
        // 1. Query consolidada
        $orderableColumns = [
            'full_name'        => 'c.full_name',
            'email'            => 'c.email',
            'years_experience' => 'c.years_experience',
            'evaluator_name'   => 'e.full_name',
            'assigned_at'      => 'a.assigned_at',
        ];

        $orderBy = $orderableColumns[$this->orderBy] ?? 'c.years_experience';
        $orderDir = $this->orderDir === 'asc' ? 'asc' : 'desc';

        $query = DB::table('candidates as c')
            ->select([
                'c.id as candidate_id',
                'c.full_name',
                'c.email',
                'c.years_experience',
                'e.id as evaluator_id',
                'e.full_name as evaluator_name',
                'a.assigned_at',
                DB::raw('(
                    SELECT COUNT(DISTINCT a2.candidate_id)
                    FROM assignments a2
                    WHERE a2.evaluator_id = e.id
                ) AS total_candidates_by_evaluator'),
                DB::raw('(
                    SELECT GROUP_CONCAT(DISTINCT c2.email)
                    FROM assignments a3
                    JOIN candidates c2 ON c2.id = a3.candidate_id
                    WHERE a3.evaluator_id = e.id
                ) AS evaluator_candidate_emails'),
            ])
            ->join('assignments as a', 'a.candidate_id', '=', 'c.id')
            ->join('evaluators as e', 'e.id', '=', 'a.evaluator_id');

        foreach ($this->filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            switch ($column) {
                case 'full_name':
                    $query->where('c.full_name', 'LIKE', '%' . $value . '%');
                    break;
                case 'email':
                    $query->where('c.email', 'LIKE', '%' . $value . '%');
                    break;
                case 'evaluator_name':
                    $query->where('e.full_name', 'LIKE', '%' . $value . '%');
                    break;
                case 'years_experience':
                    $query->where('c.years_experience', '=', (int) $value);
                    break;
            }
        }

        $query->orderBy($orderBy, $orderDir);

        $rows = $query->get();

        // 2. Guardar el Excel (en storage/app/private/exports)
        $timestamp = now()->format('Ymd_His');
        $filePath  = "exports/consolidated_candidates_{$timestamp}.xlsx";

        Excel::store(
            new ConsolidatedCandidatesExport($rows),
            $filePath,
            'local'
        );

        Log::info('Consolidated export generated (NO EMAIL)', ['path' => $filePath]);
    }
}
