<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RfidCardAssignment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RfidCardController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        
        $assignments = RfidCardAssignment::with(['student.classroom', 'device'])
            ->whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Admin/RfidCards/Index', [
            'assignments' => $assignments,
        ]);
    }

    public function destroy(Request $request, RfidCardAssignment $rfidCard)
    {
        // Actually we might just want to mark it as blocked or delete it.
        // For simple CRUD, we can delete the assignment and clear student's rfid field
        $student = $rfidCard->student;
        
        // If it's the active card, also clear the student's rfid
        if ($rfidCard->status->value === 'active' && $student->rfid === $rfidCard->card_uid) {
            $student->update(['rfid' => null]);
        }

        $rfidCard->delete();

        return redirect()->back()->with('success', 'Penugasan kartu RFID berhasil dihapus.');
    }
}
