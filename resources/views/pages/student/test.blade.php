@extends('layouts.student.main')
@vite(['resources/css/student/test.css'])
@section('content')
<section class="section-space cuba-demo-section layout pt-5" id="layout">
    <div class="container mt-5">
        <div class="row">
            <div class="col-sm-12 wow pulse">
                <div class="cuba-demo-content">
                    <div class="couting">
                        <!-- Test Header with Timer -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('student.home') }}" class="btn btn-light me-3">
                                                        <i data-feather="arrow-left"></i>
                                                    </a>
                                                    <div>
                                                        <h5 class="mb-0">{{ __('Autotest') }}</h5>
                                                        <small class="text-muted">{{ $language->name }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <small class="text-muted d-block">{{ __('Question') }}</small>
                                                    <span class="h6 mb-0">
                                                        <span id="currentQuestion">1</span> / {{ count($questions) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="timer-container">
                                                    <div class="timer-circle">
                                                        <svg class="timer-progress" viewBox="0 0 80 80">
                                                            <circle cx="40" cy="40" r="35" stroke="#e2e8f0" stroke-width="6" fill="none" />
                                                            <circle cx="40" cy="40" r="35" stroke="#3b82f6" stroke-width="6" fill="none"
                                                                id="progressCircle" stroke-dasharray="220" stroke-dashoffset="0" />
                                                        </svg>
                                                        <div class="timer-text">
                                                            <span id="timerDisplay">25:00</span>
                                                            <small>{{ __('Remaining') }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Container -->
                        <div class="container-fluid">
                            @foreach($questions as $index => $question)
                            @php
                            $questionTranslation = $question->translations->where('language_id', $language->id)->first();
                            $questionAnswers = $question->answers->load(['translations' => function($query) use ($language) {
                            $query->where('language_id', $language->id);
                            }]);
                            @endphp

                            <div class="question-container {{ $index == 0 ? 'active' : '' }}"
                                id="question-{{ $question->id }}"
                                data-question-number="{{ $index + 1 }}">

                                <div class="row">
                                    <!-- Question Card - 8 columns -->
                                    <div class="col-lg-8">
                                        <div class="card question-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="badge badge-primary">{{ __('Question') }} {{ $index + 1 }}</span>
                                                    <small class="text-muted">{{ count($questions) }} {{ __('questions') }}</small>
                                                </div>

                                                <h6 class="mb-3 question-text">{{ $questionTranslation->text ?? __('Question text not found') }}</h6>

                                                @if($questionTranslation && $questionTranslation->image)
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset('storage/' . $questionTranslation->image) }}"
                                                        class="img-fluid w-100 rounded border question-image"
                                                        alt="{{ __('Question image') }}">
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Answers Card - 4 columns, always on the right -->
                                    <div class="col-lg-4">
                                        <div class="card answers-card">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 small">{{ __('Please select an answer') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                // 1. Avval tarjimasi bor javoblarni filter qilamiz
                                                $validAnswers = $questionAnswers->filter(function($answer) use ($language) {
                                                return $answer->translations->where('language_id', $language->id)->isNotEmpty();
                                                });

                                                // 2. Keyin unique qilamiz va indekslarni qayta tartiblaymiz
                                                $filteredAnswers = $validAnswers->unique('id')->values();

                                                // 3. Letters array'ini kengaytiramiz (xavfsizlik uchun)
                                                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
                                                @endphp

                                                @foreach($filteredAnswers as $answerIndex => $answer)
                                                @php
                                                $answerTranslation = $answer->translations->where('language_id', $language->id)->first();
                                                @endphp

                                                {{-- Bu yerda translation guaranteed mavjud --}}
                                                <div class="variant-card mb-2"
                                                    data-answer-id="{{ $answer->id }}"
                                                    data-question-id="{{ $question->id }}"
                                                    data-is-correct="{{ $answer->is_correct ? 'true' : 'false' }}">
                                                    <div class="card-body p-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                type="radio"
                                                                name="question{{ $question->id }}"
                                                                id="q{{ $question->id }}{{ $letters[$answerIndex] }}"
                                                                value="{{ $answer->id }}">
                                                            <label class="form-check-label w-100"
                                                                for="q{{ $question->id }}{{ $letters[$answerIndex] }}">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="badge me-2">{{ $letters[$answerIndex] }}</span>
                                                                    <span class="answer-text">{{ $answerTranslation->text }}</span>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach

                                                <button type="button"
                                                    class="btn btn-primary submit-btn w-100 mt-3"
                                                    id="submitBtn{{ $question->id }}"
                                                    data-question-id="{{ $question->id }}"
                                                    disabled>
                                                    <i class="icofont icofont-check me-1"></i>
                                                    <small>{{ __('Confirm Answer') }}</small>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Navigation and Progress -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1">{{ __('Progress') }}</h6>
                                                <small class="text-muted">
                                                    {{ __('Completed') }}: <span id="completedCount">0</span>/{{ count($questions) }}
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-danger">
                                                <i class="icofont icofont-racing-flag-alt"></i>
                                                {{ __('Finish Test') }}
                                            </button>

                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="progress mb-3" style="height: 6px;">
                                            <div class="progress-bar bg-success"
                                                role="progressbar"
                                                id="progressBar"
                                                style="width: 0%"
                                                aria-valuenow="0"
                                                aria-valuemin="0"
                                                aria-valuemax="{{ count($questions) }}">
                                            </div>
                                        </div>

                                        <!-- Navigation Buttons -->
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($questions as $index => $question)
                                            <button type="button"
                                                class="btn btn-sm nav-btn {{ $index == 0 ? 'current' : '' }}"
                                                data-question-id="{{ $question->id }}"
                                                data-question-number="{{ $index + 1 }}"
                                                id="navBtn{{ $question->id }}"
                                                title="{{ __('Question') }} {{ $index + 1 }}">
                                                {{ $index + 1 }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@php
$questionsData = $questions->map(function($question, $index) use ($language) {
$translation = $question->translations->where('language_id', $language->id)->first();
return [
'id' => $question->id,
'index' => $index,
'text' => $translation ? $translation->text : 'Question text not found',
'image' => $translation ? $translation->image : null
];
})->values();

$testData = [
'sessionId' => $testSession->id,
'languageId' => $language->id,
'totalQuestions' => count($questions),
'timeLimit' => 25 * 60,
'startTime' => strtotime($testSession->started_at) * 1000, // JS uchun ms format
'csrfToken' => csrf_token(),
'routes' => [
'submitAnswer' => route("student.test.submitAnswer"),
'finish' => route("student.test.finish", $testSession->id),
],
];
$translations = [
'timeUpTitle' => __('Time is up!'),
'timeUpText' => __('Test is being completed automatically...'),
'warningTitle' => __('Warning!'),
'selectAnswer' => __('Please select an answer!'),
'errorTitle' => __('Error!'),
'errorOccurred' => __('An error occurred'),
'serverError' => __('Could not connect to server'),
'noFinishRoute' => __('Error: Test completion route not found. Please reload the page.'),
'correctAnswer' => __('Correct answer!'),
'wrongAnswer' => __('Wrong answer'),
'finishTitle' => __('Finish Test'),
'finishText' => __('Do you want to finish the test?'),
'yesFinish' => __('Yes, finish'),
'cancel' => __('Cancel'),
'testFinished' => __('Test finished!'),
'answeredQuestions' => __('Answered questions'),
'correctAnswers' => __('Correct answers'),
'wrongAnswers' => __('Wrong answers'),
'timeUsed' => __('Time used'),
];

@endphp
<script>
    window.questions = @json($questionsData);
    window.testData = @json($testData);
    window.translations = @json($translations);
</script>
@vite(['resources/js/student/test.js'])
@endsection