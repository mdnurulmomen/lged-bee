<?php

namespace App\Repository;

use App\Models\SPFile;
use App\Models\SPSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Traits\GenericData;

class StrategicPlanRepo
{

    use GenericData;

    public function store(Request $request)
    {
        $attachment = $request->file;

        if ($request->hasfile('file')) {
            $fileName = uniqid() . '.' . $attachment->extension();
            Storage::disk('sp')->put($fileName,  File::get($attachment));

            $sPFile = new SPFile();
            $sPFile->duration_id = 1;
            $sPFile->file_name = $attachment->getClientOriginalName();
            $sPFile->file_location = 'storage/app/public/sp/' . $fileName;
            $sPFile->file_url = url('storage/sp/' . $fileName);
            $sPFile->save();
        }
    }

    public function show(Request $request)
    {
        return SPFile::find($request->id);
    }

    public function list(Request $request)
    {
        return SPFile::all();
    }

    public function settingStore(Request $request)
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

    public function settingList(Request $request)
    {
        return SPSetting::all();
    }
}
