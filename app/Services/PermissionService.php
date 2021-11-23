<?php

namespace App\Services;

use App\Models\PIndividualActionPermissionMap;
use App\Models\PMenuAction;
use App\Models\PMenuActionRoleMap;
use App\Models\PMenuRoleMap;
use App\Models\PRole;
use App\Models\PRoleDesignationMap;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class PermissionService
{
    use GenericData, ApiHeart;

    public function permittedModules(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $menu_ids = collect();
            $master_designation_id = $cdesk->master_designation_id;
            $roleDesignationMap = PRoleDesignationMap::where('master_designation_id', $master_designation_id)->first();
            if (!empty($roleDesignationMap)) {
                $roleMenuMapIds = PMenuActionRoleMap::where('p_role_id', $roleDesignationMap->p_role_id)->pluck('p_menu_action_id');
                $menu_ids = $menu_ids->merge($roleMenuMapIds);
            }
            $individualPermittedMap = PIndividualActionPermissionMap::where('designation_id', $cdesk->designation_id)->pluck('p_menu_action_id');
            $menu_ids = $menu_ids->merge($individualPermittedMap);
            $menu_ids = $menu_ids->unique();
            $modules = PMenuAction::where('is_other_module', 0)->whereIn('id', $menu_ids)->where('type', 'module')->with([
                'module_childrens' => function ($query) use ($menu_ids) {
                    $query->where('type', 'module')->whereIn('id', $menu_ids);
                }])->where('parent_id', null)->get();
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedOtherModules(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $menu_ids = collect();
            $master_designation_id = $cdesk->master_designation_id;
            $roleDesignationMap = PRoleDesignationMap::where('master_designation_id', $master_designation_id)->first();
            if (!empty($roleDesignationMap)) {
                $roleMenuMapIds = PMenuActionRoleMap::where('p_role_id', $roleDesignationMap->p_role_id)->pluck('p_menu_action_id');
                $menu_ids = $menu_ids->merge($roleMenuMapIds);
            }
            $individualPermittedMap = PIndividualActionPermissionMap::where('designation_id', $cdesk->designation_id)->pluck('p_menu_action_id');
            $menu_ids = $menu_ids->merge($individualPermittedMap);
            $menu_ids = $menu_ids->unique();
            $modules = PMenuAction::where('is_other_module', 1)->whereIn('id', $menu_ids)->where('type', 'module')->with([
                'module_childrens' => function ($query) use ($menu_ids) {
                    $query->where('type', 'module')->whereIn('id', $menu_ids);
                }])->where('parent_id', null)->get();
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedModuleWiseMenus(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $menu_ids = collect();
            $module_link = $request->module_link;
            $master_designation_id = $cdesk->master_designation_id;
            $module = PMenuAction::where('link', $module_link)->first();
            $menus = [];
            $roleDesignationMapId = PRoleDesignationMap::where('master_designation_id', $master_designation_id)->first();
            if (!empty($roleDesignationMapId)) {
                $roleMenuMapIds = PMenuRoleMap::where('p_role_id', $roleDesignationMapId->p_role_id)->pluck('p_menu_action_id');
                $menu_ids = $menu_ids->merge($roleMenuMapIds);
            }
            $individualPermittedMap = PIndividualActionPermissionMap::where('designation_id', $cdesk->designation_id)->pluck('p_menu_action_id');
            $menu_ids = $menu_ids->merge($individualPermittedMap);
            $menu_ids = $menu_ids->unique();
            $menus = PMenuAction::where('menu_module_id', $module->id)->whereIn('id', $menu_ids)->where('type', 'menu')->with([
                'menu_childrens' => function ($query) use ($menu_ids) {
                    $query->whereIn('id', $menu_ids)->where('type', 'menu');
                }])->where('parent_id', null)->get();
            $data = [
                'module' => $module,
                'menus' => $menus,
            ];
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAllMenuActionLists(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $data = PMenuAction::where('type', 'module')->where('parent_id', null)
                ->where('status', 1)
                ->with(['menus', 'module_childrens.menus.menu_actions'])
                ->orderBy('display_order')->get();
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAllMenuActionsRoleWise(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $roleMenuMappedIds = PMenuActionRoleMap::where('p_role_id', $request->role)->pluck('p_menu_action_id');
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => implode(',', $roleMenuMappedIds->toArray())];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function assignMenuActionsToRole(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $menu_actions = json_decode($request->menu_actions) ?: [];
            $role_id = $request->role_id;
            $all_actions = PMenuAction::whereIn('id', $menu_actions)->get();
            $role = PRole::find($role_id);
            $role->menu_actions()->sync($all_actions);
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => 'success'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function assignIndividualMenuActionsToEmployee(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $menu_actions = json_decode($request->menu_actions) ?: [];
            $designation_id = $request->designation_id;
            $master_designation_id = $request->master_designation_id;
            PIndividualActionPermissionMap::where('designation_id', $designation_id)->delete();
            foreach ($menu_actions as $action) {
                PIndividualActionPermissionMap::create([
                    'designation_id' => $designation_id,
                    'p_menu_action_id' => $action,
                    'created_by' => $cdesk->officer_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => 'success'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getIndividualPermittedModules($master_designation_id)
    {

    }

}
