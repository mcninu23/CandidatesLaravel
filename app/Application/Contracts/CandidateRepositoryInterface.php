<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Candidate\Entities\Candidate;

interface CandidateRepositoryInterface
{
    /**
     * Guarda una candidatura en el almacenamiento persistente.
     *
     * - Si el Candidate no tiene ID (nuevo), lo crea.
     * - Si ya tiene ID, lo actualiza.
     *
     * Se devuelve siempre la instancia (por si el repositorio asigna el ID).
     */
    public function save(Candidate $candidate): Candidate;

    /**
     * Obtiene una candidatura por ID o lanza una excepción si no existe.
     * 
     * Se permite int|string para ser flexible (auto-incremental o UUID).
     *
     * @throws \RuntimeException si no se encuentra el candidato.
     */
    public function findByIdOrFail(int|string $id): Candidate;

    /**
     * Obtiene una candidatura por ID o null si no existe.
     */
    public function findById(int|string $id): ?Candidate;

    /**
     * (Opcionalmente útil) Comprueba si ya existe una candidatura con ese email.
     * Puede servir para evitar duplicados en el registro.
     */
    public function existsByEmail(string $email): bool;
}
