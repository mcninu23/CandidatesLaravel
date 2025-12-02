<?php

declare(strict_types=1);

namespace App\Domain\Evaluator\Entities;

use DateTimeImmutable;

final class Evaluator
{
    private ?int $id;
    private string $fullName;
    private string $email;
    private DateTimeImmutable $createdAt;

    public function __construct(
        ?int $id,
        string $fullName,
        string $email,
        ?DateTimeImmutable $createdAt = null
    ) {
        $fullName = trim($fullName);
        $email    = trim(mb_strtolower($email));

        if ($fullName === '') {
            throw new \InvalidArgumentException('Evaluator full name cannot be empty.');
        }

        if ($email === '') {
            throw new \InvalidArgumentException('Evaluator email cannot be empty.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid evaluator email: "%s".', $email));
        }

        $this->id        = $id;
        $this->fullName  = $fullName;
        $this->email     = $email;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($this->id !== null && $this->id !== $id) {
            throw new \LogicException('Evaluator ID cannot be changed once set.');
        }

        $this->id = $id;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function changeFullName(string $fullName): void
    {
        $fullName = trim($fullName);

        if ($fullName === '') {
            throw new \InvalidArgumentException('Evaluator full name cannot be empty.');
        }

        $this->fullName = $fullName;
    }

    public function changeEmail(string $email): void
    {
        $email = trim(mb_strtolower($email));

        if ($email === '') {
            throw new \InvalidArgumentException('Evaluator email cannot be empty.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid evaluator email: "%s".', $email));
        }

        $this->email = $email;
    }
}
