<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XAuditAssessmentCriteria extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'category_id',
        'name_en',
        'name_bn',
        'weight',
        'created_at',
        'updated_at',
    ];

    public function audit_assessment_category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XAuditAssessmentCategory::class, 'category_id', 'id');
    }

}
