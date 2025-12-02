<?php

declare(strict_types=1);

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\HasCvRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;

it('fails when candidate has empty CV', function () {
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

    expect($result->ruleName)->toBe('has_cv')
        ->and($result->passed)->toBeFalse()
        ->and($result->message)->toBe('El candidato debe aportar CV.');
});

it('passes when candidate has non empty CV', function () {
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

    expect($result->ruleName)->toBe('has_cv')
        ->and($result->passed)->toBeTrue()
        ->and($result->message)->toBeNull();
});
