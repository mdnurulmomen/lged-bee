<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadsheetReplyToResponsibleParty extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = [
            'broad_sheet_reply_id',
            'ref_memorandum_no',
            'memorandum_no',
            'memorandum_date',
            'rpu_office_head_details',
            'subject',
            'description',
            'braod_sheet_cc',
            'is_sent_rpu',
            'sender_id',
            'sender_name_bn',
            'sender_name_en',
            'sender_office_address',
            'sender_designation_id',
            'sender_designation_bn',
            'sender_designation_en',
            'sender_unit_id',
            'sender_unit_bn',
            'sender_unit_en',
        ];


}
