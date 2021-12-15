<?php

namespace App\Http\Controllers;

use App\Services\QacService;
use Illuminate\Http\Request;

class QacController extends Controller
{
public function qacApotti(Request $request, QacService $qacService){
    $qac_apotti = $qacService->qacApotti($request);
    if (isSuccessResponse($qac_apotti)) {
        $response = responseFormat('success', $qac_apotti['data']);
    } else {
        $response = responseFormat('error', $qac_apotti['data']);
    }

    return response()->json($response);
    }

}
