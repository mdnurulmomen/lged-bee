<?php

namespace App\Traits;

use App\Models\OfficeDomain;
use App\Models\OpYearlyAuditCalendarActivity;
use App\Models\XFiscalYear;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait GenericData
{
    public function durationIdFromFiscalYear($fiscal_year_id)
    {
        return XFiscalYear::select('duration_id')->where('id', $fiscal_year_id)->first()->duration_id;
    }

    public function milestoneTargetDate($milestone_id)
    {
        return OpYearlyAuditCalendarActivity::select('target_date')->where('milestone_id', $milestone_id)->first()->target_date;
    }

    public function switchOffice($office_id, $status = -1, $returnErrorMsg = true): array
    {
        return ['status' => 'success', 'message' => __("Successfully Connected")];
//        $officeDomain = OfficeDomain::where('office_id', $office_id)->first();
//
//        if ($status != -1) {
//            $officeDomain = OfficeDomain::where('status', $status)->first();
//        }
//        if (empty($officeDomain)) {
//            $msg = sprintf("অফিস ডাটাবেজ পাওয়া যায় নি! সাপোর্ট টিমের সাথে যোগাযোগ করুন। অফিস আইডিঃ %s.", $office_id);
//            return ['status' => 'error', 'message' => $msg];
//        }
//
//        $this->emptyOfficeDBConnection();
//
//        Config::set("database.connections.OfficeDB", [
//            'driver' => 'mysql',
//            "host" => $officeDomain->domain_host,
//            "database" => $officeDomain->office_db,
//            "username" => $officeDomain->domain_username,
//            "password" => $officeDomain->domain_password,
//            "port" => 3306,
//            'charset' => 'utf8',
//            'collation' => 'utf8_general_ci',
//        ]);
//
//        DB::purge('OfficeDB');
//        DB::reconnect('OfficeDB');
//
//        return ['status' => 'success', 'message' => __("Successfully Connected"), 'office_id' => $office_id, 'office_domain' => $officeDomain];
    }

    public function emptyOfficeDBConnection()
    {
        Config::set("database.connections.OfficeDB", [
            'driver' => 'mysql',
            "host" => '',
            "database" => '',
            "username" => '',
            "password" => '',
            "port" => '',
        ]);
        DB::purge('OfficeDB');
    }
}

