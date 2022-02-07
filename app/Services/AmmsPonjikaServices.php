<?php

namespace App\Services;

use App\Traits\ApiHeart;
use App\Traits\GenericData;

class AmmsPonjikaServices
{
    use GenericData, ApiHeart;

    public function storeTask($validated_data)
    {
        $data = [
            'task_title_en' => $validated_data['title'],
            'task_title_bn' => $validated_data['title'],
            'task_description' => $validated_data['description'],
            'start_date' => $validated_data['start_date'],
            'start_time' => $validated_data['start_time'],
            'end_date' => $validated_data['end_date'],
            'end_time' => $validated_data['end_time'],
            'task_organizer' => $validated_data['task_organizer'],
            'task_to_event' => $validated_data['task_to_event'],
            'task_status' => $validated_data['task_status'],
            'system_type' => 'amms-api',
            'notifications' => $validated_data['notifications'],
            'task_assignee' => $validated_data['task_assignee'],
            'meta_data' => $validated_data['meta_data'],
        ];
        $storeTask = $this->initPonjikaHttp()->post(config('cag_ponjika_api.tasks.store'), $data)->json();
    }
}
