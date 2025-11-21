<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\Student;
use function PHPSTORM_META\map;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.payments')->only('index', 'show');
        $this->middleware('permission:create.payments')->only('create', 'store');
        $this->middleware('permission:edit.payments')->only('edit', 'update');
        $this->middleware('permission:delete.payments')->only('destroy');
        $this->middleware('has_permission:manage.fees')->only('edit', 'update');
        $this->middleware('has_permission:manage.scholarships')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $payments = Payment::with(['student', 'academicYear', 'semester'])->paginate(10);
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Plan to use AJAX(Livewire in future)
        $students = Student::join('users', 'students.user_id', '=', 'users.id')
            ->select(
                'students.id',
                'users.name',
                'students.student_id as id_card'
            )
            ->orderBy('users.name')
            ->get()
            ->map(function ($s) {
                $s->display_label = "{$s->name} ({$s->id_card})";
                return $s;
            });
        $semesters     = Semester::select('id', 'name')->orderBy('name')->get();
        $academicYears = AcademicYear::select('id', 'name')
            ->orderByDesc('name')
            ->get();

        return view('admin.payments.create', compact(
            'students',
            'semesters',
            'academicYears',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id'      => 'required|exists:semesters,id',
            'amount'           => 'required|numeric|min:0',
            'payment_date'     => 'required|date',
            'payment_method'   => 'required|string|max:255',
            'status'           => 'required|string|max:255', // e.g., Paid, Pending, Refunded
            'transaction_id'   => 'nullable|string|max:255|unique:payments,transaction_id',
        ]);

        Payment::create($validated);

        return redirect()->route('admin.payments.index')->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['student', 'academicYear', 'semester']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment): View
    {
        $students = Student::join('users', 'students.user_id', '=', 'users.id')
            ->select(
                'students.id',
                'users.name',
                'students.student_id as id_card'
            )
            ->orderBy('users.name')
            ->get()
            ->map(function ($s) {
                $s->display_label = "{$s->name} ({$s->id_card})";
                return $s;
            });
        $semesters     = Semester::select('id', 'name')->orderBy('name')->get();
        $academicYears = AcademicYear::select('id', 'name')
            ->orderByDesc('name')
            ->get();

        return view('admin.payments.create', compact(
            'students',
            'semesters',
            'academicYears',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id'      => 'required|exists:semesters,id',
            'amount'           => 'required|numeric|min:0',
            'payment_date'     => 'required|date',
            'payment_method'   => 'required|string|max:255',
            'status'           => 'required|string|max:255',
            'transaction_id'   => 'nullable|string|max:255|unique:payments,transaction_id,' . $payment->id,
        ]);

        $payment->update($validated);

        return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully.');
    }
}
