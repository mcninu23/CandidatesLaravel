<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use App\Application\Contracts\CandidateRepositoryInterface;
use App\Application\Contracts\EvaluatorRepositoryInterface;
use App\Application\Contracts\AssignmentRepositoryInterface;
use App\Domain\Candidate\Services\CandidateValidator;
use App\Domain\Candidate\ValidationRules\HasCvRule;
use App\Domain\Candidate\ValidationRules\ValidEmailRule;
use App\Domain\Candidate\ValidationRules\MinExperienceRule;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCandidateRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentEvaluatorRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAssignmentRepository;
use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use App\Infrastructure\Persistence\Eloquent\Models\AssignmentModel;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });
        
        $this->app->bind(CandidateRepositoryInterface::class, function () {
            return new EloquentCandidateRepository(new CandidateModel());
        });

        $this->app->bind(EvaluatorRepositoryInterface::class, function () {
            return new EloquentEvaluatorRepository(new EvaluatorModel());
        });

        $this->app->bind(AssignmentRepositoryInterface::class, function () {
            return new EloquentAssignmentRepository(new AssignmentModel());
        });

        $this->app->bind(CandidateValidator::class, function () {
            return new CandidateValidator([
                new HasCvRule(),
                new ValidEmailRule(),
                new MinExperienceRule(2),
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}
