<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Contracts\CandidateRepositoryInterface;
use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;

final class RegisterCandidateHandler
{
    public function __construct(
        private readonly CandidateRepositoryInterface $candidates
    ) {
    }

    /**
     * Registra una nueva candidatura.
     *
     * @param array{
     *   full_name: string,
     *   email: string,
     *   years_experience: int,
     *   cv_text: string
     * } $data
     */
    public function handle(array $data): Candidate
    {
        // Regla opcional: no permitir candidatos duplicados por email
        if ($this->candidates->existsByEmail($data['email'])) {
            throw new \RuntimeException('Ya existe un candidato con ese email.');
        }

        $emailVo  = new CandidateEmail($data['email']);
        $yearsVo  = new YearsOfExperience($data['years_experience']);

        $candidate = new Candidate(
            id: null,
            fullName: $data['full_name'],
            email: $emailVo,
            yearsExperience: $yearsVo,
            cvText: $data['cv_text'],
            status: 'pending'
        );

        return $this->candidates->save($candidate);
    }
}
