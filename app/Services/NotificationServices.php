<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Traits\ApiHeart;
use Illuminate\Http\Request;

class NotificationServices
{
    use ApiHeart;

    public function sendMailToRpu(Request $request)
    {
        try {
            $meta_data = json_decode($request->meta_data, true);

            if (\Arr::has($meta_data, 'cost_center_id') || \Arr::has($meta_data, 'entity_id')) {
                $office_ids = [];
                if ($request->notifiable_type == 'air') {
                    $office_ids = [$meta_data['entity_id']];
                }
                if ($request->notifiable_type == 'memo' || $request->notifiable_type == 'query') {
                    $office_ids = [$meta_data['cost_center_id']];
                }
                $office_ids = implode(',', $office_ids);
                $office_infos = $this->initRPUHttp()->post(config('cag_rpu_api.get-offices-info'), ['office_ids' => $office_ids])->json();
                if (isSuccess($office_infos, 'status', 'error')) {
                    throw new \Exception('ERROR CODE: RNO - ' . $office_infos['message']);
                }
                foreach ($office_infos['data'] as $office_info) {
                    $mail_data = [
                        'subject' => $request->mail_subject ?: 'You have received an email',
                        'body' => $request->mail_body ?: '',
                        'email' => $office_info['office_email'],
                    ];
                    SendMailJob::dispatch($mail_data);
                }
                return responseFormat('success', 'Mail dispatched!');
            } else {
                throw new \Exception('ERROR CODE: MDI - No Proper Meta Data!');
            }
        } catch (\Exception $exception) {
            \Log::error('ERROR CODE: NS - ' . $exception->getMessage());
            return responseFormat('error', $exception->getMessage());
        }
    }
}
