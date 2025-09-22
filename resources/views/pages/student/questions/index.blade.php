@extends('layouts.user.main')
@vite(['resources/css/user/questions/index.css'])
@section('content')

<section class="section-space cuba-demo-section layout pt-5" id="layout">
    <div class="container mt-5">
        <div class="row">
            <div class="col-sm-12 wow pulse">
                <div class="couting">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('user.templates') }}" class="btn btn-light me-3">
                                                    <i data-feather="arrow-left"></i>
                                                </a>
                                                <span class="text-muted me-2">Shablonlarga qaytish</span>
                                                <h4 class="mb-0">Test {{ $template->number }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="timer-container">
                                                    <div class="timer-circle">
                                                        <div class="timer-text">
                                                            <span id="timerDisplay">20:00</span>
                                                            <small>qoldi</small>
                                                        </div>
                                                        <svg class="timer-progress" width="80" height="80">
                                                            <circle cx="40" cy="40" r="35" fill="none" stroke="#e2e8f0" stroke-width="4" />
                                                            <circle id="progressCircle" cx="40" cy="40" r="35" fill="none" stroke="#3b82f6" stroke-width="4"
                                                                stroke-dasharray="220" stroke-dashoffset="0" transform="rotate(-90 40 40)" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-end">
                                                <span class="badge bg-light text-dark me-2">
                                                    <i data-feather="clock" class="me-1"></i>
                                                    Savol <span id="currentQuestion">1</span> dan {{ count($questions) }}
                                                </span>
                                                <span class="text-muted">Tugallangan: <span id="completedCount">0</span>/{{ count($questions) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid px-0">
                        @foreach($questions as $index => $question)
                        <div class="question-container {{ $index == 0 ? 'active' : '' }}" id="question-{{ $question->id }}">
                            <div class="main-content-row">
                                <!-- Savol qismi -->
                                <div class="question-section">
                                    <div class="card question-content">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge badge-primary">Savol {{ $index + 1 }}</span>
                                                <span class="text-muted">{{ count($questions) }} ta savol</span>
                                            </div>
                                            <h5 class="mb-4">{{ $question->text }}</h5>
                                            @if($question->image)
                                            <div class="text-center mb-4">
                                                <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid w-100 rounded border" alt="Savol rasmi">
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="answers-section">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Iltimos, javobni tanlang</h6>
                                        </div>
                                        <div class="card-body">
                                            @php
                                            $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                                            @endphp

                                            @foreach($question->answers as $answerIndex => $answer)
                                            <div class="variant-card mb-2" data-answer-id="{{ $answer->id }}" data-question-id="{{ $question->id }}" data-is-correct="{{ $answer->is_correct ? 'true' : 'false' }}">
                                                <div class="card-body p-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="question{{ $question->id }}" id="q{{ $question->id }}_{{ $letters[$answerIndex] }}" value="{{ $answer->id }}">
                                                        <label class="form-check-label w-100" for="q{{ $question->id }}_{{ $letters[$answerIndex] }}">
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge me-2">{{ $letters[$answerIndex] }}</span>
                                                                <span>{{ $answer->text }}</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            <button type="button" class="btn btn-primary submit-btn w-100 mt-3" id="submitBtn{{ $question->id }}" data-question-id="{{ $question->id }}" disabled>
                                                <i class="icofont icofont-check me-2"></i>
                                                Javobni Tasdiqlash
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($questions as $index => $question)
                                        <button type="button" class="btn btn-sm nav-btn {{ $index == 0 ? 'current' : '' }} px-3 py-2"
                                            data-question-id="{{ $question->id }}" data-question-number="{{ $index + 1 }}" id="navBtn{{ $question->id }}">
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
</section>

<script>
    window.questions = @json($questions);
</script>

@vite(['resources/js/user/questions/index.js'])
@endsection