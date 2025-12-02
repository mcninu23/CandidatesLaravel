<?php

declare(strict_types=1);

use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\Services\CandidateValidator;
use App\Domain\Candidate\ValidationRules\HasCvRule;
use App\Domain\Candidate\ValidationRules\ValidEmailRule;
use App\Domain\Candidate\ValidationRules\MinExperienceRule;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;

it('aggregates all rules and returns overall validity', function () {
    $validator = new CandidateValidator([
        new HasCvRule(),
        new ValidEmailRule(),
        new MinExperienceRule(2),
    ]);

    $candidateOk = new Candidate(
        id: 1,
        fullName: 'Senior Dev',
        email: new CandidateEmail('senior@example.com'),
        yearsExperience: new YearsOfExperience(5),
        cvText: 'Mucho CV',
        status: 'pending',
    );

    $candidateKo = new Candidate(
        id: 2,
        fullName: 'Junior Sin CV',
        email: new CandidateEmail('no-email'),
        yearsExperience: new YearsOfExperience(0),
        cvText: '',
        status: 'pending',
    );

    // Candidato OK → todas las reglas pasan
    $resultsOk = $validator->validate($candidateOk);
    expect($resultsOk)->toHaveCount(3)
        ->and($validator->isValid($candidateOk))->toBeTrue();

    // Candidato KO → varias reglas fallan
    $resultsKo = $validator->validate($candidateKo);
    expect($resultsKo)->toHaveCount(3)
        ->and($validator->isValid($candidateKo))->toBeFalse();
});
