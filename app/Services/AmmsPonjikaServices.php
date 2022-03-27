<?php

namespace App\Services;

use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AmmsPonjikaServices
{
    use GenericData, ApiHeart;

    public function createTask($data, $cdesk): array
    {
        $task_organizer = [
            'user_email' => $cdesk->email,
            'user_name_en' => $cdesk->officer_en,
            'user_name_bn' => $cdesk->officer_bn,
            'username' => $cdesk->user_id,
            'user_phone' => $cdesk->phone,
            'user_officer_id' => $cdesk->officer_id,
            'user_designation_id' => $cdesk->designation_id,
            'user_office_id' => $cdesk->office_id,
            'user_office_name_en' => $cdesk->office_name_en,
            'user_office_name_bn' => $cdesk->office_name_bn,
            'user_unit_id' => $cdesk->office_unit_id,
            'user_office_unit_name_en' => $cdesk->office_unit_en,
            'user_office_unit_name_bn' => $cdesk->office_unit_bn,
            'user_designation_name_en' => $cdesk->designation_en,
            'user_designation_name_bn' => $cdesk->designation_bn,
            'user_type' => 'organizer',
        ];

        $task_data = [
            'task_organizer' => json_encode($task_organizer),
            'task_title_en' => $data['task_title_en'],
            'task_title_bn' => $data['task_title_bn'],
            'description' => $data['description'],
            'meta_data' => $data['meta_data'],
            'task_start_end_date_time' => $data['task_start_end_date_time'],
            'notifications' => $data['notifications'],
            'system_type' => 'amms-api',
        ];
        if (Arr::has($data, 'task_assignee')) {
            $task_data['task_assignee'] = json_encode($data['task_assignee']);
        }

        $storeTask = $this->initPonjikaHttp()->post(config('cag_ponjika_api.tasks.store'), $task_data)->json();

        if (isSuccess($storeTask)) {
            \Log::error($storeTask['data']);
            return responseFormat('success', 'Successfully created task');
        } else {
            \Log::error(json_encode($storeTask));
            return responseFormat('error', $storeTask);
        }
    }
}
