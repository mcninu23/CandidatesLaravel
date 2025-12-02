<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\UseCases\RegisterCandidateHandler;
use App\Http\Requests\RegisterCandidateRequest;
use Illuminate\Http\JsonResponse;
use App\Application\UseCases\ValidateCandidateHandler;
use App\Application\UseCases\GetCandidateSummaryHandler;
use Illuminate\Http\Request;


class CandidateController extends Controller
{
    /**
     * Registra una nueva candidatura.
     *
     * POST /api/candidates
     */
    public function store(
        RegisterCandidateRequest $request,
        RegisterCandidateHandler $handler
    ): JsonResponse {
        $data = $request->validated();

        try {
            $candidate = $handler->handle($data);
        } catch (\RuntimeException $e) {
            // Duplicado por email u otra regla de negocio
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'id'               => $candidate->id(),
            'full_name'        => $candidate->fullName(),
            'email'            => $candidate->email()->value(),
            'years_experience' => $candidate->yearsExperience()->value(),
            'cv_text'          => $candidate->cvText(),
            'status'           => $candidate->status(),
        ], 201);
    }

    /**
     * Valida una candidatura y devuelve las reglas pasadas/falladas.
     *
     * POST /api/candidates/{candidate}/validate
     */
    public function validateCandidate(
        string $candidate,
        Request $request,
        ValidateCandidateHandler $handler
    ): JsonResponse {
        // Por ahora no necesitamos body, pero podrías pasar flags si quisieras
        try {
            $result = $handler->handle($candidate);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json($result);
    }

    /**
     * Resumen completo de una candidatura:
     * - datos del candidato
     * - validaciones
     * - evaluador actual (si existe)
     * - histórico de asignaciones
     *
     * GET /api/candidates/{candidate}/summary
     */
    public function summary(
        string $candidate,
        GetCandidateSummaryHandler $handler
    ): JsonResponse {
        try {
            $summary = $handler->handle($candidate);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json($summary);
    }


}
