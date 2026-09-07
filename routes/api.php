<?php

require __DIR__. '/auth.php';

use Illuminate\Http\Request;
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
use App\Http\Controllers\DiseaseTreatmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\FieldVisitController;
use App\Http\Controllers\PlatformSettingController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NotificationController; 
use App\Http\Controllers\EngineerProfileController;
use App\Http\Controllers\FieldVisitReportController;
use App\Http\Controllers\AdminEngineerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DraftController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public Routes (المسارات العامة)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

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
// أضف هذا المسار في قسم Public Routes
Route::get('/engineer_profiles', [EngineerProfileController::class, 'index'])->name('api.engineer-profiles.index');
Route::get('/stats', [HomeController::class, 'getStats']);

/*
|--------------------------------------------------------------------------
| Protected Routes (المسارات المحمية بتوكين Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
    Route::post('/user/password', [UserController::class, 'updatePassword']);


    // مسارات الإشعارات العامة لجميع المستخدمين المسجلين (مزارع، مهندس، مدير)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'send']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::match(['post', 'patch'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    
    // مسارات إدارة طلبات المهندسين (للأدمن)
    Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
        Route::get('/admin/engineer_join_requests', [AdminEngineerController::class, 'indexRequests']);
        Route::post('/admin/engineer_join_requests/{id}/approve', [AdminEngineerController::class, 'approveRequest']);
        Route::post('/admin/engineer_join_requests/{id}/reject', [AdminEngineerController::class, 'rejectRequest']);
    });

    Route::get('/drafts', [DraftController::class, 'show']);
    Route::post('/drafts', [DraftController::class, 'store']);
    Route::delete('/drafts', [DraftController::class, 'destroy']);

    // مسارات التقارير التشخيصية للزيارات الميدانية (محمية بالكامل)
    Route::get('/field_visit_reports', [FieldVisitReportController::class, 'index']);
    Route::post('/field_visits/{id}/report', [FieldVisitReportController::class, 'store']);

    Route::put('/users/{id}', [UserController::class, 'update']);
    
    // مسارات الأدمن الإدارية
    Route::middleware(['role:Admin'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::put('/platform_settings', [PlatformSettingController::class, 'update'])->name('api.platform-settings.update');
    });

    Route::middleware(['permission:manage consultations|answer consultations'])->group(function () {
        Route::apiResource('consultations', ConsultationController::class);
    });

    Route::middleware(['permission:manage feasibility studies|show feasibility studies'])->group(function () {
        Route::apiResource('feasibility_studies', FeasibilityStudyController::class);
        Route::post('feasibility_studies/{id}/update', [FeasibilityStudyController::class, 'update']);
    });

    Route::middleware(['permission:manage feasibility requests|create feasibility request|view own feasibility request'])->group(function () {
        Route::apiResource('feasibility_requests', FeasibilityRequestController::class);
    });

    Route::apiResource('plant_diseases', PlantDiseaseController::class);
    Route::apiResource('disease_treatments', DiseaseTreatmentController::class);
    Route::apiResource('plants', PlantController::class);

    // إدارة الزيارات الميدانية ومراحل الرحلة
    Route::apiResource('field_visits', FieldVisitController::class);
    Route::prefix('field_visits')->group(function () {
        Route::patch('{id}/assign', [FieldVisitController::class, 'assignEngineer']);
        Route::patch('{id}/estimate', [FieldVisitController::class, 'submitEstimate']);
        Route::post('{id}/report', [FieldVisitController::class, 'submitReport']);
        Route::post('{id}/rating', [FieldVisitController::class, 'submitRating']);
    });

    Route::middleware(['role:Admin|Agricultural Expert'])->group(function () {
        Route::post('/knowledge_base_item', [KnowledgeBaseController::class, 'store'])->name('api.knowledge-base.store');
        Route::put('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'update'])->name('api.knowledge-base.update');
        Route::delete('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'destroy'])->name('api.knowledge-base.destroy');
    });

    //Route::get('/engineer_profiles', [EngineerProfileController::class, 'show']);
    Route::post('/engineer_profiles', [EngineerProfileController::class, 'store']);
    Route::put('/engineer_profiles/{id}', [EngineerProfileController::class, 'update']);

    // المرفقات
    Route::post('/attachments', [AttachmentController::class, 'store'])->name('api.attachments.store');
    Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('api.attachments.show');
    Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy'])->name('api.attachments.destroy');
});