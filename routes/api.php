<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DentistController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ProcedureController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::post('/auth/login/patient', [AuthController::class, 'loginPatient']);
Route::post('/auth/login/staff',   [AuthController::class, 'loginStaff']);

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout',       [AuthController::class, 'logout']);
    Route::get('/auth/me',            [AuthController::class, 'me']);
    Route::post('/auth/photo',        [AuthController::class, 'updatePhoto']);

    // ── Administrator only ──────────────────────────────────────────────────
    Route::middleware('role:Administrator')->prefix('admin')->group(function () {

        // Branches
        Route::get('/branches',             [BranchController::class, 'index']);
        Route::post('/branches',            [BranchController::class, 'store']);
        Route::post('/branches/state',      [BranchController::class, 'changeState']);

        // Dentists
        Route::get('/dentists',             [DentistController::class, 'index']);
        Route::post('/dentists',            [DentistController::class, 'store']);
        Route::get('/dentists/{dentist}',   [DentistController::class, 'show']);
        Route::post('/dentists/state',      [DentistController::class, 'changeState']);
        Route::get('/dentists/{dentist}/schedule', [DentistController::class, 'getSchedule']);
        Route::post('/dentists/schedule',   [DentistController::class, 'storeSchedule']);

        // Patients
        Route::get('/patients',             [PatientController::class, 'index']);
        Route::post('/patients',            [PatientController::class, 'store']);
        Route::get('/patients/{patient}',   [PatientController::class, 'show']);
        Route::post('/patients/deactivate', [PatientController::class, 'deactivate']);
        Route::get('/patients/select',      [PatientController::class, 'select']);
        Route::post('/patients/find-by-document', [PatientController::class, 'findByDocument']);

        // Procedures
        Route::get('/procedures',           [ProcedureController::class, 'index']);
        Route::post('/procedures',          [ProcedureController::class, 'store']);
        Route::post('/procedures/state',    [ProcedureController::class, 'changeState']);

        // Appointments (admin)
        Route::get('/appointments',         [AppointmentController::class, 'index']);
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
        Route::post('/appointments',        [AppointmentController::class, 'store']);
        Route::post('/appointments/state',  [AppointmentController::class, 'changeState']);
        Route::post('/appointments/delete', [AppointmentController::class, 'delete']);
        Route::post('/appointments/whatsapp', [AppointmentController::class, 'markWhatsapp']);
        Route::post('/appointments/phone',  [AppointmentController::class, 'markPhone']);
        Route::get('/appointments/by-document', [AppointmentController::class, 'byDocument']);
        Route::get('/appointments/by-patient',  [AppointmentController::class, 'byPatient']);

        // Products
        Route::get('/products',             [ProductController::class, 'index']);
        Route::post('/products',            [ProductController::class, 'store']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Promotions
        Route::get('/promotions',           [PromotionController::class, 'index']);
        Route::post('/promotions',          [PromotionController::class, 'store']);
        Route::post('/promotions/deactivate', [PromotionController::class, 'deactivate']);

        // Reports
        Route::get('/reports/staff',        [ReportController::class, 'staffGraph']);
        Route::get('/reports/billing',      [ReportController::class, 'billingByPatient']);
    });

    // ── Administrator + Dentist ────────────────────────────────────────────────
    Route::middleware('role:Administrator,Dentist')->prefix('staff')->group(function () {

        Route::get('/branches/select',       [BranchController::class, 'select']);
        Route::get('/procedures/select',     [ProcedureController::class, 'select']);
        Route::get('/dentists/select',       [DentistController::class, 'select']);

        Route::post('/appointments/form-data',  [AppointmentController::class, 'formData']);
        Route::post('/appointments/slots',      [AppointmentController::class, 'availableSlots']);
        Route::post('/appointments/by-procedure', [AppointmentController::class, 'dentistsByProcedure']);
    });

    // ── Dentist only ───────────────────────────────────────────────────────────
    Route::middleware('role:Dentist')->prefix('dentist')->group(function () {

        Route::get('/schedule',             [DentistController::class, 'getSchedule']);
        Route::post('/schedule',            [DentistController::class, 'storeSchedule']);
        Route::get('/appointments',         [DentistController::class, 'myAppointments']);
        Route::post('/appointments/state',  [AppointmentController::class, 'changeState']);
    });

    // ── Patient only ───────────────────────────────────────────────────────────
    Route::middleware('role:Patient')->prefix('patient')->group(function () {

        Route::get('/me',                       [PatientController::class, 'me']);
        Route::post('/appointments',            [AppointmentController::class, 'store']);
        Route::get('/appointments',             [AppointmentController::class, 'byPatient']);
        Route::post('/appointments/cancel',     [AppointmentController::class, 'cancel']);
        Route::post('/appointments/form-data',  [AppointmentController::class, 'formData']);
        Route::post('/appointments/slots',      [AppointmentController::class, 'availableSlots']);
        Route::post('/appointments/by-procedure', [AppointmentController::class, 'dentistsByProcedure']);
    });
});
