<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Template;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $templateId = $request->session()->get('template_id');

        if (!$templateId) {
            return redirect()->route('user.templates')->with('error', 'Template tanlanmagan');
        }

        $template = Template::find($templateId);
        if (!$template) {
            return redirect()->route('user.templates')->with('error', 'Template topilmadi');
        }

        $questions = Question::whereHas('templates', function ($query) use ($templateId) {
            $query->where('template_id', $templateId);
        })
            ->with('answers')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('user.templates')->with('error', 'Bu templateda savollar mavjud emas');
        }

        return view('pages.user.questions.index', compact('questions', 'template'));
    }

    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
