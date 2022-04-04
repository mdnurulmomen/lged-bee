<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcMemo extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    public static $memo_types = [
        '0' => 'N/A',
        '1' => 'এসএফআই',
        '2' => 'নন-এসএফআই',
        '3' => 'ড্রাফ্ট প্যারা',
        '4' => 'পাণ্ডুলিপি',
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

    protected $appends = ['memo_type_name', 'memo_irregularity_type_name', 'memo_irregularity_sub_type_name', 'memo_status_name'];
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
        'fiscal_year',
        'audit_plan_id',
        'audit_year_start',
        'audit_year_end',
        'ac_query_potro_no',
        'ap_office_order_id',
        'audit_type',
        'team_id',
        'memo_title_bn',
        'memo_description_bn',
        'irregularity_cause',
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
        'memo_sharok_no',
        'memo_send_date',
        'memo_cc',
        'comment',
        'has_sent_to_rpu',
        'sender_officer_id',
        'sender_officer_name_bn',
        'sender_officer_name_en',
        'sender_unit_id',
        'sender_unit_name_bn',
        'sender_unit_name_en',
        'sender_designation_id',
        'sender_designation_bn',
        'sender_designation_en',
        'team_leader_name',
        'team_leader_designation',
        'sub_team_leader_name',
        'sub_team_leader_designation',
        'issued_by',
        'rpu_acceptor_officer_id',
        'rpu_acceptor_officer_name_bn',
        'rpu_acceptor_officer_name_en',
        'rpu_acceptor_unit_name_bn',
        'rpu_acceptor_unit_name_en',
        'rpu_acceptor_designation_name_bn',
        'rpu_acceptor_designation_name_en',
        'rpu_acceptor_signature',
        'memo_date',
        'porisisto_details',
    ];

    public function getMemoTypeNameAttribute()
    {
        return self::$memo_types[$this->attributes['memo_type']];
    }

    public function getMemoStatusNameAttribute()
    {
        return self::$memo_status_list[$this->attributes['memo_status']];
    }

    public function getMemoIrregularityTypeNameAttribute()
    {
        if($this->attributes['memo_irregularity_type']){
            return self::$memo_irregularity_types[$this->attributes['memo_irregularity_type']];
        }else{
            return self::$memo_irregularity_types[0];
        }
    }

    public function getMemoIrregularitySubTypeNameAttribute($value)
    {
        if($this->attributes['memo_irregularity_sub_type']){
            return self::$memo_irregularity_sub_types[$this->attributes['memo_irregularity_sub_type']];
        }else{
            return self::$memo_irregularity_sub_types[0];
        }

    }

    public function setJoritoOrthoPorimanAttribute($value)
    {
        $this->attributes['jorito_ortho_poriman'] = str_replace(",","",$value);
    }

    public function setOnishponnoJoritoOrthoPorimanAttribute($value)
    {
        $this->attributes['onishponno_jorito_ortho_poriman'] = str_replace(",","",$value);
    }

    public function ac_memo_attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcMemoAttachment::class, 'ac_memo_id', 'id');
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'id', 'team_id');
    }

    public function audit_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApEntityIndividualAuditPlan::class, 'audit_plan_id', 'id');
    }

}
