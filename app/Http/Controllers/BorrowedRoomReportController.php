<?php

namespace App\Http\Controllers;

use App\Http\Services\BorrowedRoomService;
use App\Http\Services\RoomService;
use App\Mail\BorrowedRoomReportMail;
use App\Models\ReportReceiver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BorrowedRoomReportController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, RoomService $roomService, BorrowedRoomService $borrowedRoomService)
    {
        $admin = User::where('role', 2)->first();

        if (!$admin){
            return $this->sendError(Response::HTTP_UNPROCESSABLE_ENTITY, 'Theres no user with admin role!');
        }
        
        Auth::loginUsingId($admin->id);
        $data = $this->getSearchAndSort();
        $rooms = $roomService->index($data);

        // get startDate and endDate from $request, if theres no exists, set default value to today
        $startDate = $request->query('start_date', Carbon::today()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());


        // $css = file_get_contents(public_path('build/assets/app-KbJAj8Tt.css'));
        $borrowedRooms = $borrowedRoomService->activeRequest($startDate, $endDate);

        if ($request->has('preview')){
            $css = '/app.css';
            return view('pdf.report', compact('borrowedRooms', 'css'));
        }

        $css = public_path('/app.css');
        $pdf = Pdf::loadView('pdf.report', compact('borrowedRooms', 'css'));
        $pdf->setPaper('A4', 'portrait');

        $pdfFileName = time() . '_booking_report_' . $startDate . '.pdf';
        $pdfPath = "public/reports/{$pdfFileName}";


        Storage::put($pdfPath, $pdf->output());

        $reportReceivers = ReportReceiver::all();
        foreach ($reportReceivers as $reportReceiver){
            Mail::to($reportReceiver->email)->send(new BorrowedRoomReportMail($pdfPath, $pdfFileName, $reportReceiver->name));
        }        

        return $this->sendResponse(Response::HTTP_OK, 'Successfully send message', []);
    }
}
