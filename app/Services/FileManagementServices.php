<?php

namespace App\Services;

use App\Models\AcMemoAttachment;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileManagementServices
{
    use GenericData, ApiHeart;

    public function storeFiles(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $finalAttachments = [];
            if ($request->attachemnt_type == 'memo') {
                if ($request->hasfile('porisishtos')) {
                    foreach ($request->porisishtos as $key => $file) {
                        $userDefineFileName = $file->getClientOriginalName();
                        $fileExtension = $file->extension();
                        $fileSize = $file->getSize();
                        $fileName = 'porisishto_' . uniqid() . '.' . $fileExtension;

                        Storage::disk('public')->put('memo/dicfia/' . $fileName, File::get($file));
                        array_push($finalAttachments, array(
                                'ac_memo_id' => $request->memo_id,
                                'file_type' => 'porisishto',
                                'file_user_define_name' => $userDefineFileName,
                                'file_custom_name' => $fileName,
                                'file_path' => url('storage/memo/dicfia/' . $fileName),
                                'file_size' => $fileSize,
                                'file_extension' => $fileExtension,
                                'sequence' => $key + 1,
                                'created_by' => 0,
                                'modified_by' => 0,
                            )
                        );
                    }
                }

                //for pramanoks
                if ($request->hasfile('pramanoks')) {
                    foreach ($request->pramanoks as $key => $file) {
                        $userDefineFileName = $file->getClientOriginalName();
                        $fileExtension = $file->extension();
                        $fileSize = $file->getSize();
                        $fileName = 'pramanok_' . uniqid() . '.' . $fileExtension;

                        Storage::disk('public')->put('memo/dicfia/' . $fileName, File::get($file));

                        array_push($finalAttachments, array(
                                'ac_memo_id' => $audit_memo->id,
                                'file_type' => 'pramanok',
                                'file_user_define_name' => $userDefineFileName,
                                'file_custom_name' => $fileName,
                                'file_path' => url('storage/memo/dicfia/' . $fileName),
                                'file_size' => $fileSize,
                                'file_extension' => $fileExtension,
                                'sequence' => $key + 1,
                                'created_by' => $cdesk->officer_id,
                                'modified_by' => $cdesk->officer_id,
                            )
                        );
                    }
                }

                if (!empty($finalAttachments)) {
                    AcMemoAttachment::insert($finalAttachments);
                }
            }
            \DB::commit();
            return ['status' => 'success', 'data' => ''];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoAttachmentDelete(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            AcMemoAttachment::where('id', $request->memo_attachment_id)
                ->update(['deleted_by' => $cdesk->officer_id]);
            AcMemoAttachment::find($request->memo_attachment_id)->delete();
            \DB::commit();
            return ['status' => 'success', 'data' => 'Attachment Delete Successfully'];

        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
