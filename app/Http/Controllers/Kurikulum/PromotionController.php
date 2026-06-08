<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Enums\PromotionStatus;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PromotionController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService,
    ) {}

    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('order')->get();

        $students = Student::with('classroom')
            ->when($request->classroom_id, fn($q, $id) => $q->where('classroom_id', $id))
            ->whereNull('graduated_at')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Kurikulum/Promotion/Index', [
            'classrooms' => $classrooms,
            'students' => $students,
            'filters' => $request->only('classroom_id'),
            'statuses' => collect(PromotionStatus::cases())->map(fn($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    /**
     * Update individual student promotion status.
     */
    public function updateStatus(Request $request, Student $student)
    {
        $validated = $request->validate([
            'promotion_status' => 'required|in:pending,naik,tidak_naik,lulus',
        ]);

        $student->update(['promotion_status' => $validated['promotion_status']]);

        return redirect()->back()->with('success', "Status {$student->name} diperbarui.");
    }

    /**
     * Bulk update promotion status.
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'students' => 'required|array',
            'students.*.id' => 'required|exists:students,id',
            'students.*.promotion_status' => 'required|in:pending,naik,tidak_naik,lulus',
        ]);

        foreach ($validated['students'] as $data) {
            Student::where('id', $data['id'])->update([
                'promotion_status' => $data['promotion_status'],
            ]);
        }

        return redirect()->back()->with('success', 'Status kenaikan kelas berhasil diperbarui.');
    }

    /**
     * Apply all set promotions.
     */
    public function apply(Request $request)
    {
        try {
            $results = $this->promotionService->applyPromotions(
                $request->user()->school_id,
                $request->user()->id,
            );

            $message = sprintf(
                'Kenaikan kelas selesai. Naik: %d, Lulus: %d, Tidak Naik: %d.',
                $results['promoted'],
                $results['graduated'],
                $results['retained'],
            );

            if (!empty($results['errors'])) {
                $message .= ' Errors: ' . implode('; ', $results['errors']);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
