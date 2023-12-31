<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApEntityAuditPlan extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'party_id',
        'ap_organization_yearly_plan_rp_id',
        'plan_description',
        'draft_office_id',
        'draft_unit_id',
        'draft_unit_name_en',
        'draft_unit_name_bn',
        'draft_designation_id',
        'draft_designation_name_en',
        'draft_designation_name_bn',
        'draft_officer_id',
        'draft_officer_name_en',
        'draft_officer_name_bn',
        'created_by',
        'modified_by',
        'device_type',
        'device_id',
    ];

    public function party()
    {
        return $this->belongsTo(
            ApOrganizationYearlyPlanResponsibleParty::class,
            'ap_organization_yearly_plan_rp_id',
            'id'
        );
    }
}
