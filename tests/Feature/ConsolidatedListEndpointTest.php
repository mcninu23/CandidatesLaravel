<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use App\Infrastructure\Persistence\Eloquent\Models\AssignmentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns consolidated list with aggregated data per evaluator', function () {
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

    expect($data)->toHaveCount(2);

    // La agregación debe reflejar 2 candidatos por ese evaluador
    expect($data[0]['evaluator_name'])->toBe('Eva Luadora')
        ->and((int) $data[0]['total_candidates_by_evaluator'])->toBe(2)
        ->and($data[0]['evaluator_candidate_emails'])
            ->toContain('c1@example.com')
            ->toContain('c2@example.com');
});
