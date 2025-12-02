<?php

declare(strict_types=1);

use App\Application\UseCases\AssignEvaluatorHandler;
use App\Application\Contracts\AssignmentRepositoryInterface;
use App\Application\Contracts\CandidateRepositoryInterface;
use App\Application\Contracts\EvaluatorRepositoryInterface;
use App\Domain\Assignment\Entities\Assignment;
use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use App\Domain\Evaluator\Entities\Evaluator;

it('assigns evaluator to candidate using repositories', function () {
    $candidateRepo = mock(CandidateRepositoryInterface::class);
    $evaluatorRepo = mock(EvaluatorRepositoryInterface::class);
    $assignmentRepo = mock(AssignmentRepositoryInterface::class);

    $candidate = new Candidate(
        id: 1,
        fullName: 'Juan Candidato',
        email: new CandidateEmail('juan@example.com'),
        yearsExperience: new YearsOfExperience(3),
        cvText: 'CV',
        status: 'pending',
    );

    $evaluator = new Evaluator(
        id: 10,
        fullName: 'Eva Luadora',
        email: 'eva@example.com',
    );

    $candidateRepo->shouldReceive('findByIdOrFail')
        ->once()
        ->with(1)
        ->andReturn($candidate);

    $evaluatorRepo->shouldReceive('findByIdOrFail')
        ->once()
        ->with(10)
        ->andReturn($evaluator);

    $assignmentRepo->shouldReceive('assign')
        ->once()
        ->withArgs(function (Candidate $c, Evaluator $e): bool {
            return $c->id() === 1 && $e->id() === 10;
        })
        ->andReturn(new Assignment(
            id: 100,
            candidate: $candidate,
            evaluator: $evaluator,
            assignedAt: new DateTimeImmutable(),
        ));

    $handler = new AssignEvaluatorHandler(
        $candidateRepo,
        $evaluatorRepo,
        $assignmentRepo
    );

    $assignment = $handler->handle(1, 10);

    expect($assignment->id())->toBe(100);
});
