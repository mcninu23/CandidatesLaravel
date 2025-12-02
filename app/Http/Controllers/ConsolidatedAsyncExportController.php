<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidatedListRequest;
use App\Jobs\ExportConsolidatedCandidatesJob;
use Illuminate\Http\JsonResponse;

class ConsolidatedAsyncExportController extends Controller
{
    /**
     * Dispara la generación del Excel en background y notifica por email.
     *
     * POST /api/candidates/consolidated/export/async
     *
     * Body JSON:
     * {
     *   "email": "user@example.com",
     *   "full_name": "Juan",
     *   "evaluator_name": "Eva",
     *   ...
     * }
     */
    public function __invoke(ConsolidatedListRequest $request): JsonResponse 
    {
        $params  = $request->sanitized();

        $emailToNotify = $request->input('email');

        if (!$emailToNotify) {
            return response()->json([
                'message' => 'El campo "email" es obligatorio para notificar el resultado.',
            ], 422);
        }

        // Lanzamos el Job a la cola
        ExportConsolidatedCandidatesJob::dispatch(
            emailToNotify: $emailToNotify,
            filters: $params['filters'],
            orderBy: $params['order_by'],
            orderDir: $params['order_dir'],
        );

        return response()->json([
            'message' => 'Export request accepted. You will receive an email when it is ready.',
        ], 202);
    }
}
