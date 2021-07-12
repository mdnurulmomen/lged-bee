<?php

namespace App\Traits;

use App\Models\XFiscalYear;

trait GenericInfo
{
    public function durationIdFromFiscalYear($fiscal_year_id)
    {
       return XFiscalYear::select('duration_id')->where('id', $fiscal_year_id)->first()->duration_id;
    }
}

