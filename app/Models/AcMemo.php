<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcMemo extends Model
{
    use HasFactory;

    public static $memo_type = [
        '1' => '১. SFI',
        '2' => '২. Non-SFI',
        '3' => '৩. ড্রাফ্ট প্যারা',
        '4' => '৪. পাণ্ডুলিপি',
    ];
    public static $memo_irregularity_type = [
        '1' => '১. জালিয়াতী',
        '2' => '২. আর্থিক ক্ষতি',
        '3' => '৩. আর্থিক বিধির ব্যত্যয়',
        '4' => '৪. নিরিক্ষা কালিন অসহযোগীতা',
        '5' => '৫. রাজস্ব ক্ষতি',
        '6' => '৬. অন্যান্য',
    ];
    public static $memo_irregularity_sub_type = [
        '1' => 'ভ্যাট-আইটিসহ সরকারি প্রাপ্য আদায় না করা',
    ];
    protected $connection = 'OfficeDB';
    protected $fillable = [
        'onucched_no',
        'memo_irregularity_type',
        'memo_irregularity_sub_type',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn',
        'controlling_office_id',
        'controlling_office_name_en',
        'controlling_office_name_bn',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'fiscal_year_id',
        'audit_year_start',
        'audit_year_end',
        'ac_query_potro_no',
        'ap_office_order_id',
        'audit_type',
        'team_id',
        'memo_title_bn',
        'memo_description_bn',
        'memo_type',
        'memo_status',
        'jorito_ortho_poriman',
        'onishponno_jorito_ortho_poriman',
        'response_of_rpu',
        'audit_conclusion',
        'audit_recommendation',
        'created_by',
        'updated_by',
        'approve_status',
        'status',
        'comment',
    ];

    public function ac_memo_attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcMemoAttachment::class, 'ac_memo_id', 'id');
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'id', 'team_id');
    }

}
