<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionStoreRequest;
use App\Models\Language;
use App\Repositories\Admin\QuestionRepository;
use App\Services\Admin\QuestionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function __construct(
        protected QuestionService $questionService,
        protected QuestionRepository $questionRepository
    ) {}

    public function index(Request $request)
    {
        try {
            $languageId = $request->get('language_id');
            $search = $request->get('search');

            $questions = $this->questionRepository->getQuestionsWithTranslationsAndAnswers($languageId, $search);

            $languages = $this->questionRepository->getAllLanguages();

            if ($request->ajax()) {
                return view('partials.admin.questions.question_list', compact('questions', 'languages'))->render();
            }

            return view('pages.admin.questions.index', compact('questions', 'languages'));
        } catch (Exception $e) {
            Log::error('Questions index error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $languages = Language::all();

        return view('pages.admin.questions.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionStoreRequest $request)
    {
        try {
            // Directly using request->all()
            $this->questionService->storeMultipleQuestions($request->all());

            return redirect()->route('admin.questions.index')->with('success', __('Questions saved successfully'));
        } catch (Exception $e) {
            Log::error("Question store error: " . $e->getMessage());
            return back()->with('error', 'An error occurred! ' . $e->getMessage());
        }
    }

    public function show(string $id, Request $request)
    {
        try {
            $languageId = $request->get('language_id');

            $question = $this->questionRepository->findQuestionWithAllTranslationsAndAnswers($id);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => __('Question not found')
                ], 404);
            }

            // Return only data for the selected language
            $filteredQuestion = [
                'id' => $question->id,
                'translation' => $question->translations->where('language_id', $languageId)->first(),
                'answers' => $question->answers->map(function ($answer) use ($languageId) {
                    return [
                        'id' => $answer->id,
                        'is_correct' => $answer->is_correct,
                        'translation' => $answer->translations->where('language_id', $languageId)->first()
                    ];
                })->filter(function ($answer) {
                    return $answer['translation'] !== null;
                })->values()
            ];

            return response()->json([
                'success' => true,
                'data' => $filteredQuestion
            ]);
        } catch (Exception $e) {
            Log::error('Question show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('An error occurred: ') . $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            $question = $this->questionRepository->findQuestionWithAllTranslationsAndAnswers($id);
            if (!$question) {
                return back()->with('error', 'Question not found');
            }

            $languages = $this->questionRepository->getAllLanguages();

            return view('pages.admin.questions.edit', compact('question', 'languages'));
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            // Request validation (create QuestionUpdateRequest if needed)
            $request->validate([
                'questions' => 'required|array',
                'questions.*' => 'required|string|min:3',
                'answers' => 'required|array',
                'answers.*' => 'required|array',
                'image' => 'nullable|image|max:5120', // 5MB max
                'remove_current_image' => 'nullable|in:0,1'
            ]);

            // Check if each language has a correct answer
            $this->validateCorrectAnswers($request->all());

            // Update the question
            $this->questionService->updateMultipleQuestions((int)$id, $request->all());

            return redirect()
                ->route('admin.questions.index')
                ->with('success', __('Question updated successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error("Question update error: " . $e->getMessage());

            return back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Validate that each language has a correct answer selected
     */
    private function validateCorrectAnswers(array $data): void
    {
        if (!isset($data['answers']) || !is_array($data['answers'])) {
            throw new Exception('Answers not provided');
        }

        foreach ($data['answers'] as $languageId => $answers) {
            $hasCorrectAnswer = false;

            foreach ($data as $key => $value) {
                if (
                    str_starts_with($key, 'correct_answer_') &&
                    str_ends_with($key, '_' . $languageId)
                ) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                throw new Exception("No correct answer selected for language ID {$languageId}");
            }
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->questionRepository->deleteQuestion($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found or deletion error'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
