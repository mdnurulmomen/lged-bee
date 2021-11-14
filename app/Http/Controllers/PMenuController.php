<?php

namespace App\Http\Controllers;

use App\Models\PMenu;
use Illuminate\Http\Request;

class PMenuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $menus = PMenu::with('children')->where('parent_menu_id', null)->paginate($request->per_page);
        } else {
            $menus = PMenu::with('children')->where('parent_menu_id', null)->get();
        }

        if ($menus) {
            $response = responseFormat('success', $menus);
        } else {
            $response = responseFormat('error', 'Module Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $data = \Validator::make($request->all(), [])->validate();
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
