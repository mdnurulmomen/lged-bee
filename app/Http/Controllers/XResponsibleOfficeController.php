<?php

namespace App\Http\Controllers;

use App\Models\XResponsibleOffice;
use Illuminate\Http\Request;

class XResponsibleOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $offices = XResponsibleOffice::orderBy('office_sequence')->paginate($request->per_page);
        } else {
            $offices = XResponsibleOffice::orderBy('office_sequence')->orderBy('office_sequence')->get();
        }

        if ($offices) {
            $response = responseFormat('success', $offices);
        } else {
            $response = responseFormat('error', 'Directorates Not Found');
        }
        return response()->json($response, 200);
    }

    public function allDirectorates(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $offices = XResponsibleOffice::orderBy('office_sequence')->where('office_layer', 2)->paginate($request->per_page);
        } else {
            $offices = XResponsibleOffice::orderBy('office_sequence')->where('office_layer', 2)->get();
        }

        if ($offices) {
            $response = responseFormat('success', $offices);
        } else {
            $response = responseFormat('error', 'Directorates Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $response = responseFormat('success', 'No Action');
        return response()->json($response, 200);
    }

    public function show(XResponsibleOffice $xResponsibleOffice)
    {
        $response = responseFormat('success', 'No Action');
        return response()->json($response, 200);
    }

    public function update(Request $request, XResponsibleOffice $xResponsibleOffice)
    {
        $response = responseFormat('success', 'No Action');
        return response()->json($response, 200);
    }

    public function destroy(XResponsibleOffice $xResponsibleOffice)
    {
        $response = responseFormat('success', 'No Action');
        return response()->json($response, 200);
    }
}
