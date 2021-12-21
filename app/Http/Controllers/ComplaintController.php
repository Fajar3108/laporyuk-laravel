<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('is_masyarakat')->only('store');
        $this->middleware('is_not_masyarakat')->only('update');
    }

    public function index()
    {
        if (request()->user()->role->slug == 'masyarakat') {
            $complaints = Complaint::where('visibility', '!=', 'private')->latest();
        } else {
            $complaints = Complaint::latest();
        }

        if (request()->keyword) {
            $complaints = $complaints->where('title', 'LIKE', '%' . request()->keyword . '%');
        }

        $complaints = $complaints->paginate(10);

        return ComplaintResource::collection($complaints);
    }

    public function show($id)
    {
        $complaint = Complaint::find($id);

        if (!$complaint || ($complaint->visibility == 'private' && request()->user()->role->slug == 'masyarakat')) return ResponseBuilder::buildErrorResponse('Complaint Not Found', [], 404);

        return new ComplaintResource($complaint);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => ['required'],
            'description' => ['required'],
            'visibility' => ['required', 'in:public,private,anonim'],
            'date' => ['required'],
            'province' => ['required'],
            'city' => ['required'],
        ]);

        if ($validate->fails()) return ResponseBuilder::buildErrorResponse('Your request is invalid', $validate->errors, 400);

        $complaint = $request->user()->complaints()->create([
            'title' => $request->title,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'date' => $request->date,
            'province' => $request->province,
            'city' => $request->city,
        ]);

        if ($request->hasFile('files')) {
            $allowedfileExtension = ['jpeg','jpg','png', 'svg', 'mp4'];
            $files = $request->file('files');

            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();

                $check = in_array($extension,$allowedfileExtension);

                if($check) {
                    foreach($files as $media) {
                        $path = $media->store('complaint_files', ['disk' => 'public']);

                        //store image file into directory and db
                        $complaint->files()->create([
                            'path' => $path,
                        ]);
                    }
                } else {
                    return ResponseBuilder::buildErrorResponse('Invalid File Type', [],422);
                }
            }
        }


        return ResponseBuilder::buildResponse('Complaint created successfully', $complaint);
    }

    public function update($id, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'status' => ['required', 'in:menunggu,selesai,diproses,ditolak'],
        ]);

        if ($validate->fails()) return ResponseBuilder::buildErrorResponse('Your request is invalid', $validate->errors(), 400);

        $complaint = Complaint::find($id);

        if (!$complaint) return ResponseBuilder::buildErrorResponse('Complaint Not Found', [], 404);

        $complaint->update([
            'status' => $request->status,
        ]);

        return ResponseBuilder::buildResponse('Complaint updated successfully', $complaint);
    }

    public function destroy(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        if (!$complaint) return ResponseBuilder::buildErrorResponse('Complaint Not Found', [], 404);

        if (($complaint->status !== 'menunggu' && request()->user()->role->slug == 'masyarakat') || ($request->user()->id !== $complaint->user_id && request()->user()->role->slug == 'masyarakat')) return ResponseBuilder::buildErrorResponse("Can't delete this complaint", [], 401);

        $complaint->delete();

        return ResponseBuilder::buildResponse('Complaint deleted successfully', $complaint);
    }
}
