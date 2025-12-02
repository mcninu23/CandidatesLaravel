<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\CandidateModel;
use App\Infrastructure\Persistence\Eloquent\Models\EvaluatorModel;
use App\Infrastructure\Persistence\Eloquent\Models\AssignmentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns candidate summary with validation and evaluator', function () {
    // Arrange: creamos candidate + evaluator + assignment en la DB real de test
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

    // Act
    $response = $this->getJson("/api/candidates/{$candidate->id}/summary");

    // Assert
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
                'results' => [
                    ['rule', 'passed', 'message'],
                ],
            ],
            'evaluator' => [
                'id',
                'full_name',
                'email',
            ],
            'assignments' => [
                ['id', 'evaluator_id', 'evaluator_name', 'assigned_at'],
            ],
        ]);
});
