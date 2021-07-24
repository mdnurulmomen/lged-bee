<?php

namespace App\Traits;

use App\Models\OfficeDomain;
use App\Models\XFiscalYear;
use Illuminate\Support\Facades\Config;

trait GenericData
{
    public function durationIdFromFiscalYear($fiscal_year_id)
    {
        return XFiscalYear::select('duration_id')->where('id', $fiscal_year_id)->first()->duration_id;
    }

    function switchOffice($office_id, $status = -1, $returnErrorMsg = true): array
    {
        $officeDomain = OfficeDomain::where('office_id', $office_id)->first();

        if ($status != -1) {
            $officeDomain = OfficeDomain::where('status', $status)->first();
        }
        if (empty($officeDomain)) {
            $msg = __("অফিস ডাটাবেজ পাওয়া যায় নি! সাপোর্ট টিমের সাথে যোগাযোগ করুন। অফিস আইডিঃ {0}", $office_id);
            if (!$returnErrorMsg) {
                $msg = "";
            }
            return ['status' => 'error', 'message' => $msg];
        }

        Config::set("database.connections.OfficeDB", [
            'driver' => 'mysql',
            "host" => $officeDomain->domain_host,
            "database" => $officeDomain->office_db,
            "username" => $officeDomain->domain_username,
            "password" => $officeDomain->domain_password,
            "port" => 3306,
        ]);

        return ['status' => 'success', 'message' => __("Successfully Connected"), 'office_id' => $office_id, 'office_domain' => $officeDomain];
    }
}

