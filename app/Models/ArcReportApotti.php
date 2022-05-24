<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArcReportApotti extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'audit_report_name',
        'directorate_id',
        'directorate_name_en',
        'directorate_name_bn',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn',
        'entity_id',
        'entity_name_en',
        'entity_name_bn',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'nirikkhito_ortho_bosor',
        'orthobosor_start',
        'orthobosor_end',
        'nirikkha_dhoron',
        'onucched_no',
        'apotti_title',
        'jorito_ortho_poriman',
        'apotti_status',
        'pa_commitee_meeting',
        'pa_commitee_siddhanto',
        'ministry_actions',
        'audit_department_actions',
        'mamla',
        'niyomitokorn_jonito_nispotti',
        'is_nispottikrito',
        'is_alocito',
        'created_by',
        'updated_by',
        'approve_status',
    ];
}
