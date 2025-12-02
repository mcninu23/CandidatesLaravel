<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Application\Contracts\CandidateRepositoryInterface;
use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use DateTimeImmutable;
use RuntimeException;

final class EloquentCandidateRepository implements CandidateRepositoryInterface
{
    public function __construct(
        private readonly CandidateModel $model
    ) {
    }

    public function save(Candidate $candidate): Candidate
    {
        $model = $this->getModelForCandidate($candidate);

        $model->full_name        = $candidate->fullName();
        $model->email            = $candidate->email()->value();
        $model->years_experience = $candidate->yearsExperience()->value();
        $model->cv_text          = $candidate->cvText();
        $model->status           = $candidate->status();

        // Dejo que Eloquent gestione created_at/updated_at.
        // Si quisieras forzar created_at desde dominio, podrías asignarlo aquí.

        $model->save();

        // Si el candidato no tenía ID, lo asignamos ahora desde el modelo
        if ($candidate->id() === null) {
            $candidate->setId((int) $model->getKey());
        }

        return $candidate;
    }

    public function findByIdOrFail(int|string $id): Candidate
    {
        $candidate = $this->findById($id);

        if ($candidate === null) {
            throw new RuntimeException(sprintf('Candidate with id "%s" not found.', (string) $id));
        }

        return $candidate;
    }

    public function findById(int|string $id): ?Candidate
    {
        /** @var CandidateModel|null $model */
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

    /**
     * Obtiene el modelo Eloquent correspondiente a la entidad.
     * Si la entidad tiene ID, intenta recuperar el registro. Si no existe, crea uno nuevo.
     * Si no tiene ID, siempre crea un nuevo modelo.
     */
    private function getModelForCandidate(Candidate $candidate): CandidateModel
    {
        if ($candidate->id() === null) {
            return $this->model->newInstance();
        }

        /** @var CandidateModel|null $model */
        $model = $this->model->newQuery()->find($candidate->id());

        if ($model === null) {
            // Decisión: si el dominio tiene un ID pero en BD no existe, consideramos que es un error de integridad.
            throw new RuntimeException(sprintf(
                'Cannot save Candidate with id "%s": record not found in database.',
                (string) $candidate->id()
            ));
        }

        return $model;
    }

    /**
     * Reconstruye una entidad de dominio Candidate a partir de un modelo Eloquent.
     */
    private function toEntity(CandidateModel $model): Candidate
    {
        $createdAt = null;

        if ($model->created_at !== null) {
            // created_at suele ser instancia de Carbon (DateTimeImmutable compatible)
            $createdAt = DateTimeImmutable::createFromMutable($model->created_at);
        }

        return new Candidate(
            id: (int) $model->getKey(),
            fullName: (string) $model->full_name,
            email: new CandidateEmail((string) $model->email),
            yearsExperience: new YearsOfExperience((int) $model->years_experience),
            cvText: (string) $model->cv_text,
            status: $model->status !== null ? (string) $model->status : null,
            createdAt: $createdAt
        );
    }
}
