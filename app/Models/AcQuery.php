<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcQuery extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'fiscal_year_id',
        'activity_id',
        'annual_plan_id',
        'audit_plan_id',
        'office_order_id',
        'team_id',
        'team_leader_name_en',
        'team_leader_name_bn',
        'cost_center_type_id',
        'ministry_id',
        'controlling_office_id',
        'controlling_office_name_en',
        'controlling_office_name_bn',
        'entity_office_id',
        'entity_office_name_en',
        'entity_office_name_bn',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'query_id',
        'query_title_en',
        'query_title_bn',
        'is_query_sent',
        'query_send_date',
        'is_query_document_received',
        'query_document_received_date',
        'querier_officer_id',
        'querier_officer_name_en',
        'querier_officer_name_bn',
        'querier_designation_id',
        'querier_designation_bn',
        'querier_designation_bn',
        'query_receiver_officer_id',
        'query_receiver_officer_name_bn',
        'query_receiver_officer_name_en',
        'query_receiver_designation_id',
        'status',
    ];

    public function getQueryDocumentReceivedDateAttribute($value): string
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function setQueryDocumentReceivedDateAttribute($value)
    {
        if (strstr($value, '/')) {
            $value = str_replace('/', '-', $value);
        }
        $this->attributes['query_document_received_date'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function getQuerySendDateAttribute($value): string
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function setQuerySendDateAttribute($value)
    {
        if (strstr($value, '/')) {
            $value = str_replace('/', '-', $value);
        }
        $this->attributes['query_send_date'] = Carbon::parse($value)->format('Y-m-d');
    }
}
