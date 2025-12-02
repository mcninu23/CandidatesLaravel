<?php

declare(strict_types=1);

namespace App\Domain\Candidate\Services;

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\ValidationResult;
use App\Domain\Candidate\ValidationRules\ValidationRuleInterface;
use Illuminate\Support\Collection;

final class CandidateValidator
{
    /** @var Collection<int, ValidationRuleInterface> */
    private Collection $rules;

    /**
     * @param ValidationRuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = collect($rules);
    }

    /**
     * @return Collection<int, ValidationResult>
     */
    public function validate(Candidate $candidate): Collection
    {
        return $this->rules->map(
            fn (ValidationRuleInterface $rule) => $rule->validate($candidate)
        );
    }

    public function isValid(Candidate $candidate): bool
    {
        return $this->validate($candidate)->every(
            fn (ValidationResult $result) => $result->passed
        );
    }
}
