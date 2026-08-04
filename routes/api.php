<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// استدعاء المتحكمات (Controllers)
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
use App\Http\Controllers\DiseaseTreatmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\FieldVisitController;
use App\Http\Controllers\PlatformSettingController;
use App\Http\Controllers\AttachmentController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public Routes (المسارات العامة - لا تتطلب تسجيل دخول)
|--------------------------------------------------------------------------
*/

// المصادقة (Auth Public)
Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('login'); // ضروري لمنع خطأ Route [login] not defined

// القواميس والمعلومات العامة (Read-Only)
Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('api.categories.show');

Route::get('/regions', [RegionController::class, 'index'])->name('api.regions.index');
Route::get('/regions/{id}', [RegionController::class, 'show'])->name('api.regions.show');

Route::get('/specializations', [SpecializationController::class, 'index'])->name('api.specializations.index');
Route::get('/specializations/{id}', [SpecializationController::class, 'show'])->name('api.specializations.show');

Route::get('/plants', [PlantController::class, 'index'])->name('api.plants.index');
Route::get('/plants/{id}', [PlantController::class, 'show'])->name('api.plants.show');

Route::get('/knowledge_base_item', [KnowledgeBaseController::class, 'index'])->name('api.knowledge-base.index');
Route::get('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'show'])->name('api.knowledge-base.show');

Route::get('/platform_settings', [PlatformSettingController::class, 'index'])->name('api.platform-settings.index');


/*
|--------------------------------------------------------------------------
| Protected Routes (المسارات المحمية - تتطلب توكين Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // إدارة الجلسة والمستخدم الحالي
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
    
    // إدارة المرفقات (Attachments)
    Route::post('/attachments', [AttachmentController::class, 'store'])->name('api.attachments.store');
    Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('api.attachments.show');
    Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy'])->name('api.attachments.destroy');

Route::post('/feasibility_studies', [FeasibilityStudyController::class, 'store'])
    ->name('api.feasibility-studies.store');

Route::put('/feasibility_studies/{id}', [FeasibilityStudyController::class, 'update'])
    ->name('api.feasibility-studies.update');

Route::delete('/feasibility_studies/{id}', [FeasibilityStudyController::class, 'destroy'])
    ->name('api.feasibility-studies.destroy');

// Feasibility Requests
Route::get('/feasibility_requests', [FeasibilityRequestController::class, 'index'])
    ->name('api.feasibility-requests.index');
Route::get('/feasibility_requests/{id}', [FeasibilityRequestController::class, 'show'])
    ->name('api.feasibility-requests.show');

Route::post('/feasibility_requests', [FeasibilityRequestController::class, 'store'])
    ->name('api.feasibility-requests.store');

Route::put('/feasibility_requests/{id}', [FeasibilityRequestController::class, 'update'])
    ->name('api.feasibility-requests.update');

Route::delete('/feasibility_requests/{id}', [FeasibilityRequestController::class, 'destroy'])
    ->name('api.feasibility-requests.destroy');

// Knowledge Base
Route::get('/knowledge_base_item', [KnowledgeBaseController::class, 'index'])
    ->name('api.knowledge-base.index');
Route::get('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'show'])
    ->name('api.knowledge-base.show');

Route::post('/knowledge_base_item', [KnowledgeBaseController::class, 'store'])
    ->name('api.knowledge-base.store');

Route::put('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'update'])
    ->name('api.knowledge-base.update');

Route::delete('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'destroy'])
    ->name('api.knowledge-base.destroy');
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

    // Disease Treatments
Route::get('/disease_treatments', [DiseaseTreatmentController::class, 'index'])
    ->name('api.disease-treatments.index');

Route::post('/disease_treatments', [DiseaseTreatmentController::class, 'store'])
    ->name('api.disease-treatments.store');

Route::get('/disease_treatments/{id}', [DiseaseTreatmentController::class, 'show'])
    ->name('api.disease-treatments.show');

Route::put('/disease_treatments/{id}', [DiseaseTreatmentController::class, 'update'])
    ->name('api.disease-treatments.update');

Route::delete('/disease_treatments/{id}', [DiseaseTreatmentController::class, 'destroy'])
    ->name('api.disease-treatments.destroy');

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

Route::post('/field_visits', [FieldVisitController::class, 'store'])
    ->name('api.field-visits.store');

Route::put('/field_visits/{id}', [FieldVisitController::class, 'update'])
    ->name('api.field-visits.update');

Route::delete('/field_visits/{id}', [FieldVisitController::class, 'destroy'])
    ->name('api.field-visits.destroy');
// Platform Settings
Route::get('/platform_settings', [PlatformSettingController::class, 'index'])
    ->name('api.platform-settings.index');


});