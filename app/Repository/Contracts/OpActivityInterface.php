<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface OpActivityInterface
{
    public function allActivities(Request $request);

}
