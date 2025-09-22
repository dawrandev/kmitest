<?php

namespace App\Services\Admin;

use App\Repositories\Admin\QuestionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\UploadedFile;

class QuestionService
{
    protected $questionRepository;

    const DEFAULT_IMAGE_PATH = 'questions/medicaltest.png';

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function storeMultipleQuestions(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // ✅ Rasm yuklashni yaxshilash
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                try {
                    $imagePath = $this->uploadImage($data['image']);
                } catch (Exception $e) {
                    Log::error('Image upload error: ' . $e->getMessage());
                    // Rasm yuklashda xatolik bo'lsa, default rasmni ishlatamiz
                    $imagePath = self::DEFAULT_IMAGE_PATH;
                }
            } else {
                // Rasm kelmasa default rasmni ishlatamiz
                $imagePath = self::DEFAULT_IMAGE_PATH;
            }

            // Bitta savol yaratamiz
            $question = $this->questionRepository->createQuestion();

            // Har bir tildagi savol tarjimasini yaratamiz
            foreach ($data['questions'] as $languageId => $questionText) {
                $this->questionRepository->createQuestionTranslation([
                    'question_id' => $question->id,
                    'language_id' => $languageId,
                    'text' => $questionText,
                    'image' => $imagePath
                ]);
            }

            // Javoblarni yaratamiz
            $this->createAnswersForQuestion($question->id, $data['answers'], $data);

            return [$question];
        });
    }

    private function createAnswersForQuestion(int $questionId, array $answersData, array $allData): void
    {
        Log::info("=== createAnswersForQuestion DEBUG ===");
        Log::info("Question ID: {$questionId}");
        Log::info("All data in createAnswersForQuestion: " . json_encode($allData));

        // Barcha answer_id larni yig'amiz
        $allAnswerIds = [];
        foreach ($answersData as $languageId => $languageAnswers) {
            foreach ($languageAnswers as $answerData) {
                if (!in_array($answerData['answer_id'], $allAnswerIds)) {
                    $allAnswerIds[] = $answerData['answer_id'];
                }
            }
        }

        Log::info("All answer IDs: " . json_encode($allAnswerIds));

        // Har bir unique answer_id uchun answer yaratamiz
        foreach ($allAnswerIds as $answerId) {
            // Bu answer_id to'g'ri javobmi tekshiramiz
            $isCorrect = $this->checkIfAnswerIsCorrect($answerId, $allData);

            // Answer yaratamiz
            $answer = $this->questionRepository->createAnswer([
                'question_id' => $questionId,
                'is_correct' => $isCorrect
            ]);

            // Har bir tildagi javob tarjimasini yaratamiz
            foreach ($answersData as $languageId => $languageAnswers) {
                foreach ($languageAnswers as $answerData) {
                    if ($answerData['answer_id'] == $answerId) {
                        $this->questionRepository->createAnswerTranslation([
                            'answer_id' => $answer->id,
                            'language_id' => $languageId,
                            'text' => $answerData['text']
                        ]);
                        break;
                    }
                }
            }
        }
    }

    private function checkIfAnswerIsCorrect(string $answerId, array $allData): bool
    {
        Log::info("Looking for correct answers in data: " . json_encode($allData));

        foreach ($allData as $key => $value) {
            if (str_starts_with($key, 'correct_answer_') && $value == $answerId) {
                Log::info("Found correct answer: {$key} = {$value} matches answer_id {$answerId}");
                return true;
            }
        }

        return false;
    }

    private function uploadImage(UploadedFile $image): string
    {
        try {
            // ✅ Fayl validatsiyasini qo'shimcha tekshirish
            if (!$image->isValid()) {
                throw new Exception('Yuklangan fayl noto\'g\'ri');
            }

            // ✅ MIME type tekshirish
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!in_array($image->getMimeType(), $allowedMimes)) {
                throw new Exception('Fayl formati qo\'llab-quvvatlanmaydi: ' . $image->getMimeType());
            }

            // ✅ Fayl hajmini tekshirish (5MB)
            if ($image->getSize() > 5242880) { // 5MB in bytes
                throw new Exception('Fayl hajmi juda katta: ' . round($image->getSize() / 1024 / 1024, 2) . 'MB');
            }

            // ✅ Storage papkasini tekshirish va yaratish
            $storagePath = storage_path('app/public/questions');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Unique filename yaratamiz
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // ✅ Public disk'ga questions papkasiga saqlaymiz
            $path = $image->storeAs('questions', $fileName, 'public');

            // ✅ Fayl saqlanganligi tekshirish
            if (!Storage::disk('public')->exists($path)) {
                throw new Exception('Fayl saqlanmadi: ' . $path);
            }

            Log::info('Image uploaded successfully: ' . $path);
            return $path;
        } catch (Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            throw new Exception('Rasm yuklashda xatolik: ' . $e->getMessage());
        }
    }

    public function deleteImage(?string $imagePath): void
    {
        // Default rasmni o'chirmaslik uchun tekshirish
        if ($imagePath && $imagePath !== self::DEFAULT_IMAGE_PATH && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    public function updateMultipleQuestions(int $questionId, array $data): bool
    {
        return DB::transaction(function () use ($questionId, $data) {

            // 1. Savolning mavjudligini tekshirish
            $question = $this->questionRepository->findQuestionById($questionId);
            if (!$question) {
                throw new Exception('Savol topilmadi');
            }

            // 2. Rasm bilan ishlash
            $this->handleImageUpdate($questionId, $data);

            // 3. Savol tarjimalarini yangilash
            $this->updateQuestionTranslations($questionId, $data);

            // 4. Javoblarni yangilash
            $this->updateAnswersForQuestion($questionId, $data);

            return true;
        });
    }

    /**
     * Handle image upload/update/delete
     */
    private function handleImageUpdate(int $questionId, array $data): string
    {
        // Yangi rasm yuklash
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            try {
                return $this->uploadImage($data['image']);
            } catch (Exception $e) {
                Log::error('Image upload error: ' . $e->getMessage());
                // Xatolik bo'lsa default rasmni qaytaramiz
                return self::DEFAULT_IMAGE_PATH;
            }
        }

        // Eski rasmni o'chirish
        if (isset($data['remove_current_image']) && $data['remove_current_image'] == '1') {
            $oldImagePath = $this->questionRepository->getQuestionImagePath($questionId);
            if ($oldImagePath && $oldImagePath !== self::DEFAULT_IMAGE_PATH) {
                $this->deleteImage($oldImagePath);
            }
            return self::DEFAULT_IMAGE_PATH;
        }

        // Eski rasmni saqlash
        $oldImagePath = $this->questionRepository->getQuestionImagePath($questionId);
        return $oldImagePath ?: self::DEFAULT_IMAGE_PATH;
    }

    /**
     * Update question translations
     */
    private function updateQuestionTranslations(int $questionId, array $data): void
    {
        if (!isset($data['questions']) || !is_array($data['questions'])) {
            return;
        }

        $imagePath = $this->handleImageUpdate($questionId, $data);

        foreach ($data['questions'] as $languageId => $questionText) {
            if (empty(trim($questionText))) {
                continue;
            }

            $this->questionRepository->createOrUpdateQuestionTranslation([
                'question_id' => $questionId,
                'language_id' => $languageId,
                'text' => trim($questionText),
                'image' => $imagePath
            ]);
        }
    }

    /**
     * Update answers for question
     */
    private function updateAnswersForQuestion(int $questionId, array $data): void
    {
        if (!isset($data['answers']) || !is_array($data['answers'])) {
            return;
        }

        Log::info("=== updateAnswersForQuestion DEBUG ===");
        Log::info("Question ID: {$questionId}");
        Log::info("Incoming answers data: " . json_encode($data['answers']));

        // Barcha kelgan answer_id larni yig'amiz
        $incomingAnswerIds = [];
        $processedAnswers = [];

        foreach ($data['answers'] as $languageId => $languageAnswers) {
            foreach ($languageAnswers as $answerData) {
                $answerId = $answerData['answer_id'] ?? null;
                $existingId = $answerData['existing_id'] ?? null;

                // Agar existing_id mavjud bo'lsa, uni ishlatamiz (update uchun)
                $actualAnswerId = $existingId ?: $answerId;

                if ($actualAnswerId && !in_array($actualAnswerId, $incomingAnswerIds)) {
                    $incomingAnswerIds[] = $actualAnswerId;
                }

                $processedAnswers[$actualAnswerId][$languageId] = [
                    'text' => $answerData['text'] ?? '',
                    'answer_id' => $answerId,
                    'existing_id' => $existingId
                ];
            }
        }

        Log::info("Incoming answer IDs: " . json_encode($incomingAnswerIds));
        Log::info("Processed answers: " . json_encode($processedAnswers));

        // Eski javoblarni o'chirish (faqat kelmaganlarini)
        $this->questionRepository->deleteAnswersNotInList($questionId, $incomingAnswerIds);

        // Har bir javobni yaratish yoki yangilash
        foreach ($processedAnswers as $actualAnswerId => $languageData) {
            $this->createOrUpdateAnswer($questionId, $actualAnswerId, $languageData, $data);
        }
    }

    /**
     * Create or update single answer with translations
     */
    private function createOrUpdateAnswer(int $questionId, $answerId, array $languageData, array $allData): void
    {
        // To'g'ri javob ekanligini tekshirish
        $isCorrect = $this->checkIfAnswerIsCorrect($answerId, $allData);

        Log::info("Processing answer ID: {$answerId}, is_correct: " . ($isCorrect ? 'true' : 'false'));

        // Mavjud javobni topishga harakat qilish
        $existingAnswer = $this->questionRepository->findAnswerById($answerId);

        if ($existingAnswer) {
            // Mavjud javobni yangilash
            $this->questionRepository->updateAnswer($answerId, [
                'is_correct' => $isCorrect
            ]);

            Log::info("Updated existing answer: {$answerId}");
        } else {
            // Yangi javob yaratish
            $existingAnswer = $this->questionRepository->createAnswer([
                'question_id' => $questionId,
                'is_correct' => $isCorrect
            ]);

            Log::info("Created new answer with ID: {$existingAnswer->id}");
        }

        // Javob tarjimalarini yaratish/yangilash
        foreach ($languageData as $languageId => $answerInfo) {
            if (empty(trim($answerInfo['text']))) {
                continue;
            }

            $this->questionRepository->createOrUpdateAnswerTranslation([
                'answer_id' => $existingAnswer->id,
                'language_id' => $languageId,
                'text' => trim($answerInfo['text'])
            ]);
        }
    }
}
