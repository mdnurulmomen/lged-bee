<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArcApotti extends Model
{
    use HasFactory;

    protected $fillable = [
        'onucched_no',
        'apotti_oniyomer_dhoron',
        'directorate_id',
        'office_ministry_id',
        'cover_page',
        'cover_page_path',
        'attachment_path',
        'cost_center_id',
        'cost_center_name_bn',
        'cost_center_name_en',
        'apotti_year',
        'apotti_title',
        'jorito_ortho_poriman',
        'onisponno_jorito_ortho_poriman',
        'nirikkha_dhoron',
        'apottir_dhoron',
        'apotti_status',
        'entity_info_id',
        'entity_name',
        'checked_by',
        'approved_by',
        'created_by',
        'updated_by',
        'approve_status',
        'deleted_by',
        'deleted_at',
        'apotti_category_id',
        'apotti_sub_category_id',
        'attachment_count',
        'year_start',
        'year_end',
        'report_type_id',
        'file_token_no',
        'nirikkhar_shal',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn'
    ];

    public function oniyomer_category()
    {
        return $this->belongsTo(ApottiCategory::class, 'apotti_category_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(ArcApottiAttachment::class, 'apotti_id', 'id');
    }

}
