<?php

namespace App\Repository;

use App\Models\ApEntityAuditPlan;
use App\Models\ApOrganizationYearlyPlanResponsibleParty;
use App\Repository\Contracts\ApEntityAuditPlanInterface;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class ApEntityAuditPlanRepository implements ApEntityAuditPlanInterface
{
    use GenericData;

    public function allEntityAuditPlanLists(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            if ($request->per_page && $request->page && !$request->all) {
                $all_entities = ApOrganizationYearlyPlanResponsibleParty::where('fiscal_year_id', $fiscal_year_id)->paginate($request->per_page);
            } else {
                $all_entities = ApOrganizationYearlyPlanResponsibleParty::where('fiscal_year_id', $fiscal_year_id)->get();
            }
            return ['status' => 'success', 'data' => $all_entities];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeDraftAuditPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $plan = json_decode($request->plan, true);

        $this->switchOffice($cdesk->office_id);

        $draft_plan_data = [
            'party_id' => $plan['party_id'],
            'ap_organization_yearly_plan_rp_id' => $plan['yearly_plan_rp_id'],
            'plan_description' => $plan['plan_description'],
            'draft_office_id' => $cdesk->office_id,
            'draft_unit_id' => $cdesk->office_unit_id,
            'draft_unit_name_en' => $cdesk->office_unit_en,
            'draft_unit_name_bn' => $cdesk->office_unit_bn,
            'draft_designation_id' => $cdesk->designation_id,
            'draft_designation_name_en' => $cdesk->designation_en,
            'draft_designation_name_bn' => $cdesk->designation_bn,
            'draft_officer_id' => $cdesk->officer_id,
            'draft_officer_name_en' => $cdesk->officer_en,
            'draft_officer_name_bn' => $cdesk->officer_bn,
            'created_by' => $cdesk->officer_id,
            'modified_by' => $cdesk->officer_id,
            'device_type' => '',
            'device_id' => '',
        ];

        try {
            $draft_plan = ApEntityAuditPlan::updateOrCreate(['entity_id' => $plan['entity_id']], $draft_plan_data);
            $data = ['status' => 'success', 'data' => $draft_plan];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function showEntityAuditPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);

        try {
            $plan = ApEntityAuditPlan::where('entity_id', $request->entity_id)->where('yearly_plan_rp_id', $request->yearly_plan_rp_id)->first();
            $data = ['status' => 'success', 'data' => $plan];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;

    }

}
