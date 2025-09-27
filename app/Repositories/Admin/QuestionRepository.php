<?php

namespace App\Repositories\Admin;

use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\Answer;
use App\Models\AnswerTranslation;
use App\Models\Language;

class QuestionRepository
{
    const DEFAULT_IMAGE_PATH = 'questions/medicaltest.png';

    public function createQuestion(array $data = []): Question
    {
        return Question::create($data);
    }

    public function createQuestionTranslation(array $data): QuestionTranslation
    {
        return QuestionTranslation::create($data);
    }

    public function createAnswer(array $data): Answer
    {
        return Answer::create($data);
    }

    public function createAnswerTranslation(array $data): AnswerTranslation
    {
        return AnswerTranslation::create($data);
    }

    public function findQuestionById(int $id): ?Question
    {
        return Question::find($id);
    }

    public function findQuestionWithTranslations(int $id): ?Question
    {
        return Question::with(['translations', 'answers.translations'])->find($id);
    }

    public function getAllQuestions()
    {
        return Question::with(['translations', 'answers.translations'])->get();
    }

    public function updateQuestion(int $id, array $data): bool
    {
        return Question::where('id', $id)->update($data);
    }

    public function deleteQuestion(int $id): bool
    {
        return Question::destroy($id);
    }

    public function findAnswersByQuestionId(int $questionId)
    {
        return Answer::where('question_id', $questionId)
            ->with('translations.language')
            ->get();
    }

    public function getAllLanguages()
    {
        return Language::all();
    }

    public function findLanguageById(int $id): ?Language
    {
        return Language::find($id);
    }


    // QuestionRepository ga qo'shimcha metodlar

    public function getQuestionsWithTranslationsAndAnswers($languageId = null, $search = null)
    {
        $query = Question::with([
            'translations' => function ($translationQuery) use ($languageId) {
                $translationQuery->with('language');
                if ($languageId) {
                    $translationQuery->where('language_id', $languageId);
                }
            },
            'answers.translations.language'
        ]);

        // Search functionality
        if ($search && !empty(trim($search))) {
            $query->whereHas('translations', function ($translationQuery) use ($search) {
                $translationQuery->where('text', 'LIKE', '%' . trim($search) . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function findQuestionWithAllTranslationsAndAnswers($id)
    {
        return Question::with([
            'translations.language',
            'answers' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'answers.translations.language'
        ])->find($id);
    }

    public function findQuestionByIdWithLanguage($id, $languageId)
    {
        return Question::with([
            'translations' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->with('language');
            },
            'answers' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'answers.translations' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId)->with('language');
            }
        ])->find($id);
    }

    public function updateQuestionTranslation(int $questionId, int $languageId, array $data): bool
    {
        return QuestionTranslation::where('question_id', $questionId)
            ->where('language_id', $languageId)
            ->update($data);
    }

    /**
     * Create or update question translation
     */
    public function createOrUpdateQuestionTranslation(array $data): QuestionTranslation
    {
        return QuestionTranslation::updateOrCreate(
            [
                'question_id' => $data['question_id'],
                'language_id' => $data['language_id']
            ],
            $data
        );
    }

    /**
     * Update answer by ID
     */
    public function updateAnswer(int $answerId, array $data): bool
    {
        return Answer::where('id', $answerId)->update($data);
    }

    /**
     * Update answer translation
     */
    public function updateAnswerTranslation(int $answerId, int $languageId, array $data): bool
    {
        return AnswerTranslation::where('answer_id', $answerId)
            ->where('language_id', $languageId)
            ->update($data);
    }

    /**
     * Create or update answer translation
     */
    public function createOrUpdateAnswerTranslation(array $data): AnswerTranslation
    {
        return AnswerTranslation::updateOrCreate(
            [
                'answer_id' => $data['answer_id'],
                'language_id' => $data['language_id']
            ],
            $data
        );
    }

    public function deleteAnswersNotInList(int $questionId, array $keepAnswerIds = []): int
    {
        $query = Answer::where('question_id', $questionId);

        if (!empty($keepAnswerIds)) {
            $query->whereNotIn('id', $keepAnswerIds);
        }

        return $query->delete();
    }


    public function findAnswerById(int $answerId): ?Answer
    {
        return Answer::find($answerId);
    }

    public function getQuestionImagePath(int $questionId): string
    {
        $translation = QuestionTranslation::where('question_id', $questionId)->first();

        if ($translation && $translation->image && $translation->image !== self::DEFAULT_IMAGE_PATH) {
            return $translation->image;
        }

        if ($translation) {
            $translation->update(['image' => self::DEFAULT_IMAGE_PATH]);
        }

        return self::DEFAULT_IMAGE_PATH;
    }
}
