<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date' => 'required|date',
        ]);

        $fileName = 'presensi_kelas_' . $request->classroom_id . '_' . $request->date . '.xlsx';

        return Excel::download(
            new AttendanceExport($request->classroom_id, $request->date), 
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date' => 'required|date',
        ]);

        $export = new AttendanceExport($request->classroom_id, $request->date);
        
        $pdf = Pdf::loadView('exports.attendance', $export->view()->getData());

        $fileName = 'presensi_kelas_' . $request->classroom_id . '_' . $request->date . '.pdf';

        return $pdf->download($fileName);
    }
}
