<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcQuery extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'fiscal_year_id',
        'activity_id',
        'annual_plan_id',
        'audit_plan_id',
        'office_order_id',
        'team_id',
        'entity_office_id',
        'entity_office_name_en',
        'entity_office_name_bn',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'querier_officer_id',
        'querier_officer_name_en',
        'querier_officer_name_bn',
        'querier_unit_name_en',
        'querier_unit_name_bn',
        'querier_designation_id',
        'querier_designation_bn',
        'querier_designation_en',
        'team_leader_name',
        'team_leader_designation',
        'rpu_office_head_details',
        'memorandum_no',
        'memorandum_date',
        'suthro',
        'subject',
        'description',
        'cc',
        'responsible_person_details',
        'comment',
        'has_sent_to_rpu',
        'status',
        'created_by',
        'updated_by'
    ];

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

    public function query_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcQueryItem::class,'ac_query_id','id');
    }

    public function plan_team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'team_id', 'id');
    }
}
