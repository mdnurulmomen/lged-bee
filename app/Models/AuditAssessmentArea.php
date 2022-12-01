<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditAssessmentArea extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $connection = 'OfficeDB';

    /*
    public function auditArea()
    {
        return $this->belongsTo(XAuditArea::class, 'audit_area_id', 'id');
    }
    */

    public function auditAssessmentAreaRisks()
    {
        return $this->hasMany(AuditAssessmentAreaRisk::class, 'audit_assessment_area_id', 'id');
    }
}
