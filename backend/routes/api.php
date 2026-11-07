<?php

use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CollectionController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ResearcherController;
use App\Http\Controllers\Api\V1\SpecimenController;
use App\Http\Controllers\Api\V1\TaxonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Specimen Repository (Version 1)
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api/v1
| Protected routes require Sanctum token authentication.
|
*/

Route::prefix('v1')->group(function () {

    // -------------------------------------------------------------------------
    // Authentication Routes
    // -------------------------------------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // -------------------------------------------------------------------------
    // Protected Core System Routes (Requires Authentication)
    // -------------------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {

        // --- Biological Specimens ---
        Route::get('specimens', [SpecimenController::class, 'index']);
        Route::get('specimens/{id}', [SpecimenController::class, 'show']);
        Route::get('specimens/{id}/images', [SpecimenController::class, 'images']);
        Route::get('specimens/{id}/documents', [SpecimenController::class, 'documents']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('specimens', [SpecimenController::class, 'store']);
            Route::put('specimens/{id}', [SpecimenController::class, 'update']);
            Route::delete('specimens/{id}', [SpecimenController::class, 'destroy']);
            Route::post('specimens/{id}/images', [SpecimenController::class, 'uploadImage']);
            Route::post('specimens/{id}/documents', [SpecimenController::class, 'uploadDocument']);
        });

        // --- Taxonomy ---
        Route::get('taxa', [TaxonController::class, 'index']);
        Route::get('taxa/{id}', [TaxonController::class, 'show']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('taxa', [TaxonController::class, 'store']);
            Route::put('taxa/{id}', [TaxonController::class, 'update']);
            Route::delete('taxa/{id}', [TaxonController::class, 'destroy']);
        });

        // --- Collections ---
        Route::get('collections', [CollectionController::class, 'index']);
        Route::get('collections/{id}', [CollectionController::class, 'show']);
        Route::get('collections/{id}/specimens', [CollectionController::class, 'specimens']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('collections', [CollectionController::class, 'store']);
            Route::put('collections/{id}', [CollectionController::class, 'update']);
            Route::delete('collections/{id}', [CollectionController::class, 'destroy']);
        });

        // --- Researchers ---
        Route::get('researchers', [ResearcherController::class, 'index']);
        Route::get('researchers/{id}', [ResearcherController::class, 'show']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('researchers', [ResearcherController::class, 'store']);
            Route::put('researchers/{id}', [ResearcherController::class, 'update']);
            Route::delete('researchers/{id}', [ResearcherController::class, 'destroy']);
        });

        // --- Research Projects ---
        Route::get('projects', [ProjectController::class, 'index']);
        Route::get('projects/{id}', [ProjectController::class, 'show']);
        Route::get('projects/{id}/specimens', [ProjectController::class, 'specimens']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('projects', [ProjectController::class, 'store']);
            Route::put('projects/{id}', [ProjectController::class, 'update']);
            Route::delete('projects/{id}', [ProjectController::class, 'destroy']);
        });

        // --- Locations ---
        Route::get('locations', [LocationController::class, 'index']);
        Route::get('locations/{id}', [LocationController::class, 'show']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('locations', [LocationController::class, 'store']);
            Route::put('locations/{id}', [LocationController::class, 'update']);
            Route::delete('locations/{id}', [LocationController::class, 'destroy']);
        });

        // --- Specimen Loans ---
        Route::get('loans', [LoanController::class, 'index']);
        Route::post('loans', [LoanController::class, 'store']);
        Route::get('loans/{id}', [LoanController::class, 'show']);

        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('loans/{id}/approve', [LoanController::class, 'approve']);
            Route::post('loans/{id}/return', [LoanController::class, 'returnLoan']);
            Route::post('loans/{id}/cancel', [LoanController::class, 'cancel']);
        });

        // --- Analytical Reports (MongoDB Aggregations) ---
        Route::prefix('reports')->group(function () {
            Route::get('dashboard', [ReportController::class, 'dashboard']);
            Route::get('specimens', [ReportController::class, 'specimens']);
            Route::get('taxonomy', [ReportController::class, 'taxonomy']);
            Route::get('collections', [ReportController::class, 'collections']);
            Route::get('loans', [ReportController::class, 'loans']);
        });

        // --- Audit Logs ---
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::get('audit-logs', [AuditLogController::class, 'index']);
            Route::get('audit-logs/{id}', [AuditLogController::class, 'show']);
        });
    });
});
