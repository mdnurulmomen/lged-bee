<?php

namespace App\Repository;

use App\Models\SPSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Traits\GenericData;

class StrategicSettingPlanRepo
{
    use GenericData;

    public function store(Request $request)
    {
        $settings = array();
        foreach ($request->setting_key as $key => $settingKey) {
            if(!empty($settingKey)){
                $settings[] = array(
                    'setting_key'=> $settingKey,
                    'setting_value'=> $request->setting_value[$key],
                );
            }
        }
        return SPSetting::insert($settings);
    }

    public function list(Request $request)
    {
        return SPSetting::all();
    }
}
