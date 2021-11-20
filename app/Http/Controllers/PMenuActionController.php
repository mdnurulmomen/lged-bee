<?php

namespace App\Http\Controllers;

use App\Models\PMenuAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PMenuActionController extends Controller
{
    public function index(Request $request)
    {

        if ($request->per_page && $request->page && !$request->all) {
            $responseData = PMenuAction::with(['parent'])
                ->where('type',$request->type)
                ->orderBy('id','DESC')
                ->paginate($request->per_page);
        } else {
            $responseData = PMenuAction::with(['parent'])
                ->where('type',$request->type)
                ->orderBy('id','DESC')
                ->get();
        }

        if ($responseData) {
            $response = responseFormat('success', $responseData);
        } else {
            $response = responseFormat('error', 'Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'title_en' => 'required',
            'title_bn' => 'required',
            'link' => 'required',
            'class' => 'nullable',
            'controller' => 'nullable',
            'method' => 'nullable',
            'icon' => 'nullable',
            'display_order' => 'nullable',
            'parent_id' => 'nullable',
            'is_other_module' => 'nullable',
            'type' => 'nullable',
        ])->validate();

        try {
            PMenuAction::create($data);
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(Request $request)
    {
        Validator::make($request->all(), [
            'menu_action_id' => 'required|integer',
        ])->validate();

        $menuAction = PMenuAction::find($request->menu_action_id);

        if ($menuAction) {
            $response = responseFormat('success', $menuAction);
        } else {
            $response = responseFormat('error', 'Menu Action Not Found');
        }
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {
        $data = Validator::make($request->all(), [
            'menu_action_id' => 'required|integer',
            'title_en' => 'required',
            'title_bn' => 'required',
            'link' => 'required',
            'class' => 'nullable',
            'controller' => 'nullable',
            'method' => 'nullable',
            'icon' => 'nullable',
            'display_order' => 'nullable',
            'parent_id' => 'nullable',
            'is_other_module' => 'nullable',
            'type' => 'nullable',
        ])->validate();

        try {
            $menu_action = PMenuAction::find($request->menu_action_id);
            $menu_action->update($data);
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }


    public function destroy(Request $request)
    {
        try {
            PMenuAction::find($request->menu_action_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
