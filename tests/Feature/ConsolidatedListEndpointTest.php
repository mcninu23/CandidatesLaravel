<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Models\AssignmentModel;
use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ConsolidatedListEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_consolidated_list_with_aggregated_data(): void
    {
        $eva = EvaluatorModel::create([
            'full_name' => 'Eva Luadora',
            'email'     => 'eva@example.com',
        ]);

        $c1 = CandidateModel::create([
            'full_name'        => 'Candidato 1',
            'email'            => 'c1@example.com',
            'years_experience' => 3,
            'cv_text'          => 'CV1',
            'status'           => 'pending',
        ]);

        $c2 = CandidateModel::create([
            'full_name'        => 'Candidato 2',
            'email'            => 'c2@example.com',
            'years_experience' => 5,
            'cv_text'          => 'CV2',
            'status'           => 'pending',
        ]);

        AssignmentModel::create([
            'candidate_id' => $c1->id,
            'evaluator_id' => $eva->id,
            'assigned_at'  => now()->subDay(),
        ]);

        AssignmentModel::create([
            'candidate_id' => $c2->id,
            'evaluator_id' => $eva->id,
            'assigned_at'  => now(),
        ]);

        $response = $this->getJson('/api/candidates/consolidated');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertCount(2, $data);
        $this->assertSame('Eva Luadora', $data[0]['evaluator_name']);
        $this->assertSame(2, (int) $data[0]['total_candidates_by_evaluator']);
        $this->assertStringContainsString('c1@example.com', $data[0]['evaluator_candidate_emails']);
        $this->assertStringContainsString('c2@example.com', $data[0]['evaluator_candidate_emails']);
    }
}
