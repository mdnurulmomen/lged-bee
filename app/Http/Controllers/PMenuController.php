<?php

namespace App\Http\Controllers;

use App\Models\PMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PMenuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $menus = PMenu::with(['module_menu','parent'])->paginate($request->per_page);
        } else {
            $menus = PMenu::with(['module_menu','parent'])->get();
        }

        if ($menus) {
            $response = responseFormat('success', $menus);
        } else {
            $response = responseFormat('error', 'Menu List Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'menu_name_en' => 'required',
            'menu_name_bn' => 'required',
            'module_menu_id' => 'required',
            'parent_menu_id' => 'nullable',
            'menu_class' => 'nullable',
            'menu_link' => 'nullable',
            'menu_icon' => 'nullable',
            'display_order' => 'nullable',
        ])->validate();

        try {
            PMenu::create($data);
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
            $menu_module = PMenu::find($request->menu_id);
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
            PMenu::find($request->menu_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
