<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';

    protected $fillable = [
        'candidate_id',
        'evaluator_id',
        'assigned_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(CandidateModel::class, 'candidate_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(EvaluatorModel::class, 'evaluator_id');
    }
}
