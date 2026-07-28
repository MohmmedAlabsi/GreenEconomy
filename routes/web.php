<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\FeasibilityStudyController;
use App\Http\Controllers\FeasibilityRequestController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PlantDiseaseController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\FieldVisitController;
use App\Http\Controllers\PlatformSettingController;

// Auth Controller
/*
Route::get('/auth/me', [AuthController::class, 'me'])
    ->name('auth.me');
*/

    // User Controller
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('/users/{id}', [UserController::class, 'show'])
        ->name('users.show');
        // Region Controller
Route::get('/regions', [RegionController::class, 'index'])
    ->name('regions.index');
Route::get('/regions/{id}', [RegionController::class, 'show'])
    ->name('regions.show');

// Category Controller
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])
    ->name('categories.show');

// Specialization Controller
Route::get('/specializations', [SpecializationController::class, 'index'])
    ->name('specializations.index');
Route::get('/specializations/{id}', [SpecializationController::class, 'show'])
    ->name('specializations.show');

// Feasibility Study Controller
Route::get('/feasibility_studies', [FeasibilityStudyController::class, 'index'])
    ->name('feasibility-studies.index');
Route::get('/feasibility_studies/{id}', [FeasibilityStudyController::class, 'show'])
    ->name('feasibility-studies.show');

// Feasibility Request Controller
Route::get('/feasibility_requests', [FeasibilityRequestController::class, 'index'])
    ->name('feasibility-requests.index');
Route::get('/feasibility_requests/{id}', [FeasibilityRequestController::class, 'show'])
    ->name('feasibility-requests.show');

// Knowledge Base Controller
Route::get('/knowledge_base_item', [KnowledgeBaseController::class, 'index'])
    ->name('knowledge-base.index');
Route::get('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'show'])
    ->name('knowledge-base.show');

// Plant Controller
Route::get('/plants', [PlantController::class, 'index'])
    ->name('plants.index');
Route::get('/plants/{id}', [PlantController::class, 'show'])
    ->name('plants.show');

// Plant Disease Controller
Route::get('/plant_diseases', [PlantDiseaseController::class, 'index'])
    ->name('plant-diseases.index');
Route::get('/plant_diseases/{id}', [PlantDiseaseController::class, 'show'])
    ->name('plant-diseases.show');

// Consultation Controller
Route::get('/consultations', [ConsultationController::class, 'index'])
    ->name('consultations.index');
Route::get('/consultations/{id}', [ConsultationController::class, 'show'])
    ->name('consultations.show');

// Field Visit Controller
Route::get('/field_visits', [FieldVisitController::class, 'index'])
    ->name('field-visits.index');
Route::get('/field_visits/{id}', [FieldVisitController::class, 'show'])
    ->name('field-visits.show');

// Platform Setting Controller
Route::get('/platform_settings', [PlatformSettingController::class, 'index'])
    ->name('platform-settings.index');