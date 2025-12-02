<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidatedListRequest;
use App\Exports\ConsolidatedCandidatesExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ConsolidatedExportController extends Controller
{
    public function __invoke(ConsolidatedListRequest $request)
    {
        $params = $request->sanitized();

        // Base query del endpoint consolidado, pero sin paginar
        $rows = DB::table('candidates as c')
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
            ->join('evaluators as e', 'e.id', '=', 'a.evaluator_id')
            ->orderBy('c.years_experience', 'desc')
            ->get();

        // Nombre del archivo
        $fileName = 'consolidated_candidates.xlsx';

        // Exportar directamente
        return Excel::download(
            new ConsolidatedCandidatesExport($rows),
            $fileName
        );
    }
}
