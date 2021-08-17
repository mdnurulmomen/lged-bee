<?php

namespace App\Repository;

use App\Repository\Contracts\AuditObservationInterface;
use App\Models\AuditObservation;
use App\Models\AuditObservationAttachment;
use App\Models\AuditObservationCommunication;
use App\Models\AuditObservationCommunicationAttachment;
use App\Models\AuditObservationCommunicationCC;
use App\Models\ApEntityAuditPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Traits\GenericData;

class AuditObservationRepo implements AuditObservationInterface
{

    use GenericData;

    public function __construct(AuditObservation $observation, AuditObservationCommunication $communication)
    {
        $this->observation = $observation;
        $this->communication = $communication;
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
        $observation->audit_id = $request->audit_id;
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
        $observation->audit_id = $request->audit_id;
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

    public function getAuditPlan(Request $request)
    {
        $this->switchOffice($request->office_id);

        $party = ApEntityAuditPlan::whereHas('party', function ($query) use ($request) {
            $query->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('party_id', $request->rp_office_id);
        })->select('id', 'plan_description')->get();
        $this->emptyOfficeDBConnection();
        return $party;
    }

    public function generateObservationNumber()
    {
        $number = mt_rand(1000000000, 9999999999);
        if ($this->NumberExists($number)) {
            return $this->generateObservationNumber();
        }
        return $number;
    }

    public function observationCommunication(Request $request)
    {
        $communication = new AuditObservationCommunication();
        $communication->observation_id = $request->observation_id;
        $communication->parent_office_id = $request->parent_office_id;
        $communication->rp_office_id = $request->rp_office_id;
        $communication->directorate_id = $request->directorate_id;
        $communication->message_title = $request->message_title;
        $communication->message_body = $request->message_body;
        $communication->sent_to = $request->sent_to;
        $communication->sent_by = $request->authorization['payload']['user_id'];
        $communication->status = 1;

        $ccIds = [];
        $attachments = [];
        if ($communication->save()) {
            if ($request->hasfile('attachments')) {
                foreach ($request->file('attachments') as $key => $attachment) {
                    $fileName = "comm_" . uniqid() . $key . '.' . $attachment->extension();
                    Storage::disk('public')->put($fileName,  File::get($attachment));
                    $attachments[] = new AuditObservationCommunicationAttachment([
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_location' => 'storage/app/public/' . $fileName,
                        'file_url' => url('storage/' . $fileName),
                        'file_type' => $attachment->extension()
                    ]);
                }
            }
            if (!empty($request->cc)) {
                foreach ($request->cc as $cc) {
                    $ccIds[] = new AuditObservationCommunicationCC([
                        'communication_cc' => $cc
                    ]);
                }
            }

            $communication->attachments()->saveMany($attachments);
            $communication->cc()->saveMany($ccIds);
        }

        return $communication;
    }

    public function observationCommunicationLists(Request $request)
    {
        $user = $request->authorization['payload']['user_id'];
        return $this->communication->with(['observation'])->where('sent_by', $user)->latest()->get();
    }

    public function NumberExists($number)
    {
        return AuditObservation::where('observation_no', $number)->exists();
    }
}
