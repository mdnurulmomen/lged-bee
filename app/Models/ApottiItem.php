<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiItem extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'apotti_id',
        'memo_id',
        'onucched_no',
        'memo_irregularity_type',
        'memo_irregularity_sub_type',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
        'cost_center_id',
        'cost_center_name_en', 'undefined cost_center',
        'cost_center_name_bn', 'undefined cost_center',
        'fiscal_year_id',
        'audit_year_start',
        'audit_year_end',
        'ac_query_potro_no',
        'ap_office_order_id',
        'audit_plan_id',
        'audit_type',
        'team_id',
        'memo_description_bn',
        'memo_title_bn',
        'memo_type',
        'memo_status',
        'jorito_ortho_poriman',
        'onishponno_jorito_ortho_poriman',
        'created_by',
        'status',
    ];

    public static $memo_status_list = [
        '0' => 'N/A',
        '1' => 'নিস্পন্ন',
        '2' => 'অনিস্পন্ন',
        '3' => 'আংশিক নিস্পন্ন',
    ];

    protected $appends = ['memo_status_name'];

    public function getMemoStatusNameAttribute()
    {
        return self::$memo_status_list[$this->attributes['memo_status']];
    }

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function apotti_attachment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcMemoAttachment::class, 'ac_memo_id', 'memo_id')->where('file_type', 'broadsheet');
    }

    public function porisishtos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcMemoPorisishto::class, 'ac_memo_id', 'memo_id');
    }

    public function apotti(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Apotti::class, 'apotti_id', 'id');
    }
}
