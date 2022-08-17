<?php

namespace App\Http\Controllers;

use App\Models\XCalendarVacation;
use App\Models\XFiscalYear;
use Illuminate\Http\Request;

class XCalendarVacationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function yearWiseVacationList(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $list = XCalendarVacation::where('year',$request->year)->pluck('vacation_date');
            if ($list) {
                $response = responseFormat('success', $list);
            } else {
                $response = responseFormat('error', 'Fiscal Year Not Found');
            }
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response, 200);
    }
}
