<?php

namespace App\Repository;

use App\Repository\Contracts\AuditObservationInterface;
use App\Models\AuditObservation;
use App\Models\AuditObservationAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AuditObservationRepo implements AuditObservationInterface
{

    public function __construct(AuditObservation $observation)
    {
        $this->observation = $observation;
    }

    public function index()
    {
        return $this->observation->latest()->get();
    }

    public function show(Request $request)
    {
        return $this->observation->with(['fiscalYear', 'attachments'])->findOrFail($request->id);
    }

    public function search(Request $request)
    {
        $observation = $this->observation->query();
        if (!empty($request->observation_no)) {
            $observation->where('observation_no', $request->observation_no);
        }
        if (!empty($request->ministry_id)) {
            $observation->where('ministry_id', $request->ministry_id);
        }
        if (!empty($request->division_id)) {
            $observation->where('division_id', $request->division_id);
        }
        if (!empty($request->parent_office_id)) {
            $observation->where('parent_office_id', $request->parent_office_id);
        }
        if (!empty($request->rp_office_id)) {
            $observation->where('rp_office_id', $request->rp_office_id);
        }
        if (!empty($request->directorate_id)) {
            $observation->where('directorate_id', $request->directorate_id);
        }
        if (!empty($request->team_leader_id)) {
            $observation->where('team_leader_id', $request->team_leader_id);
        }
        if (!empty($request->observation_type)) {
            $observation->where('observation_type', $request->observation_type);
        }
        if (!empty($request->status)) {
            $observation->where('status', $request->status);
        }
        if (!empty($request->fiscal_year_id)) {
            $observation->where('fiscal_year_id', $request->fiscal_year_id);
        }
        if (!empty($request->observation)) {
            $observation->where('observation_en', 'like', '%' . $request->observation . '%')
                ->orWhere('observation_bn', 'like', '%' . $request->observation . '%');
        }

        return $observation->latest()->get();
    }

    public function store(Request $request)
    {
        $observation = new AuditObservation();
        $observation->observation_no = $this->generateObservationNumber();
        $observation->ministry_id = $request->ministry_id;
        $observation->division_id = $request->division_id;
        $observation->parent_office_id = $request->parent_office_id;
        $observation->rp_office_id = $request->rp_office_id;
        $observation->directorate_id = $request->directorate_id;
        $observation->team_leader_id = $request->team_leader_id;
        $observation->observation_en = $request->observation_en;
        $observation->observation_bn = $request->observation_bn;
        $observation->observation_details = $request->observation_details;
        $observation->observation_type = $request->observation_type;
        $observation->fiscal_year_id = $request->fiscal_year_id;
        $observation->amount = $request->amount;
        $observation->initiation_date = $request->initiation_date;
        //$observation->close_date = $request->close_date;
        $observation->status = $request->status == "on" ? 1 : 0;

        $attachments = [];
        if ($observation->save()) {
            if ($request->hasfile('cover_page')) {
                $attachment = $request->file('cover_page');
                $fileName = uniqid() . '.' . $attachment->extension();
                Storage::disk('public')->put($fileName,  File::get($attachment));
                $attachments[] = new AuditObservationAttachment([
                    'file_category' => 'cover',
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_location' => 'storage/app/public/' . $fileName,
                    'file_url' => url('storage/' . $fileName),
                    'file_type' => $attachment->extension()
                ]);
            }
            if ($request->hasfile('main_attachments')) {
                foreach ($request->file('main_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'main',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if ($request->hasfile('appendix_attachments')) {
                foreach ($request->file('appendix_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'appendix',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if ($request->hasfile('authentic_attachments')) {
                foreach ($request->file('authentic_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'authentic',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if ($request->hasfile('other_attachments')) {
                foreach ($request->file('other_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'others',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            $observation->attachments()->saveMany($attachments);
        }

        return $observation;
    }

    public function update(Request $request)
    {
        $observation = $this->observation->where('id', $request->id)->first();
        //$observation->observation_no = $this->generateObservationNumber();
        $observation->ministry_id = $request->ministry_id;
        $observation->division_id = $request->division_id;
        $observation->parent_office_id = $request->parent_office_id;
        $observation->rp_office_id = $request->rp_office_id;
        $observation->directorate_id = $request->directorate_id;
        $observation->team_leader_id = $request->team_leader_id;
        $observation->observation_en = $request->observation_en;
        $observation->observation_bn = $request->observation_bn;
        $observation->observation_details = $request->observation_details;
        $observation->observation_type = $request->observation_type;
        $observation->fiscal_year_id = $request->fiscal_year_id;
        $observation->amount = $request->amount;
        $observation->initiation_date = $request->initiation_date;
        //$observation->close_date = $request->close_date;
        $observation->status = $request->status == "on" ? 1 : 0;

        $attachments = [];

        if ($observation->save()) {
            if ($request->hasfile('cover_page')) {
                $attachment = $request->file('cover_page');
                $fileName = uniqid() . '.' . $attachment->extension();
                Storage::disk('public')->put($fileName,  File::get($attachment));
                $attachments[] = new AuditObservationAttachment([
                    'file_category' => 'cover',
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_location' => 'storage/app/public/' . $fileName,
                    'file_url' => url('storage/' . $fileName),
                    'file_type' => $attachment->extension()
                ]);
            }
            if ($request->hasfile('main_attachments')) {
                foreach ($request->file('main_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'main',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if ($request->hasfile('appendix_attachments')) {
                foreach ($request->file('appendix_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'appendix',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if ($request->hasfile('authentic_attachments')) {
                foreach ($request->file('authentic_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'authentic',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if ($request->hasfile('other_attachments')) {
                foreach ($request->file('other_attachments') as $key => $attachment) {
                    $fileName = uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationAttachment([
                        'file_category' => 'others',
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            $observation->attachments()->saveMany($attachments);
        }

        return $observation;
    }

    public function removeAttachment(Request $request)
    {
        return AuditObservationAttachment::where('id', $request->id)->delete();
    }

    public function destroy(Request $request)
    {
        return $this->observation->findOrFail($request->id)->delete();
    }

    public function generateObservationNumber()
    {
        $number = mt_rand(1000000000, 9999999999);
        if ($this->NumberExists($number)) {
            return $this->generateObservationNumber();
        }
        return $number;
    }

    public function NumberExists($number)
    {
        return AuditObservation::where('observation_no', $number)->exists();
    }
}
