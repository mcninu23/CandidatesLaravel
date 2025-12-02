<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\UseCases\AssignEvaluatorHandler;
use App\Http\Requests\AssignEvaluatorRequest;
use Illuminate\Http\JsonResponse;

class AssignmentController extends Controller
{
    /**
     * Asigna un evaluador a una candidatura.
     *
     * POST /api/candidates/{candidate}/assign-evaluator
     *
     * Body JSON:
     * {
     *   "evaluator_id": 1
     * }
     */
    public function assign(
        string $candidate,
        AssignEvaluatorRequest $request,
        AssignEvaluatorHandler $handler
    ): JsonResponse {
        // Datos ya validados por AssignEvaluatorRequest
        $validated = $request->validated();
        $evaluatorId = $validated['evaluator_id'];

        try {
            $assignment = $handler->handle($candidate, $evaluatorId);
        } catch (\RuntimeException $e) {
            // Por ejemplo, si el Candidate o Evaluator no existen
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'assignment_id'  => $assignment->id(),
            'candidate_id'   => $assignment->candidate()->id(),
            'evaluator_id'   => $assignment->evaluator()->id(),
            'evaluator_name' => $assignment->evaluator()->fullName(),
            'assigned_at'    => $assignment->assignedAt()->format(DATE_ATOM),
        ], 201);
    }
}
