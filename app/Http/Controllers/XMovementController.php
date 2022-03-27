<?php

namespace App\Http\Controllers;

use App\Http\Requests\XFiscalYear\SaveRequest;
use App\Http\Requests\XFiscalYear\ShowOrDeleteRequest;
use App\Http\Requests\XFiscalYear\UpdateRequest;
use App\Models\XFiscalYear;
use Illuminate\Http\Request;

class XMovementController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            XFiscalYear::create($request->validated());
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }
}
