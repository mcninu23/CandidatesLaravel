<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Contracts\AssignmentRepositoryInterface;
use App\Domain\Assignment\Entities\Assignment;
use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Evaluator\Entities\Evaluator;
use App\Infrastructure\Persistence\Eloquent\Models\AssignmentModel;
use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use DateTimeImmutable;

final class EloquentAssignmentRepository implements AssignmentRepositoryInterface
{
    public function __construct(
        private readonly AssignmentModel $model
    ) {
    }

    public function createAssignment(Candidate $candidate, Evaluator $evaluator, DateTimeImmutable $assignedAt): Assignment
    {
        $model = $this->model->newInstance();

        $model->candidate_id = $candidate->id();
        $model->evaluator_id = $evaluator->id();
        $model->assigned_at  = $assignedAt;

        $model->save();

        $assignment = new Assignment(
            id: (int) $model->getKey(),
            candidate: $candidate,
            evaluator: $evaluator,
            assignedAt: $assignedAt
        );

        return $assignment;
    }

    public function findByCandidate(Candidate $candidate): array
    {
        $models = $this->model
            ->newQuery()
            ->where('candidate_id', $candidate->id())
            ->with(['candidate', 'evaluator'])
            ->get();

        return $models->map(function (AssignmentModel $model) use ($candidate) {
            /** @var EvaluatorModel $evaluatorModel */
            $evaluatorModel = $model->evaluator;

            $evaluator = new Evaluator(
                id: (int) $evaluatorModel->getKey(),
                fullName: (string) $evaluatorModel->full_name,
                email: (string) $evaluatorModel->email,
                createdAt: $evaluatorModel->created_at
                    ? DateTimeImmutable::createFromMutable($evaluatorModel->created_at)
                    : null
            );

            $assignedAt = $model->assigned_at
                ? DateTimeImmutable::createFromMutable($model->assigned_at)
                : new DateTimeImmutable();

            return new Assignment(
                id: (int) $model->getKey(),
                candidate: $candidate,
                evaluator: $evaluator,
                assignedAt: $assignedAt
            );
        })->all();
    }
}
