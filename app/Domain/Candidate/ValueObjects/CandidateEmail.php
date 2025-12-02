<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValueObjects;

/**
 * Value Object que representa el email de un candidato.
 *
 * Inmutable: una vez creado, no se modifica.
 * Valida el formato del email en el constructor.
 */
final class CandidateEmail
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim(mb_strtolower($value));

        if ($value === '') {
            throw new \InvalidArgumentException('Candidate email cannot be empty.');
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid candidate email: "%s".', $value));
        }

        $this->value = $value;
    }

    /**
     * Devuelve el email como string plano.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compara dos CandidateEmail por valor.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
