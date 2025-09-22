@extends('layouts.admin.main')
@vite([
'resources/css/admin/questions/create.css',
'resources/js/admin/questions/edit.js'
])

@section('title', __('Edit Question and Answers'))

@section('content')
<x-admin.breadcrumb :title="__('Edit Question and Answers')">
    <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
        <i class="icofont icofont-arrow-left"></i>
        {{__('Back to Questions')}}
    </a>
</x-admin.breadcrumb>

<div class="container-fluid questions-create">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="icofont icofont-edit me-2"></i>
                        {{__('Edit Question')}}
                    </h4>
                </div>
                <div class="card-body">
                    <form id="questionForm" action="{{ route('admin.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="icofont icofont-image me-2"></i>{{__('Question Image')}}
                                </label>

                                @php
                                $currentImage = $question->translations->first()->image ?? null;
                                @endphp

                                <div class="image-upload-container @if($currentImage) has-image @endif">
                                    <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">

                                    <button type="button" class="btn btn-outline-primary btn-upload" onclick="document.getElementById('imageInput').click()">
                                        <i class="icofont icofont-upload me-2"></i>
                                        @if($currentImage) {{__('Change Image')}} @else Rasm tanlash @endif
                                    </button>

                                    <!-- Mavjud rasm ko'rsatish -->
                                    @if($currentImage)
                                    <div id="currentImage" class="image-preview-container mt-3">
                                        <div class="preview-card">
                                            <img id="currentImg" src="{{ asset('storage/' . $currentImage) }}" alt="Current Image" class="preview-image">
                                            <div class="preview-info">
                                                <span class="file-name">Joriy rasm</span>
                                                <span class="file-size text-muted">Mavjud</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeCurrentImage()">
                                                <i class="icofont icofont-close"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endif

                                    <div id="imagePreview" class="image-preview-container mt-3" style="display: none;">
                                        <div class="preview-card">
                                            <img id="previewImg" src="" alt="Preview" class="preview-image">
                                            <div class="preview-info">
                                                <span id="fileName" class="file-name"></span>
                                                <span id="fileSize" class="file-size text-muted"></span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeImagePreview()">
                                                <i class="icofont icofont-close"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" id="removeCurrentImage" name="remove_current_image" value="0">
                                </div>

                                @error('image')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary">
                                    <i class="ti ti-world me-2"></i>
                                    {{__('Questions and Answers')}}
                                </h5>
                                <button type="button" class="btn btn-success btn-sm" onclick="questionsEdit.addAnswerToAll()">
                                    <i class="icofont icofont-plus me-1"></i>{{__('Add Answer to All Languages')}}
                                </button>
                            </div>

                            <div class="row" id="languageQuestions">
                            </div>

                            @error('questions')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            @error('answers')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            @if($errors->any())
                            @foreach($errors->all() as $error)
                            @if(str_contains($error, 'correct_answer'))
                            <div class="text-danger small mt-1">{{ $error }}</div>
                            @endif
                            @endforeach
                            @endif
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                <i class="icofont icofont-arrow-left"></i> {{__('Cancel')}}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="icofont icofont-save"></i> {{__('Update')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@php
$questions = $question->translations->pluck('text', 'language_id')->toArray();

$answers = $question->answers
->groupBy(fn($answer) => $answer->translations->first()->language_id ?? null)
->map(fn($groupedAnswers) => $groupedAnswers->map(fn($answer) => [
'id' => $answer->id,
'text' => $answer->translations->first()->text ?? '',
'is_correct' => $answer->is_correct,
]))
->filter()
->toArray();

$correctAnswers = $question->answers
->filter(fn($answer) => $answer->is_correct)
->mapWithKeys(fn($answer) => [
$answer->translations->first()->language_id ?? null => $answer->id
])
->toArray();
@endphp

<script>
    window.appLanguages = @json(function_exists('getLanguages') ? getLanguages() : []);
    window.questionData = {
        questions: @json($questions),
        answers: @json($answers),
        correctAnswers: @json($correctAnswers)
    };
</script>
@endsection