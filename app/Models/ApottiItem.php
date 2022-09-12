<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApottiItem extends Model
{
    use HasFactory, SoftDeletes;

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
        'cost_center_name_en',
        'cost_center_name_bn',
        'fiscal_year_id',
        'audit_year_start',
        'audit_year_end',
        'ac_query_potro_no',
        'ap_office_order_id',
        'audit_plan_id',
        'audit_type',
        'team_id',
        'memo_title_bn',
        'memo_description_bn',
        'memo_type',
        'memo_status',
        'jorito_ortho_poriman',
        'onishponno_jorito_ortho_poriman',
        'adjustment_ortho_poriman',
        'response_of_rpu',
        'irregularity_cause',
        'audit_conclusion',
        'audit_recommendation',
        'ministry_office_id',
        'unit_response',
        'entity_response',
        'ministry_response',
        'directorate_response',
        'created_by',
        'updated_by',
        'status',
        'is_sent_rp',
        'file_token_no',
        'cover_page_path',
        'cover_page',
        'attachment_path',
        'report_type_id',
        'deleted_by',
        'project_id',
        'project_name_en',
        'project_name_bn',

    ];

    public static $memo_status_list = [
        '0' => 'N/A',
        '1' => 'নিস্পন্ন',
        '2' => 'অনিস্পন্ন',
        '3' => 'আংশিক নিস্পন্ন',
    ];
    public static $memo_irregularity_types = [
        '0' => 'N/A',
        '1' => 'আত্মসাত, চুরি, প্রতারণা ও জালিয়াতিমূলক',
        '2' => 'সরকারের আর্থিক ক্ষতি',
        '3' => 'বিধি ও পদ্ধতিগত অনিয়ম',
        '4' => 'নিরিক্ষা কালিন অসহযোগীতা',
        '5' => 'বিশেষ ধরনের আপত্তি',
    ];
    public static $memo_irregularity_sub_types = [
        '0' => 'N/A',
        '1' => 'ভ্যাট-আইটিসহ সরকারি প্রাপ্য আদায় না করা',
        '2' => 'কম আদায় করাা',
        '3' => 'আদায় করা সত্ত্বেও কোষাগারে জমা না করা',
        '4' => 'বাজার দর অপেক্ষা উচ্চমূল্যে ক্রয় কার্য সম্পাদন',
        '5' => 'রেসপন্সিভ সর্বনিম্ন দরদাতার স্থলে উচ্চ দরদাতার নিকট থেকে কার্য/পণ্য/সেবা ক্রয়',
        '6' => 'প্রকল্প শেষে অব্যয়িত অর্থ ফেরত না দেওয়া',
        '7' => 'ভুল বেতন নির্ধারণীর মাধ্যমে অতিরিক্ত বেতন উত্তোলন',
        '8' => 'প্রাপ্যতাবিহীন ভাতা উত্তোলন',
        '9' => 'জাতীয় অন্যান্য সরকারী অর্থের ক্ষতি সংক্রান্ত আপত্তি।',
    ];

    protected $appends = ['memo_irregularity_type_name', 'memo_irregularity_sub_type_name', 'memo_status_name'];

    public function getMemoStatusNameAttribute()
    {
        return self::$memo_status_list[$this->attributes['memo_status']];
    }

    public function getMemoIrregularityTypeNameAttribute()
    {
        if ($this->attributes['memo_irregularity_type']) {
            return self::$memo_irregularity_types[$this->attributes['memo_irregularity_type']];
        } else {
            return self::$memo_irregularity_types[0];
        }
    }

    public function oniyomer_category()
    {
        return $this->belongsTo(ApottiCategory::class, 'apotti_category_id', 'id');
    }


    public function getMemoIrregularitySubTypeNameAttribute()
    {
        if ($this->attributes['memo_irregularity_sub_type']) {
            return self::$memo_irregularity_sub_types[$this->attributes['memo_irregularity_sub_type']];
        } else {
            return self::$memo_irregularity_sub_types[0];
        }

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
