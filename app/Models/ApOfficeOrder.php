<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOfficeOrder extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'annual_plan_id',
        'schedule_id',
        'activity_id',
        'milestone_id',
        'fiscal_year_id',
        'audit_plan_id',
        'duration_id',
        'outcome_id',
        'output_id',
        'memorandum_no',
        'memorandum_date',
        'heading_details',
        'advices',
        'approved_status',
        'order_cc_list',
        'cc_sender_details',
        'issuer_details',
        'team_members',
        'team_schedules',
        'draft_officer_id',
        'draft_officer_name_en',
        'draft_officer_name_bn',
        'draft_designation_id',
        'draft_designation_name_en',
        'draft_designation_name_bn',
        'draft_office_unit_id',
        'draft_office_unit_en',
        'draft_office_unit_bn',
        'draft_officer_phone',
        'draft_officer_email',
        'created_by',
        'modified_by'
    ];

    public function office_order_movement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApOfficeOrderMovement::class,'ap_office_order_id','id');
    }

    public function getMemorandumDateAttribute($value): string
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function setMemorandumDateAttribute($value)
    {
        if (strstr($value, '/')){
            $value = str_replace('/','-',$value);
        }
        $this->attributes['memorandum_date'] = Carbon::parse($value)->format('Y-m-d');
    }
}
