<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::post('login-in-amms', [UserController::class, 'loginInAmms'])->middleware('header.api.version');

Route::group(['middleware' => ['header.api.version']], function () {

    Route::customApiResource('fiscal-year', XFiscalYearController::class);

    Route::group(['prefix' => 'x-strategic-plan/'], function () {
        Route::customApiResource('duration', XStrategicPlanDurationController::class);

        Route::post('outcome/remarks', [XStrategicPlanOutcomeController::class, 'remarksByOutcomeId']);
        Route::customApiResource('outcome', XStrategicPlanOutcomeController::class);

        Route::post('output/by-outcome', [XStrategicPlanOutputController::class, 'outputByOutcome']);
        Route::customApiResource('output', XStrategicPlanOutputController::class);

        Route::customApiResource('required-capacity', XStrategicPlanRequiredCapacityController::class);
    });

    Route::customApiResource('responsible-offices', XResponsibleOfficeController::class);

    Route::group(['prefix' => 'operational-plan/'], function () {

        Route::post('activity/find', [OpActivityController::class, 'findActivities']);
        Route::customApiResource('activity', OpActivityController::class);

        Route::customApiResource('activity-milestone', OpActivityMilestoneController::class);

        Route::post('audit-calendar/calendar-activities', [OpYearlyAuditCalendarController::class, 'showCalendarActivities']);
        Route::post('audit-calendar/responsible/create', [OpYearlyAuditCalendarController::class, 'storeActivityResponsible']);
        Route::post('audit-calendar/milestones/date/update', [OpYearlyAuditCalendarController::class, 'storeMilestoneTargetDate']);
        Route::post('audit-calendar/comment/update', [OpYearlyAuditCalendarController::class, 'updateActivityComment']);
        Route::post('audit-calendar/change-status', [OpYearlyAuditCalendarController::class, 'changeStatus']);
        Route::post('audit-calendar/save-events-before-publish', [OpYearlyAuditCalendarController::class, 'saveEventsBeforePublishing']);

        Route::post('audit-calendar/movement/create', [OpYearlyAuditCalendarMovementController::class, 'store']);
        Route::post('audit-calendar/movement/history', [OpYearlyAuditCalendarMovementController::class, 'movementHistory']);

        Route::customApiResource('yearly-audit-calendar', OpYearlyAuditCalendarController::class);

        Route::post('list', [OperationalPlanController::class, 'OperationalPlan']);
        Route::post('details', [OperationalPlanController::class, 'OperationalDetail']);
    });
});
