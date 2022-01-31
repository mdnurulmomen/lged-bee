<?php

namespace App\Http\Controllers;

use App\Services\QacService;
use Illuminate\Http\Request;

class QacController extends Controller
{
    public function qacApotti(Request $request, QacService $qacService)
    {
        $qac_apotti = $qacService->qacApotti($request);
        if (isSuccessResponse($qac_apotti)) {
            $response = responseFormat('success', $qac_apotti['data']);
        } else {
            $response = responseFormat('error', $qac_apotti['data']);
        }

        return response()->json($response);
    }

    public function getQacApottiStatus(Request $request, QacService $qacService)
    {
        $qac_apotti = $qacService->getQacApottiStatus($request);
        if (isSuccessResponse($qac_apotti)) {
            $response = responseFormat('success', $qac_apotti['data']);
        } else {
            $response = responseFormat('error', $qac_apotti['data']);
        }

        return response()->json($response);
    }

    public function storeQacCommittee(Request $request, QacService $qacService)
    {
        $qac_committee = $qacService->storeQacCommittee($request);
        if (isSuccessResponse($qac_committee)) {
            $response = responseFormat('success', $qac_committee['data']);
        } else {
            $response = responseFormat('error', $qac_committee['data']);
        }

        return response()->json($response);
    }

    public function getQacCommitteeList(Request $request, QacService $qacService)
    {
        $qac_committee = $qacService->getQacCommitteeList($request);
        if (isSuccessResponse($qac_committee)) {
            $response = responseFormat('success', $qac_committee['data']);
        } else {
            $response = responseFormat('error', $qac_committee['data']);
        }

        return response()->json($response);
    }

    public function getQacCommitteeWiseMember(Request $request, QacService $qacService)
    {
        $qac_committee = $qacService->getQacCommitteeWiseMember($request);
        if (isSuccessResponse($qac_committee)) {
            $response = responseFormat('success', $qac_committee['data']);
        } else {
            $response = responseFormat('error', $qac_committee['data']);
        }

        return response()->json($response);
    }

    public function storeAirWiseCommittee(Request $request, QacService $qacService)
    {
        $qac_committee = $qacService->storeAirWiseCommittee($request);
        if (isSuccessResponse($qac_committee)) {
            $response = responseFormat('success', $qac_committee['data']);
        } else {
            $response = responseFormat('error', $qac_committee['data']);
        }

        return response()->json($response);
    }

    public function getAirWiseCommittee(Request $request, QacService $qacService)
    {
        $qac_committee = $qacService->getAirWiseCommittee($request);
        if (isSuccessResponse($qac_committee)) {
            $response = responseFormat('success', $qac_committee['data']);
        } else {
            $response = responseFormat('error', $qac_committee['data']);
        }

        return response()->json($response);
    }

}
