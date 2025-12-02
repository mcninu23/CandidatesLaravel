<?php

declare(strict_types=1);

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValidationRules\ValidEmailRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;

it('fails when email is invalid', function () {
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

    expect($result->ruleName)->toBe('valid_email')
        ->and($result->passed)->toBeFalse()
        ->and($result->message)->toBe('El email del candidato no es válido.');
});

it('passes when email is valid', function () {
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

    expect($result->passed)->toBeTrue();
});
