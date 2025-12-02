<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Assignment\Entities\Assignment;
use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Evaluator\Entities\Evaluator;
use DateTimeImmutable;

interface AssignmentRepositoryInterface
{
    public function createAssignment(Candidate $candidate, Evaluator $evaluator, DateTimeImmutable $assignedAt): Assignment;

    /**
     * Devuelve todas las asignaciones de un candidato (para el resumen).
     *
     * @return Assignment[]
     */
    public function findByCandidate(Candidate $candidate): array;
}
