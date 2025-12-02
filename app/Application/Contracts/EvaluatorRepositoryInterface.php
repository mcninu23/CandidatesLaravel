<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Evaluator\Entities\Evaluator;

interface EvaluatorRepositoryInterface
{
    public function save(Evaluator $evaluator): Evaluator;

    /**
     * @throws \RuntimeException si no se encuentra el evaluador.
     */
    public function findByIdOrFail(int|string $id): Evaluator;

    public function findById(int|string $id): ?Evaluator;

    public function existsByEmail(string $email): bool;
}
