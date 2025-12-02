<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Contracts\ConsolidatedReadModelInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentConsolidatedReadModel implements ConsolidatedReadModelInterface
{
    public function search(array $filters, string $sortBy = 'years_experience', string $direction = 'desc', int $perPage = 15): LengthAwarePaginator
    {
        $base = DB::table('assignments as a')
            ->join('candidates as c', 'c.id', '=', 'a.candidate_id')
            ->join('evaluators as e', 'e.id', '=', 'a.evaluator_id')
            ->leftJoin(DB::raw('(
                SELECT 
                    a2.evaluator_id,
                    COUNT(*) as total_assigned,
                    GROUP_CONCAT(DISTINCT c2.email ORDER BY c2.email SEPARATOR ",") as emails
                FROM assignments a2
                JOIN candidates c2 ON c2.id = a2.candidate_id
                GROUP BY a2.evaluator_id
            ) as stats'), 'stats.evaluator_id', '=', 'e.id')
            ->selectRaw('
                c.full_name as candidate_name,
                c.email as candidate_email,
                c.years_experience,
                e.full_name as evaluator_name,
                a.assigned_at,
                stats.total_assigned,
                stats.emails as evaluator_candidate_emails
            ');

        // Filtros dinámicos (ej: ?candidate_email=...&evaluator_name=...)
        if (!empty($filters['candidate_email'])) {
            $base->where('c.email', 'like', '%' . $filters['candidate_email'] . '%');
        }

        if (!empty($filters['candidate_name'])) {
            $base->where('c.full_name', 'like', '%' . $filters['candidate_name'] . '%');
        }

        if (!empty($filters['evaluator_name'])) {
            $base->where('e.full_name', 'like', '%' . $filters['evaluator_name'] . '%');
        }

        // Podrías permitir sortBy en una whitelist
        $allowedSorts = [
            'candidate_name' => 'c.full_name',
            'candidate_email' => 'c.email',
            'years_experience' => 'c.years_experience',
            'evaluator_name' => 'e.full_name',
            'assigned_at' => 'a.assigned_at',
            'total_assigned' => 'stats.total_assigned',
        ];

        $sortColumn = $allowedSorts[$sortBy] ?? 'c.years_experience';
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return $base
            ->orderBy($sortColumn, $direction)
            ->paginate($perPage);
    }
}
