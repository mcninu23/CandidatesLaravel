<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Contracts\AssignmentRepositoryInterface;
use App\Application\Contracts\CandidateRepositoryInterface;
use App\Application\Contracts\EvaluatorRepositoryInterface;
use App\Domain\Assignment\Entities\Assignment;
use DateTimeImmutable;

final class AssignEvaluatorHandler
{
    public function __construct(
        private readonly CandidateRepositoryInterface $candidates,
        private readonly EvaluatorRepositoryInterface $evaluators,
        private readonly AssignmentRepositoryInterface $assignments,
    ) {
    }

    /**
     * Asigna un evaluador a una candidatura.
     *
     * @param int|string $candidateId
     * @param int|string $evaluatorId
     */
    public function handle(int|string $candidateId, int|string $evaluatorId): Assignment
    {
        // Recuperamos entidades de dominio (o lanzan excepción si no existen)
        $candidate = $this->candidates->findByIdOrFail($candidateId);
        $evaluator = $this->evaluators->findByIdOrFail($evaluatorId);

        // Fecha de asignación: ahora
        $assignedAt = new DateTimeImmutable();

        // Creamos la asignación en el repositorio
        $assignment = $this->assignments->createAssignment(
            $candidate,
            $evaluator,
            $assignedAt
        );

        return $assignment;
    }
}
