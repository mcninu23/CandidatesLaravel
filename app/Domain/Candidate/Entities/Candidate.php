<?php

declare(strict_types=1);

namespace App\Domain\Candidate\Entities;

use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use DateTimeImmutable;

/**
 * Entidad de dominio Candidate
 *
 * Esta clase NO sabe nada de Eloquent, HTTP, ni Laravel.
 * Representa el modelo de negocio de una candidatura.
 */
final class Candidate
{
    /**
     * El ID puede ser null mientras la entidad aún no ha sido persistida.
     */
    private ?int $id;

    private string $fullName;

    private CandidateEmail $email;

    private YearsOfExperience $yearsExperience;

    /**
     * CV en texto plano (puede venir de un textarea, por ejemplo).
     */
    private string $cvText;

    /**
     * Estado de la candidatura (opcional).
     * Ejemplos: "pending", "valid", "invalid", "rejected", etc.
     */
    private ?string $status;

    /**
     * Fecha de creación en el dominio (no tiene por qué ser igual a created_at de DB,
     * pero normalmente lo mapearemos tal cual).
     */
    private DateTimeImmutable $createdAt;

    public function __construct(
        ?int $id,
        string $fullName,
        CandidateEmail $email,
        YearsOfExperience $yearsExperience,
        string $cvText,
        ?string $status = null,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->yearsExperience = $yearsExperience;
        $this->cvText = $cvText;
        $this->status = $status;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    // --------
    // Getters
    // --------

    public function id(): ?int
    {
        return $this->id;
    }

    /**
     * Este setter se expone porque típicamente el ID lo asigna la base de datos.
     * El repositorio puede llamar a este método tras persistir la entidad.
     */
    public function setId(int $id): void
    {
        // Permitimos asignar sólo una vez para no "saltar" identidades
        if ($this->id !== null && $this->id !== $id) {
            throw new \LogicException('Candidate ID cannot be changed once set.');
        }

        $this->id = $id;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function email(): CandidateEmail
    {
        return $this->email;
    }

    public function yearsExperience(): YearsOfExperience
    {
        return $this->yearsExperience;
    }

    public function cvText(): string
    {
        return $this->cvText;
    }

    public function status(): ?string
    {
        return $this->status;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // -------------------
    // Comportamiento de dominio
    // -------------------

    public function changeFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function changeEmail(CandidateEmail $email): void
    {
        $this->email = $email;
    }

    public function changeYearsExperience(YearsOfExperience $yearsExperience): void
    {
        $this->yearsExperience = $yearsExperience;
    }

    public function updateCv(string $cvText): void
    {
        $this->cvText = $cvText;
    }

    public function markAsValid(): void
    {
        $this->status = 'valid';
    }

    public function markAsInvalid(): void
    {
        $this->status = 'invalid';
    }

    public function markAsPending(): void
    {
        $this->status = 'pending';
    }
}
