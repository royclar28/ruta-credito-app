<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

// Autenticación y Switcher de Perfil Operativo
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/switch-user/{userId}', [AuthController::class, 'switchUser'])->name('switch_user');

// Rutas Protegidas del Sistema de Ruta de Crédito
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // 1. Dashboard Ejecutivo y Métricas Operativas
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/metrics', [DashboardController::class, 'apiMetrics'])->name('api.metrics');

    // 2. Bandejas de Trabajo y Gestión Operativa
    Route::get('/bandejas/{queue?}', [WorkflowController::class, 'index'])->name('workflow.index');
    Route::get('/solicitudes/{id}/detalles', [WorkflowController::class, 'details'])->name('workflow.details');
    Route::post('/solicitudes/transicion', [WorkflowController::class, 'transition'])->name('workflow.transition');
    Route::post('/solicitudes/transicion-masiva', [WorkflowController::class, 'batchTransition'])->name('workflow.batch_transition');

    // 3. Consulta Unificada y Auditoría Histórica
    Route::get('/consulta', [ConsultationController::class, 'index'])->name('consultation.index');

    // 4. Centro de Reportería y Exportaciones
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reportes/exportar-csv', [ReportController::class, 'exportCsv'])->name('reports.export_csv');
    Route::get('/reportes/expediente/{id}', [ReportController::class, 'printSummary'])->name('reports.print_summary');
});
