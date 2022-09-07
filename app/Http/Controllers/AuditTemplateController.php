<?php

namespace App\Http\Controllers;

use App\Models\AuditTemplate;
use Illuminate\Http\Request;

class AuditTemplateController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        //return response()->json(responseFormat('error', $request->template_type));
        try {
            $template = AuditTemplate::select('content')
                ->where('lang', $request->language)
                ->where('template_type', $request->template_type)
                ->where('template_name', $request->template_name)
                ->where('status', 1)
                ->first()
                ->toArray();
            return response()->json(responseFormat('success', $template));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception));
        }
    }
}
