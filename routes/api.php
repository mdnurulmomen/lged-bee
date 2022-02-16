<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Followup\BroadsheetReplyController;
use App\Http\Controllers\Report\AuditAIRReportController;
use App\Http\Controllers\Report\RpuAirReportController;
use Illuminate\Support\Facades\Route;

Route::post('login-in-amms', [LoginController::class, 'loginInAmms'])->middleware('header.api.version');

Route::post('client-login', [LoginController::class, 'clientLogin']);

Route::group(['middleware' => ['header.api.version', 'auth.jwt']], function () {

    Route::customApiResource('fiscal-year', XFiscalYearController::class);
    Route::customApiResource('audit-query', XAuditQueryController::class);
    Route::customApiResource('risk-assessment', XRiskAssessmentController::class);
    Route::post('cost-center-type-wise-query', [XAuditQueryController::class, 'costCenterTypeWiseQuery']);
    Route::customApiResource('cost-center-type', XCostCenterTypeController::class);
    Route::post('directorates/all', [XResponsibleOfficeController::class, 'allDirectorates']);
    Route::customApiResource('responsible-offices', XResponsibleOfficeController::class);
    Route::group(['prefix' => 'x-strategic-plan/'], function () {
        Route::customApiResource('duration', XStrategicPlanDurationController::class);

        Route::post('outcome/remarks', [XStrategicPlanOutcomeController::class, 'remarksByOutcomeId']);
        Route::customApiResource('outcome', XStrategicPlanOutcomeController::class);

        Route::post('output/by-outcome', [XStrategicPlanOutputController::class, 'outputByOutcome']);
        Route::customApiResource('output', XStrategicPlanOutputController::class);

        Route::customApiResource('required-capacity', XStrategicPlanRequiredCapacityController::class);
    });

    Route::customApiResource('audit-assessment-criteria', XAuditAssessmentCriteriaController::class);
    Route::post('audit-assessment-criteria/list-category-wise', [XAuditAssessmentCriteriaController::class, 'loadCategoryWiseCriteriaList']);


    Route::group(['prefix' => 'audit-assessment-score/'], function () {
        Route::post('list', [AuditAssessmentScoreController::class, 'list']);
        Route::post('store', [AuditAssessmentScoreController::class, 'store']);
        Route::post('edit', [AuditAssessmentScoreController::class, 'edit']);
    });

    Route::group(['prefix' => 'audit-assessment/'], function () {
        Route::post('list', [AuditAssessmentController::class, 'list']);
        Route::post('store', [AuditAssessmentController::class, 'store']);
        Route::post('get-assessment-entity', [AuditAssessmentController::class, 'getAssessmentEntity']);
        Route::post('store-annual-plan', [AuditAssessmentController::class, 'storeAnnualPlan']);
    });

    Route::group(['prefix' => 'planning/'], function () {
        Route::group(['prefix' => 'operational-plan/'], function () {

            Route::post('activity/find', [OpActivityController::class, 'findActivities']);
            Route::post('activity/all-by-fiscal-year', [OpActivityController::class, 'showActivitiesByFiscalYear']);
            Route::post('activity/milestones', [OpActivityController::class, 'showActivityMilestones']);
            Route::post('activity/get-all-activity', [OpActivityController::class, 'getAllActivity']);
            Route::post('activity/activity-wise-audit-plan', [OpActivityController::class, 'getActivityWiseAuditPlan']);
            Route::customApiResource('activity', OpActivityController::class);

            Route::customApiResource('activity-milestone', OpActivityMilestoneController::class);

            Route::post('audit-calendar/yearly-event-list', [OpYearlyAuditCalendarController::class, 'yearlyAuditCalendarEventList']);

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

            Route::post('send-annual-plan-receiver-to-sender', [AnnualPlanRevisedController::class, 'sendAnnualPlanReceiverToSender']);
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

            Route::post('update', [AnnualPlanRevisedController::class, 'updateAnnualPlan']);

            Route::post('book', [AnnualPlanRevisedController::class, 'exportAnnualPlan']);

            Route::post('submit-plan-to-ocag', [AnnualPlanRevisedController::class, 'submitToOCAG']);

            Route::post('show', [AnnualPlanRevisedController::class, 'showAnnualPlan']);

            Route::post('show-entities', [AnnualPlanRevisedController::class, 'showAnnualPlanEntities']);

            Route::post('get-annual-plan-info', [AnnualPlanRevisedController::class, 'getAnnualPlanInfo']);

            Route::post('delete-annual-plan', [AnnualPlanRevisedController::class, 'deleteAnnualPlan']);

            Route::post('get-nominated-offices', [AnnualPlanRevisedController::class, 'showNominatedOffices']);

            Route::post('send-annual-plan-sender-to-receiver', [AnnualPlanRevisedController::class, 'sendAnnualPlanSenderToReceiver']);
            Route::post('get-movement-histories', [AnnualPlanRevisedController::class, 'getMovementHistories']);
            Route::post('get-current-desk-approval-authority', [AnnualPlanRevisedController::class, 'getCurrentDeskApprovalAuthority']);
            Route::post('submit-milestone-value', [AnnualPlanRevisedController::class, 'submitMilestoneValue']);
            Route::post('get-schedule-info', [AnnualPlanRevisedController::class, 'getScheduleInfo']);
            Route::post('get-annual-plan-subject-matter-info', [AnnualPlanRevisedController::class, 'getAnnualPlanSubjectMatterInfo']);
        });

        Route::group(['prefix' => 'audit-plan'], function () {
//            Route::customApiResource('entity-audit-plan', ApEntityAuditPlanController::class);
            Route::post('entity-audit-plan/new', [ApEntityAuditPlanRevisedController::class, 'createNewAuditPlan']);
            Route::post('entity-audit-plan/edit', [ApEntityAuditPlanRevisedController::class, 'editAuditPlan']);
            Route::post('entity-audit-plan/get-audit-plan-wise-team', [ApEntityAuditPlanRevisedController::class, 'getAuditPlanWiseTeam']);
            Route::post('entity-audit-plan/get-team-info', [ApEntityAuditPlanRevisedController::class, 'getTeamInfo']);
            Route::customApiResource('entity-audit-plan', ApEntityAuditPlanRevisedController::class);

            Route::post('entity-audit-plan/audit-team/store', [ApEntityAuditPlanRevisedController::class, 'storeAuditTeam']);
            Route::post('entity-audit-plan/audit-team/update', [ApEntityAuditPlanRevisedController::class, 'updateAuditTeam']);
            Route::post('entity-audit-plan/audit-team/store-team-schedule', [ApEntityAuditPlanRevisedController::class, 'storeTeamSchedule']);
            Route::post('entity-audit-plan/audit-team/update-team-schedule', [ApEntityAuditPlanRevisedController::class, 'updateTeamSchedule']);
            Route::post('entity-audit-plan/audit-team/previously-assigned-designations', [ApEntityAuditPlanRevisedController::class, 'previouslyAssignedDesignations']);

            Route::post('office-order/audit-plan-list', [ApOfficeOrderController::class, 'auditPlanList']);
            Route::post('office-order/generate', [ApOfficeOrderController::class, 'generateOfficeOrder']);
            Route::post('office-order/show', [ApOfficeOrderController::class, 'showOfficeOrder']);
            Route::post('office-order/store-approval-authority', [ApOfficeOrderController::class, 'storeOfficeOrderApprovalAuthority']);
            Route::post('office-order/approve', [ApOfficeOrderController::class, 'approveOfficeOrder']);

            //data collection office order
            Route::post('office-order-dc/annual-plan-list', [ApDcOfficeOrderController::class, 'annualPlanList']);
            Route::post('office-order-dc/generate', [ApDcOfficeOrderController::class, 'generateOfficeOrder']);
            Route::post('office-order-dc/show', [ApDcOfficeOrderController::class, 'showOfficeOrder']);
            Route::post('office-order-dc/store-approval-authority', [ApDcOfficeOrderController::class, 'storeOfficeOrderApprovalAuthority']);
            Route::post('office-order-dc/approve', [ApDcOfficeOrderController::class, 'approveOfficeOrder']);

            Route::post('risk-assessment/type-wise-item-list', [ApRiskAssessmentController::class, 'riskAssessmentTypeWiseItemList']);
            Route::post('risk-assessment/store', [ApRiskAssessmentController::class, 'store']);
            Route::post('risk-assessment/update', [ApRiskAssessmentController::class, 'update']);
            Route::post('risk-assessment/ap-risk-assessment-list', [ApRiskAssessmentController::class, 'apRiskAssessmentList']);
        });

        Route::group(['prefix' => 'strategic-plan'], function () {
            Route::customApiResource('outcome-indicators', OutcomeIndicatorController::class);
            Route::customApiResource('output-indicators', OutputIndicatorController::class);
            Route::post('outcome-indicators/all', [OutcomeIndicatorController::class, 'outcomes']);
            Route::post('output-indicators/all', [OutputIndicatorController::class, 'outputs']);
        });

        Route::group(['prefix' => 'calendar'], function () {
            Route::post('teams', [AuditVisitCalenderPlanController::class, 'getTeamVisitPlanCalendar']);
            Route::post('team-filter', [AuditVisitCalenderPlanController::class, 'teamCalenderFilter']);
            Route::post('update-visit-calender-status', [AuditVisitCalenderPlanController::class, 'updateVisitCalenderStatus']);
            Route::post('load-fiscal-year-wise-team', [AuditVisitCalenderPlanController::class, 'fiscalYearWiseTeams']);
            Route::post('load-fiscal-year-cost-center-wise-team', [AuditVisitCalenderPlanController::class, 'fiscalYearCostCenterWiseTeams']);
            Route::post('load-cost-center-and-fiscal-year-wise-team', [AuditVisitCalenderPlanController::class, 'costCenterAndFiscalYearWiseTeams']);
            Route::post('load-schedule-entity-fiscal-year-wise', [AuditVisitCalenderPlanController::class, 'scheduleEntityFiscalYearWise']);
            Route::post('load-cost-center-directorate-fiscal-year-wise', [AuditVisitCalenderPlanController::class, 'getCostCenterDirectorateFiscalYearWise']);
            Route::post('get-sub-tam', [AuditVisitCalenderPlanController::class, 'getSubTeam']);
            Route::post('team-calender-schedule-list', [AuditVisitCalenderPlanController::class, 'teamCalenderScheduleList']);
        });
    });

    //follow up
    Route::group(['prefix' => 'follow-up'], function () {
        Route::customApiResource('audit-observations', AuditObservationController::class);
        Route::group(['prefix' => 'audit-observation/'], function () {
            Route::post('search', [AuditObservationController::class, 'search']);
            Route::post('remove_attachment', [AuditObservationController::class, 'removeAttachment']);
            Route::post('get_audit_plan', [AuditObservationController::class, 'getAuditPlan']);
            Route::post('observation_communication', [AuditObservationController::class, 'observationCommunication']);
            Route::post('observation_communication_lists', [AuditObservationController::class, 'observationCommunicationLists']);
        });

        //broadsheet reply
        Route::group(['prefix' => 'broadsheet-reply/'], function () {
            Route::post('get-apotti-item-list', [BroadsheetReplyController::class, 'getApottiItemList']);
            Route::post('get-apotti-item-info', [BroadsheetReplyController::class, 'getApottiItemInfo']);
        });
    });

    //mis and dashboard
    Route::group(['prefix' => 'mis-and-dashboard'], function () {
        Route::post('load-all-team-lists', [MISAndDashboardController::class, 'allTeams']);
    });

    //audit conduct query
    Route::group(['prefix' => 'audit-conduct-query'], function () {
        Route::post('audit-query-schedule-list', [AuditExecutionQueryController::class, 'auditQueryScheduleList']);
        Route::post('send-audit-query', [AuditExecutionQueryController::class, 'sendAuditQuery']);
        Route::post('received-audit-query', [AuditExecutionQueryController::class, 'receivedAuditQuery']);
        Route::post('rejected-audit-query', [AuditExecutionQueryController::class, 'rejectedAuditQuery']);
        Route::post('load-audit-query', [AuditExecutionQueryController::class, 'loadAuditQuery']);
        Route::post('load-type-wise-audit-query', [AuditExecutionQueryController::class, 'loadTypeWiseAuditQuery']);
        Route::post('rpu-send-query-list', [AuditExecutionQueryController::class, 'rpuSendQueryList']);
        Route::post('store-audit-query', [AuditExecutionQueryController::class, 'storeAuditQuery']);
        Route::post('update-audit-query', [AuditExecutionQueryController::class, 'updateAuditQuery']);
        Route::post('view-audit-query', [AuditExecutionQueryController::class, 'viewAuditQuery']);
        Route::post('authority-query-list', [AuditExecutionQueryController::class, 'authorityQueryList']);
        Route::post('audit-query-response-of-rpu', [AuditExecutionQueryController::class, 'responseOfRpuQuery']);
    });

    //audit conduct memo
    Route::group(['prefix' => 'audit-conduct-memo'], function () {
        Route::post('audit-memo-store', [AcMemoController::class, 'auditMemoStore']);
        Route::post('audit-memo-list', [AcMemoController::class, 'auditMemoList']);
        Route::post('send-audit-memo-to-rpu', [AcMemoController::class, 'sendMemoToRpu']);
        Route::post('audit-memo-edit', [AcMemoController::class, 'auditMemoEdit']);
        Route::post('audit-memo-update', [AcMemoController::class, 'auditMemoUpdate']);
        Route::post('authority-memo-list', [AcMemoController::class, 'authorityMemoList']);
        Route::post('audit-memo-recommendation-store', [AcMemoController::class, 'auditMemoRecommendationStore']);
        Route::post('audit-memo-recommendation-list', [AcMemoController::class, 'auditMemoRecommendationList']);
        Route::post('attachment-list', [AcMemoController::class, 'attachmentList']);
        Route::post('audit-memo-log-list', [AcMemoController::class, 'auditMemoLogList']);
        Route::post('audit-memo-response-of-rpu', [AcMemoController::class, 'responseOfRpuMemo']);
        Route::post('audit-memo-acknowledgment-of-rpu', [AcMemoController::class, 'acknowledgmentOfRpuMemo']);
    });

    //audit conduct apotti
    Route::group(['prefix' => 'audit-conduct-apotti'], function () {
        Route::post('get-apotti-list', [ApottiController::class, 'getApottilist']);
        Route::post('get-apotti-info', [ApottiController::class, 'getApottiInfo']);
        Route::post('onucched-merge', [ApottiController::class, 'onucchedMerge']);
        Route::post('onucched-unmerge', [ApottiController::class, 'onucchedUnMerge']);
        Route::post('onucched-rearrange', [ApottiController::class, 'onucchedReArrange']);
        Route::post('apotti-wise-all-tiem', [ApottiController::class, 'apottiWiseAllItem']);
        Route::post('get-apotti-item-info', [ApottiController::class, 'getApottiItemInfo']);
        Route::post('update-apotti', [ApottiController::class, 'updateApotti']);
    });

    //audit template
    Route::post('audit-template/show', [AuditTemplateController::class, 'show']);

    //audit qc
    Route::group(['prefix' => 'audit-quality-control'], function () {
        Route::post('qac-apotti', [QacController::class, 'qacApotti']);
        Route::post('get-qac-apotti-status', [QacController::class, 'getQacApottiStatus']);
        Route::post('store-qac-committee', [QacController::class, 'storeQacCommittee']);
        Route::post('update-qac-committee', [QacController::class, 'updateQacCommittee']);
        Route::post('delete-qac-committee', [QacController::class, 'deleteQacCommittee']);
        Route::post('get-qac-committee-list', [QacController::class, 'getQacCommitteeList']);
        Route::post('get-qac-committee-wise-members', [QacController::class, 'getQacCommitteeWiseMember']);
        Route::post('store-air-wise-committee', [QacController::class, 'storeAirWiseCommittee']);
        Route::post('get-air-wise-committee', [QacController::class, 'getAirWiseCommittee']);
    });

    //audit report
    Route::group(['prefix' => 'audit-report'], function () {
        //air
        Route::group(['prefix' => 'air'], function () {
            Route::post('load-approve-plan-list', [AuditAIRReportController::class, 'loadApprovePlanList']);
            Route::post('create-air-report', [AuditAIRReportController::class, 'createNewAirReport']);
            Route::post('edit-air-report', [AuditAIRReportController::class, 'editAirReport']);
            Route::post('store-air-report', [AuditAIRReportController::class, 'storeAirReport']);
            Route::post('update-qac-air-report', [AuditAIRReportController::class, 'updateQACAirReport']);
            Route::post('get-audit-team', [AuditAIRReportController::class, 'getAuditTeam']);
            Route::post('get-audit-team-schedule', [AuditAIRReportController::class, 'getAuditTeamSchedule']);
            Route::post('get-audit-apotti-list', [AuditAIRReportController::class, 'getAuditApottiList']);
            Route::post('get-air-wise-qac-apotti', [AuditAIRReportController::class, 'getAirWiseQACApotti']);
            Route::post('get-air-and-apotti-type-wise-qac-apotti', [AuditAIRReportController::class, 'getAirAndApottiTypeWiseQACApotti']);
            Route::post('get-air-wise-audit-apotti-list', [AuditAIRReportController::class, 'getAirWiseAuditApottiList']);
            Route::post('get-audit-apotti', [AuditAIRReportController::class, 'getAuditApotti']);
            Route::post('store-air-movement', [AuditAIRReportController::class, 'storeAirMovement']);
            Route::post('get-air-last-movement', [AuditAIRReportController::class, 'getAirLastMovement']);
            Route::post('get-audit-plan-and-type-wise-air', [AuditAIRReportController::class, 'getAuditPlanAndTypeWiseAir']);
            Route::post('get-audit-final-report', [AuditAIRReportController::class, 'getAuditFinalReport']);
            Route::post('delete-air-report-wise-apotti', [AuditAIRReportController::class, 'deleteAirReportWiseApotti']);
            Route::post('apotti-final-approval', [AuditAIRReportController::class, 'apottiFinalApproval']);
            Route::post('air-send-to-rpu', [RpuAirReportController::class, 'airSendToRpu']);
            Route::post('received-air-by-rpu', [RpuAirReportController::class, 'receivedAirByRpu']);
            Route::post('audit-apotti-item-response-by-rpu', [RpuAirReportController::class, 'apottiItemResponseByRpu']);
            Route::post('final-report-movement', [AuditAIRReportController::class, 'finalReportMovement']);
        });
    });


    Route::post('get-total-query-and-memo-report', [DashboardController::class, 'getTotalQueryAndMemoReport']);

    //final plan
    Route::group(['prefix' => 'final-plan-file'], function () {
        Route::post('list', [FinalPlanController::class, 'list']);
        Route::post('store', [FinalPlanController::class, 'store']);
        Route::post('edit', [FinalPlanController::class, 'edit']);
        Route::post('update', [FinalPlanController::class, 'update']);
    });
    Route::post('document-is-exist', [FinalPlanController::class, 'documentIsExist']);

    Route::post('sp-setting-list', [StrategicSettingPlanController::class, 'list']);
    Route::post('sp-setting-store', [StrategicSettingPlanController::class, 'store']);

    //Menu Action
    Route::customApiResource('menu-actions', PMenuActionController::class);
    Route::post('roles/assign-master-designations-to-role', [PRoleController::class, 'assignMasterDesignationsToRole']);
    Route::post('roles/assigned-master-designations-to-role', [PRoleController::class, 'assignedMasterDesignationRole']);
    Route::customApiResource('roles', PRoleController::class);

    //Permission
    Route::group(['prefix' => 'role-and-permissions/'], function () {
        Route::post('get-module-menu-lists', [PermissionController::class, 'getAllMenuActionLists']);
        Route::post('get-module-menu-actions-role-wise', [PermissionController::class, 'getAllMenuActionsRoleWise']);
        Route::post('assign-menu-actions-to-role', [PermissionController::class, 'assignMenuActionsToRole']);
        Route::post('assign-menu-actions-to-employee', [PermissionController::class, 'assignMenuActionsToEmployee']);
        Route::post('modules', [PermissionController::class, 'modules']);
        Route::post('modules/other', [PermissionController::class, 'otherModules']);
        Route::post('module/menus', [PermissionController::class, 'menus']);
    });

    Route::group(['prefix' => 'migration'], function () {
        Route::post('audit-team-schedules', [MigrationController::class, 'migrateAuditTeamSchedules']);
    });

    Route::group(['prefix' => 'notify'], function () {
        Route::post('/send/mail', [NotificationController::class, 'sendMail']);
    });

});
