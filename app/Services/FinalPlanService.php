<?php

namespace App\Services;

use App\Models\ApOfficeOrder;
use App\Models\Document;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FinalPlanService
{
    use GenericData;

    public function store(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            if ($request->hasfile('file')) {
                $attachment = $request->file;
                $fileSize = $attachment->getSize();
                $fileName = uniqid() . '.' . $attachment->extension();
                Storage::disk('final_documents')->put($fileName,  File::get($attachment));

                $document = new Document();
                $document->document_type = $request->document_type;
                $document->relational_id = 1;
                $document->fiscal_year = $request->fiscal_year;
                $document->attachment_type = $attachment->extension();
                $document->user_file_name = $attachment->getClientOriginalName();
                $document->file_custom_name = $fileName;
                $document->file_location = 'storage/app/public/final_documents/' . $fileName;
                $document->file_url = url('storage/final_documents/' . $fileName);
                $document->file_size_in_kb = $fileSize;
                $document->created_by = 1;
                $document->save();
                $responseData = ['status' => 'success', 'data' => 'Successfully Saved!'];
            }
            else{
                $responseData = ['status' => 'error', 'data' => 'No FIle'];
            }

        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }


}
