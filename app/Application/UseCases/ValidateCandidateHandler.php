<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Contracts\CandidateRepositoryInterface;
use App\Domain\Candidate\Services\CandidateValidator;

final class ValidateCandidateHandler
{
    public function __construct(
        private readonly CandidateRepositoryInterface $candidates,
        private readonly CandidateValidator $validator,
    ) {
    }

    /**
     * @return array{
     *   candidate_id: int|string,
     *   is_valid: bool,
     *   results: array<int, array{rule: string, passed: bool, message: ?string}>
     * }
     */
    public function handle(int|string $candidateId): array
    {
        $candidate = $this->candidates->findByIdOrFail($candidateId);

        $results = $this->validator->validate($candidate);

        return [
            'candidate_id' => $candidateId,
            'is_valid' => $results->every(fn ($r) => $r->passed),
            'results' => $results->map(fn ($r) => [
                'rule' => $r->ruleName,
                'passed' => $r->passed,
                'message' => $r->message,
            ])->toArray(),
        ];
    }
}
