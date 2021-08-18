<?php

namespace App\Http\Controllers;

use App\Models\AuditTemplate;
use Illuminate\Http\Request;

class AuditTemplateController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->template;
        $lang = $request->language;
        try {
            $template = AuditTemplate::select('content')->where('lang', $lang)->where('template_type', $type)->where('status', 1)->first()->toArray();
            return response()->json(responseFormat('success', $template));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception));
        }
    }
}
