<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Models\Complaint;
use App\Models\ComplaintSupport;
use Illuminate\Http\Request;

class ComplaintSupportController extends Controller
{
    public function store(Request $request)
    {
        $complaint = Complaint::find($request->complaint_id);

        if (!$complaint) return ResponseBuilder::buildErrorResponse('Complaint Not Found', [], 404);

        $request->user()->supports()->create([
            'complaint_id' => $complaint->id,
        ]);

        return ResponseBuilder::buildResponse('Support complaint successfuly', []);
    }

    public function destroy($id)
    {
        $support = ComplaintSupport::find($id);
        if (!$support) return ResponseBuilder::buildErrorResponse('Your not supported this complaint', [], 404);

        $support->delete();

        return ResponseBuilder::buildResponse('Unsupport complaint successfuly', []);
    }
}
