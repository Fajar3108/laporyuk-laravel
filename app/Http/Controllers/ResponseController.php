<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Models\Complaint;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    public function index()
    {
        # code...
    }

    public function show($id)
    {
        # code...
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'complaint_id' => ['required'],
            'content' => ['required'],
        ]);

        if ($validator->fails()) return ResponseBuilder::buildErrorResponse('Your request is invalid', $validator->errors(), 400);

        $complaint = Complaint::find($request->complaint_id);
        if (!$complaint) return ResponseBuilder::buildErrorResponse('Complaint Not Found', [], 404);

        $response = $request->user()->complaints()->create([
            'complaint_id' => $complaint->complaint_id,
            'content' => $request->content,
        ]);

        if ($request->hasFile('files')) {
            $allowedfileExtension = ['jpeg','jpg','png', 'svg', 'mp4'];
            $files = $request->file('files');

            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();

                $check = in_array($extension,$allowedfileExtension);

                if($check) {
                    foreach($files as $media) {
                        $path = $media->store('response_files', ['disk' => 'public']);

                        //store image file into directory and db
                        $response->files()->create([
                            'path' => $path,
                        ]);
                    }
                } else {
                    return ResponseBuilder::buildErrorResponse('Invalid File Type', [],422);
                }
            }
        }

        return ResponseBuilder::buildResponse('Response created successfuly', []);
    }

    public function destroy($id)
    {
        $response = Response::find($id);

        if (!$response) return ResponseBuilder::buildErrorResponse('Response Not Found', [], 404);

        $response->delete();

        return ResponseBuilder::buildResponse('Response deleted successfuly', []);
    }
}
