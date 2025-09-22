<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentStoreRequest;
use App\Http\Requests\Admin\StudentUpdateRequest;
use App\Models\Student;
use App\Models\User;
use App\Services\Admin\StudentsService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(protected StudentsService $studentService) {}

    public function index(Request $request)
    {
        $students = $this->studentService->getStudents($request->query());

        if ($request->ajax()) {
            return view('partials.admin.students.students_table', compact('students'))->render();
        }

        return view('pages.admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('pages.admin.students.create');
    }

    public function store(StudentStoreRequest $request)
    {
        $this->studentService->createStudent($request->validated());

        return redirect()->route('admin.students.index')->with('success', __('Student created successfully'));
    }

    public function show(int $id)
    {
        $student = Student::with('user')->findOrFail($id);

        return view('pages.admin.students.show', compact('student'));
    }

    public function edit(int $id)
    {
        $student = Student::with('user')->findOrFail($id);

        return view('pages.admin.students.edit', compact('student'));
    }

    public function update(StudentUpdateRequest $request, int $id)
    {
        $student = $this->studentService->updateStudent($request->validated(), $id);

        return redirect()->route('admin.students.index')->with('success', __('Student updated successfully'));
    }

    public function destroy(int $id)
    {
        $student = User::where('id', $id)->delete();

        return redirect()->route('admin.students.index')->with('success', __('Student deleted successfully'));
    }
}
