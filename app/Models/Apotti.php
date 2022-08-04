<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Apotti extends Model
{
    use HasFactory,SoftDeletes;

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
        'total_adjustment_ortho_poriman',
        'response_of_rpu',
        'irregularity_cause',
        'audit_conclusion',
        'audit_recommendation',
        'created_by',
        'updated_by',
        'approve_status',
        'status',
        'is_alochito',
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
        'air_issue_date',
        'status_review_date',
        'file_token_no',
        'cover_page_path',
        'cover_page',
        'attachment_path',
        'report_type_id',
        'is_sent_rp',
        'is_archived_reported_apotti'
    ];

//    protected static function boot() {
//        parent::boot();
//        static::addGlobalScope('order', function (Builder $builder) {
//            $builder->orderBy('onucched_no', 'ASC');
//        });
//    }

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function apotti_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApottiItem::class, 'apotti_id', 'id');
    }

    public function apotti_status(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApottiStatus::class, 'apotti_id', 'id');
    }

    public function apotti_latest_status(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApottiStatus::class, 'apotti_id', 'id')
            ->orderBy('id','DESC');
    }

    public function apotti_airs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(RAir::class, 'apotti_rair_maps');
    }

    public function latest_movement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(XMovement::class, 'relational_id')->latest();
    }

    public function apotti_porisishtos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApottiPorisishto::class, 'apotti_id', 'id')
            ->whereNull('porisishto_type');
    }

    public function apotti_porisishto_summary(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApottiPorisishto::class, 'apotti_id', 'id')
            ->where('porisishto_type','summary');
    }
}
