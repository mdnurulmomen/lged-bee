<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('amms-support', [\App\Http\Controllers\HiddenSupportFeature::class, 'index']);
Route::post('get-fiscal-year-wise-activity', [\App\Http\Controllers\HiddenSupportFeature::class, 'getFiscalYearWiseActivity']);
Route::post('get-audit-plan-data', [\App\Http\Controllers\HiddenSupportFeature::class, 'getAuditPlanData']);
Route::post('annual-plan-approval-status', [\App\Http\Controllers\HiddenSupportFeature::class, 'annualPlanApprovalStatus']);
Route::post('office-order-approval-status', [\App\Http\Controllers\HiddenSupportFeature::class, 'officeOrderApprovalStatus']);
Route::post('audit-plan-delete', [\App\Http\Controllers\HiddenSupportFeature::class, 'auditPlanDelete']);

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    return "Cache is cleared";
});
