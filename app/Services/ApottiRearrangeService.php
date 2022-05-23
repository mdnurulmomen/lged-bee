<?php

namespace App\Services;

use App\Models\Apotti;
use App\Traits\ApiHeart;
use App\Traits\GenericData;

class ApottiRearrangeService
{
    use GenericData, ApiHeart;

    public function draftSfiRearrange($apotti_id,$apotti_type,$audit_plan_id,$entity_id)
    {
        $onucched_count = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','draft')
            ->count();

        Apotti::where('id',$apotti_id)->update(['onucched_no' => $onucched_count++]);

    }


    public function sfiRearrange($apotti_id,$apotti_type,$audit_plan_id,$entity_id)
    {
       $sfi_info = Apotti::where('audit_plan_id',$audit_plan_id)
          ->where('parent_office_id',$entity_id)
          ->where('apotti_type','sfi')
           ->pluck('id');

        $draft_max = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','draft')
            ->max('onucched_no');

        $sfi_min = $draft_max + 1;

        foreach ($sfi_info as $sfi){
            Apotti::where('id',$sfi)->update(['onucched_no' => $sfi_min]);
            $sfi_min++;
        }

    }

    public function nonSfiRearrange($apotti_id,$apotti_type,$audit_plan_id,$entity_id)
    {
        $nonsfi_info = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','non-sfi')
            ->pluck('id');

        $sfi_max = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','sfi')
            ->max('onucched_no');

        if(!$sfi_max){
            $sfi_max = Apotti::where('audit_plan_id',$audit_plan_id)
                ->where('parent_office_id',$entity_id)
                ->where('apotti_type','sfi')
                ->max('onucched_no');
        }

        $non_sfi_min = $sfi_max + 1;

        foreach ($nonsfi_info as $non_sfi){
            Apotti::where('id',$non_sfi)->update(['onucched_no' => $non_sfi_min]);
            $non_sfi_min++;
        }

    }

    public function rejectSfiRearrange($apotti_id,$apotti_type,$audit_plan_id,$entity_id)
    {
        $reject_info = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','reject')
            ->pluck('id');

        $non_sfi_max = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','non-sfi')
            ->max('onucched_no');

        if(!$non_sfi_max){
            $non_sfi_max = Apotti::where('audit_plan_id',$audit_plan_id)
                ->where('parent_office_id',$entity_id)
                ->where('apotti_type','sfi')
                ->max('onucched_no');
        }

        $reject_min = $non_sfi_max + 1;

        foreach ($reject_info as $reject){
            Apotti::where('id',$reject)->update(['onucched_no' => $reject_min]);
            $reject_min++;
        }
    }

    public function nullApottiRearrange($apotti_id,$apotti_type,$audit_plan_id,$entity_id)
    {
        $null_apotti_info = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->whereNull('apotti_type')
            ->pluck('id');

        $reject_max = Apotti::where('audit_plan_id',$audit_plan_id)
            ->where('parent_office_id',$entity_id)
            ->where('apotti_type','reject')
            ->max('onucched_no');

        if(!$reject_max){
            $reject_max = Apotti::where('audit_plan_id',$audit_plan_id)
                ->where('parent_office_id',$entity_id)
                ->where('apotti_type','non-sfi')
                ->max('onucched_no');
        }

        if(!$reject_max){
            $reject_max = Apotti::where('audit_plan_id',$audit_plan_id)
                ->where('parent_office_id',$entity_id)
                ->where('apotti_type','sfi')
                ->max('onucched_no');
        }

        $null_min = $reject_max + 1;

        foreach ($null_apotti_info as $null_apotti){
            Apotti::where('id',$null_apotti)->update(['onucched_no' => $null_min]);
            $null_min++;
        }
    }
}
