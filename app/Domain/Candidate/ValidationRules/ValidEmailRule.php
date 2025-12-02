<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValidationRules;

use App\Domain\Candidate\Entities\Candidate;

final class ValidEmailRule implements ValidationRuleInterface
{
    public function validate(Candidate $candidate): ValidationResult
    {
        $email = $candidate->email()->value();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ValidationResult::fail(
                'valid_email',
                'El email del candidato no es válido.'
            );
        }

        return ValidationResult::pass('valid_email');
    }
}
