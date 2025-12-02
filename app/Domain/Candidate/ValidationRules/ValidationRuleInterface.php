<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValidationRules;

use App\Domain\Candidate\Entities\Candidate;

interface ValidationRuleInterface
{
    public function validate(Candidate $candidate): ValidationResult;
}
