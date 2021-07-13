<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_id',
        'fiscal_year_id',
        'outcome_id',
        'output_id',
        'activity_no',
        'title_en',
        'title_bn',
        'activity_parent_id',
        'is_parent',
    ];

    public function activity_output()
    {
        return $this->belongsTo(XstrategicPlanOutput::class, 'output_id', 'id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OpActivity::class, 'activity_parent_id')->with('children');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OpActivity::class, 'activity_parent_id')->with('parent');
    }

    public function milestones()
    {
        return $this->hasMany(OpActivityMilestone::class, 'activity_id', 'id');
    }

    public function responsibles()
    {
        return $this->hasMany(OpYearlyAuditCalendarResponsible::class, 'activity_id', 'id');
    }

    public function comment()
    {
        return $this->hasOne(OpActivityComment::class, 'activity_id', 'id');
    }

}
