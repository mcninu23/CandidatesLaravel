<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ConsolidatedController;
use App\Http\Controllers\ConsolidatedExportController;
use App\Http\Controllers\ConsolidatedAsyncExportController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => 'pong');

Route::post('/candidates', [CandidateController::class, 'store']);
Route::get('/candidates/consolidated', ConsolidatedController::class);
Route::get('/candidates/consolidated/export', ConsolidatedExportController::class);
Route::post('/candidates/consolidated/export/async', ConsolidatedAsyncExportController::class);
Route::post('/candidates/{candidate}/validate', [CandidateController::class, 'validateCandidate']);
Route::get('/candidates/{candidate}/summary', [CandidateController::class, 'summary']);
Route::post('/candidates/{candidate}/assign-evaluator', [AssignmentController::class, 'assign']);