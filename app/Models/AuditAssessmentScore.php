<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditAssessmentScore extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'category_id',
        'fiscal_year_id',
        'ministry_id',
        'ministry_name_bn',
        'ministry_name_en',
        'entity_id',
        'entity_name_bn',
        'entity_name_en',
        'created_by',
        'updated_by'
    ];

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function audit_assessment_category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XAuditAssessmentCategory::class, 'category_id', 'id');
    }

}
