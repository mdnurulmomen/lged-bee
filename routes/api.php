<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::post('login-in-amms', [LoginController::class, 'loginInAmms'])->middleware('header.api.version');

Route::post('client-login', [LoginController::class, 'clientLogin']);

Route::group(['middleware' => ['header.api.version', 'auth.jwt']], function () {

    Route::customApiResource('fiscal-year', XFiscalYearController::class);
    Route::customApiResource('responsible-offices', XResponsibleOfficeController::class);
    Route::group(['prefix' => 'x-strategic-plan/'], function () {
        Route::customApiResource('duration', XStrategicPlanDurationController::class);

        Route::post('outcome/remarks', [XStrategicPlanOutcomeController::class, 'remarksByOutcomeId']);
        Route::customApiResource('outcome', XStrategicPlanOutcomeController::class);

        Route::post('output/by-outcome', [XStrategicPlanOutputController::class, 'outputByOutcome']);
        Route::customApiResource('output', XStrategicPlanOutputController::class);

        Route::customApiResource('required-capacity', XStrategicPlanRequiredCapacityController::class);
    });

    Route::group(['prefix' => 'planning/'], function () {
        Route::group(['prefix' => 'operational-plan/'], function () {

            Route::post('activity/find', [OpActivityController::class, 'findActivities']);
            Route::post('activity/all-by-fiscal-year', [OpActivityController::class, 'showActivitiesByFiscalYear']);
            Route::post('activity/milestones', [OpActivityController::class, 'showActivityMilestones']);
            Route::customApiResource('activity', OpActivityController::class);

            Route::customApiResource('activity-milestone', OpActivityMilestoneController::class);

            Route::post('audit-calendar/calendar-activities', [OpYearlyAuditCalendarController::class, 'showCalendarActivities']);
            Route::post('audit-calendar/responsible/create', [OpYearlyAuditCalendarController::class, 'storeActivityResponsible']);
            Route::post('audit-calendar/milestones/date/update', [OpYearlyAuditCalendarController::class, 'storeMilestoneTargetDate']);
            Route::post('audit-calendar/comment/update', [OpYearlyAuditCalendarController::class, 'updateActivityComment']);
            Route::post('audit-calendar/change-status', [OpYearlyAuditCalendarController::class, 'changeStatus']);
            Route::post('audit-calendar/pending-events-for-publish', [OpYearlyAuditCalendarController::class, 'pendingEventsForPublishing']);
            Route::post('audit-calendar/publish-event-as-calendar', [OpYearlyAuditCalendarController::class, 'publishCalendar']);
            Route::post('audit-calendar/movement/create', [OpYearlyAuditCalendarMovementController::class, 'store']);
            Route::post('audit-calendar/movement/history', [OpYearlyAuditCalendarMovementController::class, 'movementHistory']);

            Route::post('audit-calendar/years-to-create', [OpYearlyAuditCalendarController::class, 'yearsToCreateCalendar']);
            Route::customApiResource('audit-calendar', OpYearlyAuditCalendarController::class);

            Route::post('list', [OperationalPlanController::class, 'OperationalPlan']);
            Route::post('details', [OperationalPlanController::class, 'OperationalDetail']);
        });

//        Route::group(['prefix' => 'annual-plan/'], function () {
//            Route::post('all', [ApOrganizationYearlyPlanController::class, 'allAnnualPlan']);
//
//            Route::post('rp-entities/all-added', [ApOrganizationYearlyPlanController::class, 'allSelectedRPEntities']);
//            Route::post('rp-entities/store', [ApOrganizationYearlyPlanController::class, 'storeSelectedRPEntities']);
//
//            Route::post('plan-submission/create', [ApOrganizationYearlyPlanController::class, 'storePlanAssignedDetails']);
//
//            Route::post('submit-plan-to-ocag', [ApOrganizationYearlyPlanController::class, 'submitToOCAG']);
//        });

        Route::group(['prefix' => 'annual-plan/'], function () {
            Route::post('all', [AnnualPlanRevisedController::class, 'allAnnualPlan']);

            Route::post('create', [AnnualPlanRevisedController::class, 'storeAnnualPlan']);

            Route::post('book', [AnnualPlanRevisedController::class, 'exportAnnualPlan']);

            Route::post('submit-plan-to-ocag', [AnnualPlanRevisedController::class, 'submitToOCAG']);

            Route::post('show', [AnnualPlanRevisedController::class, 'showAnnualPlan']);
        });

        Route::group(['prefix' => 'audit-plan'], function () {
            Route::post('entity-audit-plan-lists', [ApEntityAuditPlanController::class, 'index']);
            Route::customApiResource('entity-audit-plan', ApEntityAuditPlanController::class);
        });

        Route::group(['prefix' => 'strategic-plan'], function () {
            Route::customApiResource('outcome-indicators', OutcomeIndicatorController::class);
            Route::customApiResource('output-indicators', OutputIndicatorController::class);
            Route::post('outcome-indicators/all', [OutcomeIndicatorController::class, 'outcomes']);
            Route::post('output-indicators/all', [OutputIndicatorController::class, 'outputs']);
        });
    });

    Route::group(['prefix' => 'follow-up/'], function () {
        Route::customApiResource('audit-observations', AuditObservationController::class);
        Route::group(['prefix' => 'audit-observation/'], function () {
            Route::post('search', [AuditObservationController::class, 'search']);
            Route::post('remove_attachment', [AuditObservationController::class, 'removeAttachment']);
            Route::post('get_audit_plan', [AuditObservationController::class, 'getAuditPlan']);
            Route::post('observation_communication', [AuditObservationController::class, 'observationCommunication']);
            Route::post('observation_communication_lists', [AuditObservationController::class, 'observationCommunicationLists']);
        });
    });

    Route::post('audit-template/show', [AuditTemplateController::class, 'show']);

    //todo
    Route::post('final-plan-file-upload', [FinalPlanController::class, 'store']);
    Route::post('final-plan-file-edit', [FinalPlanController::class, 'edit']);
    Route::post('final-plan-file-update', [FinalPlanController::class, 'update']);
    Route::post('final-plan-file-list', [FinalPlanController::class, 'list']);
    Route::post('final-plan-document-is-exist', [FinalPlanController::class, 'documentIsExist']);

    Route::post('sp-setting-list', [StrategicSettingPlanController::class, 'list']);
    Route::post('sp-setting-store', [StrategicSettingPlanController::class, 'store']);

});
