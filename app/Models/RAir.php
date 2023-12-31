<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RAir extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'parent_id',
        'report_number',
        'report_name',
        'report_type',
        'fiscal_year_id',
        'annual_plan_id',
        'audit_plan_id',
        'activity_id',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn',
        'entity_id',
        'entity_name_en',
        'entity_name_bn',
        'qac_report_date',
        'air_description',
        'type',
        'is_sent',
        'status',
        'issue_date',
        'created_by',
        'modified_by',
    ];

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

    public function ap_entities()
    {
        return $this->hasMany(AnnualPlanEntitie::class, 'annual_plan_id', 'annual_plan_id');
    }

    public function audit_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApEntityIndividualAuditPlan::class, 'audit_plan_id', 'id');
    }

    public function r_air_child(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RAir::class, 'id', 'parent_id');
    }

    public function latest_r_air_movement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RAirMovement::class)->latest();
    }

    public function qac_committee()
    {
        return $this->hasMany(QacCommitteeAirMap::class, 'air_report_id', 'id');
//                return $this->belongsToMany(QacCommittee::class, 'qac_commitee_air_map');

    }

    public function reported_apotti_cover_page(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReportedApottiAttachment::class, 'report_id','id')->whereNull('attachment_name')->whereNotNull('cover_page_name');
    }

    public function reported_apotti_attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReportedApottiAttachment::class, 'report_id','id')->whereNotNull('attachment_name')->whereNull('cover_page_name');
    }

    public function report_apotti_map(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApottiRAirMap::class, 'rairs_id','id');

    }
}
