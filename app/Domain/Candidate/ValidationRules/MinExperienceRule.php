<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValidationRules;

use App\Domain\Candidate\Entities\Candidate;

final class MinExperienceRule implements ValidationRuleInterface
{
    public function __construct(
        private readonly int $minYears = 2
    ) {
    }

    public function validate(Candidate $candidate): ValidationResult
    {
        if ($candidate->yearsExperience()->value() < $this->minYears) {
            return ValidationResult::fail(
                'min_experience',
                sprintf('Debe tener al menos %d años de experiencia.', $this->minYears)
            );
        }

        return ValidationResult::pass('min_experience');
    }
}
