<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Contracts\AssignmentRepositoryInterface;
use App\Application\Contracts\CandidateRepositoryInterface;
use App\Domain\Candidate\Services\CandidateValidator;
use App\Domain\Assignment\Entities\Assignment;

final class GetCandidateSummaryHandler
{
    public function __construct(
        private readonly CandidateRepositoryInterface $candidates,
        private readonly AssignmentRepositoryInterface $assignments,
        private readonly CandidateValidator $validator,
    ) {
    }

    /**
     * @return array{
     *   candidate: array{
     *     id: int|null,
     *     full_name: string,
     *     email: string,
     *     years_experience: int,
     *     cv_text: string,
     *     status: ?string,
     *     created_at: string
     *   },
     *   validation: array{
     *     is_valid: bool,
     *     results: array<int, array{rule: string, passed: bool, message: ?string}>
     *   },
     *   evaluator: ?array{
     *     id: int|null,
     *     full_name: string,
     *     email: string
     *   },
     *   assignments: array<int, array{
     *     id: int|null,
     *     evaluator_id: int|null,
     *     evaluator_name: string,
     *     assigned_at: string
     *   }>
     * }
     */
    public function handle(int|string $candidateId): array
    {
        // 1. Cargar candidato de dominio
        $candidate = $this->candidates->findByIdOrFail($candidateId);

        // 2. Validaciones
        $validationResults = $this->validator->validate($candidate);
        $isValid = $validationResults->every(fn ($r) => $r->passed);

        // 3. Asignaciones
        $assignments = $this->assignments->findByCandidate($candidate);

        /** @var Assignment|null $currentAssignment */
        $currentAssignment = null;

        if (!empty($assignments)) {
            // Cogemos la última asignación por fecha (la más reciente)
            usort($assignments, fn (Assignment $a, Assignment $b) =>
                $a->assignedAt() <=> $b->assignedAt()
            );
            $currentAssignment = end($assignments) ?: null;
        }

        return [
            'candidate' => [
                'id'               => $candidate->id(),
                'full_name'        => $candidate->fullName(),
                'email'            => $candidate->email()->value(),
                'years_experience' => $candidate->yearsExperience()->value(),
                'cv_text'          => $candidate->cvText(),
                'status'           => $candidate->status(),
                'created_at'       => $candidate->createdAt()->format(DATE_ATOM),
            ],
            'validation' => [
                'is_valid' => $isValid,
                'results'  => $validationResults->map(fn ($r) => [
                    'rule'    => $r->ruleName,
                    'passed'  => $r->passed,
                    'message' => $r->message,
                ])->toArray(),
            ],
            'evaluator' => $currentAssignment ? [
                'id'         => $currentAssignment->evaluator()->id(),
                'full_name'  => $currentAssignment->evaluator()->fullName(),
                'email'      => $currentAssignment->evaluator()->email(),
            ] : null,
            'assignments' => array_map(
                fn (Assignment $assignment) => [
                    'id'             => $assignment->id(),
                    'evaluator_id'   => $assignment->evaluator()->id(),
                    'evaluator_name' => $assignment->evaluator()->fullName(),
                    'assigned_at'    => $assignment->assignedAt()->format(DATE_ATOM),
                ],
                $assignments
            ),
        ];
    }
}
