<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Models\AssignmentModel;
use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CandidateSummaryEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_candidate_summary_with_validation_and_evaluator(): void
    {
        $candidate = CandidateModel::create([
            'full_name'        => 'Laura Senior',
            'email'            => 'laura@example.com',
            'years_experience' => 5,
            'cv_text'          => 'CV de Laura',
            'status'           => 'pending',
        ]);

        $evaluator = EvaluatorModel::create([
            'full_name' => 'Eva Luadora',
            'email'     => 'eva@example.com',
        ]);

        AssignmentModel::create([
            'candidate_id' => $candidate->id,
            'evaluator_id' => $evaluator->id,
            'assigned_at'  => now(),
        ]);

        $response = $this->getJson("/api/candidates/{$candidate->id}/summary");

        $response->assertStatus(200)
            ->assertJsonPath('candidate.full_name', 'Laura Senior')
            ->assertJsonPath('evaluator.full_name', 'Eva Luadora')
            ->assertJsonPath('validation.is_valid', true)
            ->assertJsonStructure([
                'candidate' => [
                    'id',
                    'full_name',
                    'email',
                    'years_experience',
                    'cv_text',
                    'status',
                    'created_at',
                ],
                'validation' => [
                    'is_valid',
                    'results',
                ],
                'evaluator',
                'assignments',
            ]);
    }
}
