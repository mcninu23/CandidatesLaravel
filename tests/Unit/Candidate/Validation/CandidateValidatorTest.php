<?php

declare(strict_types=1);

namespace Tests\Unit\Candidate\Validation;

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\Services\CandidateValidator;
use App\Domain\Candidate\ValidationRules\HasCvRule;
use App\Domain\Candidate\ValidationRules\MinExperienceRule;
use App\Domain\Candidate\ValidationRules\ValidEmailRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use PHPUnit\Framework\TestCase;

final class CandidateValidatorTest extends TestCase
{
    public function test_validator_returns_true_when_all_rules_pass(): void
    {
        $validator = new CandidateValidator([
            new HasCvRule(),
            new ValidEmailRule(),
            new MinExperienceRule(2),
        ]);

        $candidate = new Candidate(
            id: 1,
            fullName: 'Senior Dev',
            email: new CandidateEmail('senior@example.com'),
            yearsExperience: new YearsOfExperience(5),
            cvText: 'Mucho CV',
            status: 'pending',
        );

        $this->assertTrue($validator->isValid($candidate));
    }

    public function test_validator_returns_false_when_some_rules_fail(): void
    {
        $validator = new CandidateValidator([
            new HasCvRule(),
            new ValidEmailRule(),
            new MinExperienceRule(2),
        ]);

        $candidate = new Candidate(
            id: 2,
            fullName: 'Junior Sin CV',
            email: new CandidateEmail('no-email'),
            yearsExperience: new YearsOfExperience(0),
            cvText: '',
            status: 'pending',
        );

        $this->assertFalse($validator->isValid($candidate));

        $results = $validator->validate($candidate);
        $this->assertCount(3, $results);
    }
}
