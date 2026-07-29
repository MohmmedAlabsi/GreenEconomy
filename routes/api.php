<?php

use Illuminate\Support\Facades\Route;

// استدعاء المتحكمات
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

/*
|--------------------------------------------------------------------------
| API Routes - Read Only Operations
|--------------------------------------------------------------------------
*/

// Auth
Route::get('/auth/me', [AuthController::class, 'me'])
    ->name('api.auth.me');

// Users
Route::get('/users', [UserController::class, 'index'])
    ->name('api.users.index');
Route::get('/users/{id}', [UserController::class, 'show'])
    ->name('api.users.show');

    // Users
Route::post('/users', [UserController::class, 'store'])
    ->name('api.users.store');
Route::put('/users/{id}', [UserController::class, 'update'])
    ->name('api.users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->name('api.users.destroy');

// Regions
Route::get('/regions', [RegionController::class, 'index'])
    ->name('api.regions.index');
Route::get('/regions/{id}', [RegionController::class, 'show'])
    ->name('api.regions.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])
    ->name('api.categories.show');

// Specializations
Route::get('/specializations', [SpecializationController::class, 'index'])
    ->name('api.specializations.index');
Route::get('/specializations/{id}', [SpecializationController::class, 'show'])
    ->name('api.specializations.show');

// Feasibility Studies
Route::get('/feasibility_studies', [FeasibilityStudyController::class, 'index'])
    ->name('api.feasibility-studies.index');
Route::get('/feasibility_studies/{id}', [FeasibilityStudyController::class, 'show'])
    ->name('api.feasibility-studies.show');

// Feasibility Requests
Route::get('/feasibility_requests', [FeasibilityRequestController::class, 'index'])
    ->name('api.feasibility-requests.index');
Route::get('/feasibility_requests/{id}', [FeasibilityRequestController::class, 'show'])
    ->name('api.feasibility-requests.show');

// Knowledge Base
Route::get('/knowledge_base_item', [KnowledgeBaseController::class, 'index'])
    ->name('api.knowledge-base.index');
Route::get('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'show'])
    ->name('api.knowledge-base.show');

// Plants
Route::get('/plants', [PlantController::class, 'index'])
    ->name('api.plants.index');
Route::get('/plants/{id}', [PlantController::class, 'show'])
    ->name('api.plants.show');

// Plant Diseases
Route::get('/plant_diseases', [PlantDiseaseController::class, 'index'])
    ->name('api.plant-diseases.index');
Route::get('/plant_diseases/{id}', [PlantDiseaseController::class, 'show'])
    ->name('api.plant-diseases.show');

// Consultations
Route::get('/consultations', [ConsultationController::class, 'index'])
    ->name('api.consultations.index');
Route::get('/consultations/{id}', [ConsultationController::class, 'show'])
    ->name('api.consultations.show');

Route::post('/consultations', [ConsultationController::class, 'store'])
    ->name('api.consultations.store');

Route::put('/consultations/{id}', [ConsultationController::class, 'update'])
    ->name('api.consultations.update');

Route::delete('/consultations/{id}', [ConsultationController::class, 'destroy'])
    ->name('api.consultations.destroy');

// Field Visits
Route::get('/field_visits', [FieldVisitController::class, 'index'])
    ->name('api.field-visits.index');
Route::get('/field_visits/{id}', [FieldVisitController::class, 'show'])
    ->name('api.field-visits.show');

// Platform Settings
Route::get('/platform_settings', [PlatformSettingController::class, 'index'])
    ->name('api.platform-settings.index');