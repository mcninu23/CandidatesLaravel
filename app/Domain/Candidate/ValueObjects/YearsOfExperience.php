<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValueObjects;

/**
 * Value Object que representa los años de experiencia de un candidato.
 *
 * Inmutable, autocontenida y valida siempre su propio estado.
 */
final class YearsOfExperience
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Years of experience cannot be negative.');
        }

        if ($value > 60) {
            throw new \InvalidArgumentException('Years of experience seems unrealistic (> 60).');
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
