@extends('layouts.admin.main')

@vite([
'resources/css/admin/questions/create.css',
'resources/js/admin/questions/create.js'
])

@section('title', __('Add Questions and Answers'))

@section('content')
<x-admin.breadcrumb :title="__('Add Questions and Answers')">
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
                        <i class="icofont icofont-plus me-2"></i>
                        {{__('Create New Question')}}
                    </h4>
                </div>
                <div class="card-body">
                    <form id="questionForm" action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="icofont icofont-image me-2"></i>{{__('Question Image')}}
                                </label>

                                <div class="image-upload-container">
                                    <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">

                                    <button type="button" class="btn btn-outline-primary btn-upload" onclick="document.getElementById('imageInput').click()">
                                        <i class="icofont icofont-upload me-2"></i>{{__('Choose Image')}}
                                    </button>

                                    <!-- Rasm preview -->
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
                                </div>

                                @error('image')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tillar bo'yicha savollar va javoblar -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary">
                                    <i class="ti ti-world me-2"></i>
                                    {{__('Questions and Answers')}}
                                </h5>
                                <button type="button" class="btn btn-success btn-sm" onclick="questionsCreate.addAnswerToAll()">
                                    <i class="icofont icofont-plus me-1"></i>{{__('Add Answer to All Languages')}}
                                </button>
                            </div>

                            <div class="row" id="languageQuestions">
                                <!-- Dynamic language questions will be loaded here -->
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

                        <!-- Saqlash tugmalari -->
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                <i class="icofont icofont-arrow-left"></i> {{__('Cancel')}}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="icofont icofont-save"></i> {{__('Save')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@php
$translations = [
'Add Answer' => __('Add Answer'),
'Answer' => __('Answer'),
'Enter question in :lang...' => __('Enter question in :lang...'),
'Answer variants (:lang)' => __('Answer variants (:lang)'),
'At least one answer is required!' => __('At least one answer is required!'),
];
@endphp

<script>
    window.translations = @json($translations);
</script>

<script>
    @if(function_exists('getLanguages'))
    window.appLanguages = @json(getLanguages());
    @else
    window.appLanguages = [];
    window.translations = @json($translations);
    @endif
</script>
@endsection