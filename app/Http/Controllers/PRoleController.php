<?php

namespace App\Http\Controllers;

use App\Models\PRole;
use App\Models\PRoleDesignationMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PRoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $roles = PRole::orderBy('user_level')->paginate($request->per_page);
        } else {
            $roles = PRole::orderBy('user_level')->get();
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

    public function assignedMasterDesignationRole(Request $request)
    {
        try {
            $master_designation_ids = PRoleDesignationMap::where('p_role_id', $request->role)->pluck('master_designation_id');
            return ['status' => 'success', 'data' => implode(',', $master_designation_ids->toArray())];
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function assignMasterDesignationsToRole(Request $request)
    {
        Validator::make($request->all(), [
            'role_id' => 'required',
            'master_designations' => 'required',
        ])->validate();
        DB::beginTransaction();
        try {
            $master_designations = explode(',', $request->master_designations);
            if ($master_designations) {
                PRoleDesignationMap::where('p_role_id', $request->role_id)->delete();
                foreach ($master_designations as $master_designation) {
                    PRoleDesignationMap::create([
                        'p_role_id' => $request->role_id,
                        'master_designation_id' => $master_designation,
                    ]);
                }
                $response = responseFormat('success', 'Successfully Updated');
                DB::commit();
            } else {
                throw new \Exception('No Designations');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
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
