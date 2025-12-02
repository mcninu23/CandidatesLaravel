<?php

declare(strict_types=1);

namespace Tests\Unit\Candidate\Validation;

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\ValidEmailRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use PHPUnit\Framework\TestCase;

final class ValidEmailRuleTest extends TestCase
{
    public function test_fails_when_email_is_invalid(): void
    {
        $rule = new ValidEmailRule();

        $candidate = new Candidate(
            id: 1,
            fullName: 'Juan Candidato',
            email: new CandidateEmail('not-an-email'),
            yearsExperience: new YearsOfExperience(3),
            cvText: 'CV',
            status: 'pending',
        );

        $result = $rule->validate($candidate);

        $this->assertSame('valid_email', $result->ruleName);
        $this->assertFalse($result->passed);
        $this->assertSame('El email del candidato no es válido.', $result->message);
    }

    public function test_passes_when_email_is_valid(): void
    {
        $rule = new ValidEmailRule();

        $candidate = new Candidate(
            id: 1,
            fullName: 'Juan Candidato',
            email: new CandidateEmail('juan@example.com'),
            yearsExperience: new YearsOfExperience(3),
            cvText: 'CV',
            status: 'pending',
        );

        $result = $rule->validate($candidate);

        $this->assertSame('valid_email', $result->ruleName);
        $this->assertTrue($result->passed);
        $this->assertNull($result->message);
    }
}
