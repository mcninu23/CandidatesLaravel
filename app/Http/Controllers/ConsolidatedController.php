<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidatedListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ConsolidatedController extends Controller
{
    /**
     * Listado consolidado de candidaturas con evaluador asignado.
     *
     * GET /api/candidates/consolidated
     */
    public function __invoke(ConsolidatedListRequest $request): JsonResponse
    {
        $params = $request->sanitized();

        // Mapeo de columnas permitidas para ordenación
        $orderableColumns = [
            'full_name'        => 'c.full_name',
            'email'            => 'c.email',
            'years_experience' => 'c.years_experience',
            'evaluator_name'   => 'e.full_name',
            'assigned_at'      => 'a.assigned_at',
        ];

        $orderBy = $orderableColumns[$params['order_by']] ?? 'c.years_experience';
        $orderDir = $params['order_dir'] === 'asc' ? 'asc' : 'desc';

        $page    = $params['page'];
        $perPage = $params['per_page'];

        // Base query: solo candidaturas con evaluador asignado
        $query = DB::table('candidates as c')
            ->select([
                'c.id as candidate_id',
                'c.full_name',
                'c.email',
                'c.years_experience',
                'e.id as evaluator_id',
                'e.full_name as evaluator_name',
                'a.assigned_at',

                // Total de candidatos evaluados por este evaluador
                DB::raw('(
                    SELECT COUNT(DISTINCT a2.candidate_id)
                    FROM assignments a2
                    WHERE a2.evaluator_id = e.id
                ) AS total_candidates_by_evaluator'),

                // Lista concatenada de emails evaluados por este evaluador
                DB::raw('(
                    SELECT GROUP_CONCAT(DISTINCT c2.email)
                    FROM assignments a3
                    JOIN candidates c2 ON c2.id = a3.candidate_id
                    WHERE a3.evaluator_id = e.id
                ) AS evaluator_candidate_emails'),

            ])
            ->join('assignments as a', 'a.candidate_id', '=', 'c.id')
            ->join('evaluators as e', 'e.id', '=', 'a.evaluator_id');

        // ---- Filtros opcionales ----
        foreach ($params['filters'] as $column => $value) {
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

        // ---- Orden ----
        $query->orderBy($orderBy, $orderDir);

        // ---- Paginación ----
        $results = $query->paginate(
            perPage: $perPage,
            page: $page
        );

        return response()->json($results);
    }
}
