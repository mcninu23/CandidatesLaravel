<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Contracts\EvaluatorRepositoryInterface;
use App\Domain\Evaluator\Entities\Evaluator;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use DateTimeImmutable;
use RuntimeException;

final class EloquentEvaluatorRepository implements EvaluatorRepositoryInterface
{
    public function __construct(
        private readonly EvaluatorModel $model
    ) {
    }

    public function save(Evaluator $evaluator): Evaluator
    {
        $model = $this->getModelForEvaluator($evaluator);

        $model->full_name = $evaluator->fullName();
        $model->email     = $evaluator->email();

        $model->save();

        if ($evaluator->id() === null) {
            $evaluator->setId((int) $model->getKey());
        }

        return $evaluator;
    }

    public function findByIdOrFail(int|string $id): Evaluator
    {
        $evaluator = $this->findById($id);

        if ($evaluator === null) {
            throw new RuntimeException(sprintf('Evaluator with id "%s" not found.', (string) $id));
        }

        return $evaluator;
    }

    public function findById(int|string $id): ?Evaluator
    {
        /** @var EvaluatorModel|null $model */
        $model = $this->model->newQuery()->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function existsByEmail(string $email): bool
    {
        $normalized = trim(mb_strtolower($email));

        return $this->model
            ->newQuery()
            ->where('email', $normalized)
            ->exists();
    }

    private function getModelForEvaluator(Evaluator $evaluator): EvaluatorModel
    {
        if ($evaluator->id() === null) {
            return $this->model->newInstance();
        }

        /** @var EvaluatorModel|null $model */
        $model = $this->model->newQuery()->find($evaluator->id());

        if ($model === null) {
            throw new RuntimeException(sprintf(
                'Cannot save Evaluator with id "%s": record not found in database.',
                (string) $evaluator->id()
            ));
        }

        return $model;
    }

    private function toEntity(EvaluatorModel $model): Evaluator
    {
        $createdAt = null;

        if ($model->created_at !== null) {
            $createdAt = DateTimeImmutable::createFromMutable($model->created_at);
        }

        return new Evaluator(
            id: (int) $model->getKey(),
            fullName: (string) $model->full_name,
            email: (string) $model->email,
            createdAt: $createdAt
        );
    }
}
