<?php

namespace App\Http\Controllers;

use App\Traits\ApiHeart;
use App\Traits\GenericData;
use App\Traits\JwtTokenizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiHeart, GenericData, JwtTokenizable;
}
