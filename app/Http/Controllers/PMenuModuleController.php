<?php

namespace App\Http\Controllers;

use App\Models\PMenuModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PMenuModuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $modules = PMenuModule::with(['parent'])->paginate($request->per_page);
        } else {
            $modules = PMenuModule::with(['parent'])->get();
        }

        if ($modules) {
            $response = responseFormat('success', $modules);
        } else {
            $response = responseFormat('error', 'Module Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'module_name_en' => 'required',
            'module_name_bn' => 'required',
            'is_other_module' => 'required',
            'parent_module_id' => 'nullable',
            'module_link' => 'nullable',
            'module_class' => 'nullable',
            'module_icon' => 'nullable',
            'display_order' => 'nullable',
        ])->validate();

        try {
            PMenuModule::create($data);
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(Request $request)
    {
        //
    }

    public function update(Request $request)
    {
        $data = \Validator::make($request->all(), [])->validate();
        try {
            $menu_module = PMenuModule::find($request->menu_module_id);
            $menu_module->update($data);
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }


    public function destroy(Request $request)
    {
        try {
            PMenuModule::find($request->menu_module_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
