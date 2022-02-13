<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiStatus extends Model
{
    protected $connection = 'OfficeDB';
    protected $table = 'apotti_status';
    protected $fillable = [
        'apotti_id',
        'apotti_type',
        'qac_type',
        'apotti_id',
        'is_audit_criteria',
        'is_5w_pera_model',
        'is_apotti_evidence',
        'is_same_porishisto',
        'is_criteria_same_as_irregularity',
        'is_rules_and_regulation',
        'is_imperfection',
        'is_risk_analysis',
        'is_broadsheet_response',
        'comment',
        'created_by',
        'created_by_name_en',
        'created_by_name_bn',
        'updated_by',
        'updated_by_name_en',
        'updated_by_name_bn',
    ];
}
