<?php

namespace App\Http\Controllers;

use App\Models\PRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PRoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $roles = PRole::paginate($request->per_page);
        } else {
            $roles = PRole::all();
        }

        if ($roles) {
            $response = responseFormat('success', $roles);
        } else {
            $response = responseFormat('error', 'Role Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'role_name_en' => 'required',
            'role_name_bn' => 'required',
            'description_en' => 'required',
            'description_bn' => 'required',
            'user_level' => 'required|integer',
            'master_designation_id' => 'required|integer',
        ])->validate();

        try {
            PRole::create($data);
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(Request $request)
    {
        Validator::make($request->all(), [
            'role_id' => 'required|integer',
        ])->validate();

        $role = PRole::find($request->role_id);
        if ($role) {
            $response = responseFormat('success', $role);
        } else {
            $response = responseFormat('error', 'Role Not Found');
        }
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {
        $data = Validator::make($request->all(), [
            'role_id' => 'required',
            'role_name_en' => 'required',
            'role_name_bn' => 'required',
            'description_en' => 'required',
            'description_bn' => 'required',
            'user_level' => 'required|integer',
            'master_designation_id' => 'required|integer',
        ])->validate();

        try {
            $menu_module = PRole::find($request->role_id);
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
            PRole::find($request->role_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
