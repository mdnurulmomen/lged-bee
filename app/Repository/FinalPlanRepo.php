<?php

namespace App\Repository;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Traits\GenericData;

class FinalPlanRepo
{

    use GenericData;

    public function store(Request $request)
    {
        if ($request->hasfile('file')) {
            $attachment = $request->file;
            $fileSize = $attachment->getSize();
            $fileName = uniqid() . '.' . $attachment->extension();
            //Storage::disk('final_documents')->put($fileName,  File::get($attachment));
            Storage::disk('public')->put($fileName,  File::get($attachment));

            $document = new Document();
            $document->document_type = $request->document_type;
            $document->relational_id = 1;
            $document->fiscal_year = $request->fiscal_year;
            $document->attachment_type = $attachment->extension();
            $document->user_file_name = $attachment->getClientOriginalName();
            $document->file_custom_name = $fileName;
            $document->file_location = 'storage/app/public/' . $fileName;
            $document->file_url = url('storage/' . $fileName);
            $document->file_size_in_kb = $fileSize;
            $document->created_by = 1;
            $document->save();
        }
    }

    public function edit(Request $request)
    {
        return Document::find($request->id);
    }

    public function update(Request $request)
    {
        if ($request->hasfile('file')) {
            $attachment = $request->file;
            $fileSize = $attachment->getSize();
            $fileName = uniqid() . '.' . $attachment->extension();
            Storage::disk('public')->put($fileName,  File::get($attachment));

            $document = Document::find($request->id);
            $document->document_type =  $request->document_type;
            $document->relational_id = 1;
            $document->fiscal_year = $request->fiscal_year;
            $document->attachment_type = $attachment->extension();
            $document->user_file_name = $attachment->getClientOriginalName();
            $document->file_custom_name = $fileName;
            $document->file_location = 'storage/app/public/' . $fileName;
            $document->file_url = url('storage/' . $fileName);
            $document->file_size_in_kb = $fileSize;
            $document->modified_by = 1;
            $document->save();
        }
    }

    public function list(Request $request)
    {
        return Document::where('document_type',$request->document_type)->get();
    }

    public function documentIsExist(Request $request)
    {
        return Document::where('document_type', $request->document_type)
            ->where('fiscal_year',$request->fiscal_year)
            ->first();
    }
}
