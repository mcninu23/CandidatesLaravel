<?php

declare(strict_types=1);

namespace Tests\Unit\Candidate\Validation;

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\HasCvRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use PHPUnit\Framework\TestCase;

final class HasCvRuleTest extends TestCase
{
    public function test_fails_when_candidate_has_empty_cv(): void
    {
        $rule = new HasCvRule();

        $candidate = new Candidate(
            id: 1,
            fullName: 'Juan Candidato',
            email: new CandidateEmail('juan@example.com'),
            yearsExperience: new YearsOfExperience(3),
            cvText: '',
            status: 'pending',
        );

        $result = $rule->validate($candidate);

        $this->assertSame('has_cv', $result->ruleName);
        $this->assertFalse($result->passed);
        $this->assertSame('El candidato debe aportar CV.', $result->message);
    }

    public function test_passes_when_candidate_has_non_empty_cv(): void
    {
        $rule = new HasCvRule();

        $candidate = new Candidate(
            id: 1,
            fullName: 'Juan Candidato',
            email: new CandidateEmail('juan@example.com'),
            yearsExperience: new YearsOfExperience(3),
            cvText: 'Experiencia en PHP y Laravel',
            status: 'pending',
        );

        $result = $rule->validate($candidate);

        $this->assertSame('has_cv', $result->ruleName);
        $this->assertTrue($result->passed);
        $this->assertNull($result->message);
    }
}
