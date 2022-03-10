<?php

namespace App\Services;

use App\Models\PacMeeting;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;
class PacService
{
    use GenericData, ApiHeart;

    public function getPacMeetingList(Request $request): array
    {

        try {
            $meeting_list = PacMeeting::all();
            return ['status' => 'success', 'data' => $meeting_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
