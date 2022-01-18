<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apotti extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'audit_plan_id',
        'apotti_title',
        'apotti_description',
        'apotti_type',
        'onucched_no',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
        'fiscal_year_id',
        'total_jorito_ortho_poriman',
        'total_onishponno_jorito_ortho_poriman',
        'response_of_rpu',
        'irregularity_cause',
        'audit_conclusion',
        'audit_recommendation',
        'created_by',
        'updated_by',
        'approve_status',
        'status',
        'comment',
        'sender_officer_id',
        'sender_officer_name_bn',
        'sender_officer_name_en',
        'sender_unit_id',
        'sender_unit_name_bn',
        'sender_unit_name_en',
        'sender_designation_id',
        'sender_designation_bn',
        'sender_designation_en',
        'rpu_acceptor_officer_id',
        'rpu_acceptor_officer_name_en',
        'rpu_acceptor_officer_name_bn',
        'rpu_acceptor_unit_name_en',
        'rpu_acceptor_unit_name_bn',
        'rpu_acceptor_designation_name_bn',
        'rpu_acceptor_designation_name_en',
        'rpu_acceptor_signature',
        'apotti_sequence',
        'is_combined',
        'air_generate_type',
        'is_delete',
        'final_status',
    ];

    public function apotti_items(){
        return $this->hasMany(ApottiItem::class, 'apotti_id', 'id');
    }

    public function apotti_status(){
        return $this->hasMany(ApottiStatus::class, 'apotti_id', 'id');
    }

    public function apotti_airs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(RAir::class, 'apotti_rair_maps');
    }
}
