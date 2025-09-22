<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = Template::withCount(['questions'])->get();

        return view('pages.user.templates.index', compact('templates'));
    }

    public function startTest(Request $request, $templateId)
    {
        $template = Template::with('questions.answers')->find($templateId);

        if (!$template) {
            return redirect()->route('user.templates')->with('error', 'Template topilmadi');
        }

        if ($template->questions->isEmpty()) {
            return redirect()->route('user.templates')->with('error', 'Bu templateda savollar mavjud emas');
        }

        session(['current_template_id' => $templateId]);

        return view('pages.user.questions.index', [
            'questions' => $template->questions,
            'template' => $template
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

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
