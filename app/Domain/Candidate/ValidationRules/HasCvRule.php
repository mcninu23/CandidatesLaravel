<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValidationRules;

use App\Domain\Candidate\Entities\Candidate;

final class HasCvRule implements ValidationRuleInterface
{
    public function validate(Candidate $candidate): ValidationResult
    {
        if (trim($candidate->cvText()) === '') {
            return ValidationResult::fail(
                'has_cv',
                'El candidato debe aportar CV.'
            );
        }

        return ValidationResult::pass('has_cv');
    }
}
