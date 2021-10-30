<?php

namespace App\Repository;

use App\Models\Document;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FinalPlanRepo
{

    use GenericData;

    public function store(Request $request)
    {
        if ($request->hasfile('file')) {
            $attachment = $request->file;
            $fileSize = $attachment->getSize();
            $fileName = uniqid() . '.' . $attachment->extension();

            if ($request->document_type == 'strategic') {
                Storage::disk('public')->put('plan/strategic/' . $fileName, File::get($attachment));
            } elseif ($request->document_type == 'operation') {
                Storage::disk('public')->put('plan/operational/' . $fileName, File::get($attachment));
            } else {
                Storage::disk('public')->put($fileName, File::get($attachment));
            }

            $document = new Document();
            $document->document_type = $request->document_type;
            $document->relational_id = 1;
            $document->fiscal_year = $request->fiscal_year;
            $document->attachment_type = $attachment->extension();
            $document->user_file_name = $attachment->getClientOriginalName();
            $document->file_custom_name = $fileName;
            $document->file_location = 'storage/app/public/plan/' . $request->document_type . '/' . $fileName;
            $document->file_url = url('storage/plan/' . $request->document_type . '/' . $fileName);
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

            if ($request->document_type == 'strategic') {
                Storage::disk('public')->put('strategic/' . $fileName, File::get($attachment));
            } elseif ($request->document_type == 'operation') {
                Storage::disk('public')->put('operational/' . $fileName, File::get($attachment));
            } else {
                Storage::disk('public')->put($fileName, File::get($attachment));
            }

            $document = Document::find($request->id);
            $document->document_type = $request->document_type;
            $document->relational_id = 1;
            $document->fiscal_year = $request->fiscal_year;
            $document->attachment_type = $attachment->extension();
            $document->user_file_name = $attachment->getClientOriginalName();
            $document->file_custom_name = $fileName;
            $document->file_location = 'storage/app/public/' . $request->document_type . '/' . $fileName;
            $document->file_url = url('storage/' . $request->document_type . '/' . $fileName);
            $document->file_size_in_kb = $fileSize;
            $document->modified_by = 1;
            $document->save();
        }
    }

    public function list(Request $request)
    {
        return Document::where('document_type', $request->document_type)->get();
    }

    public function documentIsExist(Request $request)
    {
        return Document::where('document_type', $request->document_type)
            ->where('fiscal_year', $request->fiscal_year)
            ->first();
    }
}
