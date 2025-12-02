<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use App\Application\Contracts\AssignmentRepositoryInterface;
use App\Application\Contracts\CandidateRepositoryInterface;
use App\Application\Contracts\EvaluatorRepositoryInterface;
use App\Application\UseCases\AssignEvaluatorHandler;
use App\Domain\Assignment\Entities\Assignment;
use App\Domain\Candidate\Entities\Candidate;
use App\Domain\Candidate\ValueObjects\CandidateEmail;
use App\Domain\Candidate\ValueObjects\YearsOfExperience;
use App\Domain\Evaluator\Entities\Evaluator;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class AssignEvaluatorHandlerTest extends MockeryTestCase
{
    public function test_assigns_evaluator_to_candidate(): void
    {
        $candidateRepo  = Mockery::mock(CandidateRepositoryInterface::class);
        $evaluatorRepo  = Mockery::mock(EvaluatorRepositoryInterface::class);
        $assignmentRepo = Mockery::mock(AssignmentRepositoryInterface::class);

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

        $assignmentRepo->shouldReceive('createAssignment')
            ->once()
            ->withArgs(function (Candidate $c, Evaluator $e, DateTimeImmutable $assignedAt): bool {
                return $c->id() === 1
                    && $e->id() === 10
                    && $assignedAt instanceof DateTimeImmutable;
            })
            ->andReturn(
                new Assignment(
                    id: 100,
                    candidate: $candidate,
                    evaluator: $evaluator,
                    assignedAt: new DateTimeImmutable(),
                )
            );


        $handler = new AssignEvaluatorHandler(
            $candidateRepo,
            $evaluatorRepo,
            $assignmentRepo
        );

        $assignment = $handler->handle(1, 10);

        $this->assertSame(100, $assignment->id());
    }
}
