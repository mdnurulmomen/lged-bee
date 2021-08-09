<?php

namespace App\Repository;

use App\Models\OpYearlyAuditCalendarEditHistory;
use App\Repository\Contracts\EditHistoryInterface;

class OpYearlyAuditCalendarEditHistoryRepository implements EditHistoryInterface
{
    public function addOpYearlyAuditCalendarEditHistory($cdesk, $changed_data)
    {
        $cdesk = json_decode($cdesk, false);
        $updater_data = [
            'unit_id' => $cdesk->office_unit_id,
            'employee_id' => $cdesk->officer_id,
            'employee_name_en' => $cdesk->officer_en,
            'employee_name_bn' => $cdesk->officer_bn,
            'employee_designation_id' => $cdesk->designation_id,
            'employee_designation_en' => $cdesk->designation_en,
            'employee_designation_bn' => $cdesk->designation_bn,
            'user_id' => $cdesk->user_id,
        ];
        $data = $updater_data + $changed_data;
        OpYearlyAuditCalendarEditHistory::create($data);
    }

}
