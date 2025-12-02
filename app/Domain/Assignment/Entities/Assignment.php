<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Entities;

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Evaluator\Entities\Evaluator;
use DateTimeImmutable;

final class Assignment
{
    private ?int $id;
    private Candidate $candidate;
    private Evaluator $evaluator;
    private DateTimeImmutable $assignedAt;

    public function __construct(
        ?int $id,
        Candidate $candidate,
        Evaluator $evaluator,
        DateTimeImmutable $assignedAt
    ) {
        $this->id         = $id;
        $this->candidate  = $candidate;
        $this->evaluator  = $evaluator;
        $this->assignedAt = $assignedAt;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($this->id !== null && $this->id !== $id) {
            throw new \LogicException('Assignment ID cannot be changed once set.');
        }

        $this->id = $id;
    }

    public function candidate(): Candidate
    {
        return $this->candidate;
    }

    public function evaluator(): Evaluator
    {
        return $this->evaluator;
    }

    public function assignedAt(): DateTimeImmutable
    {
        return $this->assignedAt;
    }
}
