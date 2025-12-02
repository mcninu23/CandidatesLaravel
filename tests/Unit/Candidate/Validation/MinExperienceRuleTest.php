<?php

declare(strict_types=1);

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\MinExperienceRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;

it('fails when years of experience are below minimum', function () {
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

    expect($result->ruleName)->toBe('min_experience')
        ->and($result->passed)->toBeFalse()
        ->and($result->message)->toBe('Debe tener al menos 2 años de experiencia.');
});

it('passes when years of experience meet the minimum', function () {
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

    expect($result->passed)->toBeTrue();
});
