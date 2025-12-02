<?php

declare(strict_types=1);

namespace Tests\Unit\Candidate\Validation;

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\MinExperienceRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use PHPUnit\Framework\TestCase;

final class MinExperienceRuleTest extends TestCase
{
    public function test_fails_when_experience_is_below_minimum(): void
    {
        $rule = new MinExperienceRule(2);

        $candidate = new Candidate(
            id: 1,
            fullName: 'Junior Dev',
            email: new CandidateEmail('junior@example.com'),
            yearsExperience: new YearsOfExperience(1),
            cvText: 'CV',
            status: 'pending',
        );

        $result = $rule->validate($candidate);

        $this->assertSame('min_experience', $result->ruleName);
        $this->assertFalse($result->passed);
        $this->assertSame('Debe tener al menos 2 años de experiencia.', $result->message);
    }

    public function test_passes_when_experience_meets_minimum(): void
    {
        $rule = new MinExperienceRule(2);

        $candidate = new Candidate(
            id: 1,
            fullName: 'Mid Dev',
            email: new CandidateEmail('mid@example.com'),
            yearsExperience: new YearsOfExperience(3),
            cvText: 'CV',
            status: 'pending',
        );

        $result = $rule->validate($candidate);

        $this->assertSame('min_experience', $result->ruleName);
        $this->assertTrue($result->passed);
        $this->assertNull($result->message);
    }
}
