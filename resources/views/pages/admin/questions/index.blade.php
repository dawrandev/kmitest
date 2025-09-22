@extends('layouts.admin.main')

@section('title', __('Questions Management'))

@push('styles')
@vite(['resources/css/admin/questions/index.css'])
@endpush

@section('content')
<x-admin.breadcrumb :title="__('Questions Management')">
    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
        <i class="icofont icofont-plus"></i>
        {{__('Add New Question')}}
    </a>
</x-admin.breadcrumb>
<style>
    /* Question Cards Styling */
    .question-card {
        min-height: 220px;
        border: 1px solid #e3e6f0 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }

    .question-card:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    /* Language Selector */
    .language-selector .btn-check:checked+.btn-outline-primary {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
    }

    .language-selector .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }

    /* Search Input */
    #searchInput {
        border-right: 0;
    }

    #searchButton {
        border-left: 0;
        background-color: white !important;
        border-color: #ced4da;
    }

    /* Modal */
    .modal-dialog-scrollable .modal-body {
        max-height: 70vh;
    }

    /* Answer Items */
    .answer-item {
        transition: all 0.2s ease;
        border-radius: 0.375rem;
    }

    .answer-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .bg-success.bg-opacity-25 {
        background-color: rgba(25, 135, 84, 0.25) !important;
    }

    .border-success {
        border-color: #198754 !important;
    }

    .border-secondary {
        border-color: #6c757d !important;
    }

    .answer-item.correct {
        border-left: 4px solid var(--bs-success);
        background-color: rgba(var(--bs-success-rgb), 0.1);
    }

    .answer-item.incorrect {
        border-left: 4px solid var(--bs-secondary);
    }

    /* Card Text */
    .card-text {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-size: 0.95rem !important;
        line-height: 1.5 !important;
    }

    /* Buttons */
    .view-question-btn,
    .btn-outline-warning,
    .btn-outline-danger {
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem !important;
        min-height: 38px !important;
    }

    .question-card .card-body {
        padding: 1.25rem !important;
    }

    .badge {
        font-size: 0.85rem !important;
        padding: 0.5em 0.75em !important;
    }

    .d-flex.gap-2 .btn {
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem !important;
    }

    .btn-outline-info:hover,
    .btn-outline-warning:hover,
    .btn-outline-danger:hover {
        transform: translateY(-1px) !important;
    }

    /* Modal Question Image */
    .modal-question-image {
        max-height: 300px;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* SweetAlert2 Custom Styling */
    .swal2-popup {
        font-family: inherit;
    }

    .swal2-title {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .swal2-content {
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .language-selector .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .col-lg-3 {
            margin-bottom: 1rem;
        }

        .question-card {
            min-height: 200px;
        }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0">
                                <i class="icofont icofont-question-circle me-2"></i>
                                {{__('Questions List')}}
                                <span class="badge bg-light text-primary ms-2 fs-6">
                                    {{ $questions->total() }}
                                </span>
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text"
                                    id="searchInput"
                                    class="form-control bg-white"
                                    placeholder="{{__('Search questions...')}}"
                                    value="{{ request('search') }}">
                                <button class="btn btn-light" type="button" id="searchButton">
                                    <i class="icofont icofont-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Language Filter -->
                <div class="card-body pb-2">
                    <div class="row justify-content-center mb-4">
                        <div class="col-lg-8 col-md-10">
                            <div class="card">
                                <div class="card-body py-3">
                                    <div class="btn-group w-100 language-selector" role="group">
                                        @foreach($languages as $index => $language)
                                        <input type="radio"
                                            class="btn-check"
                                            name="language"
                                            id="lang_{{ $language->id }}"
                                            value="{{ $language->id }}"
                                            {{ ($index == 0 || request('language_id') == $language->id) ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="lang_{{ $language->id }}">
                                            {{ $language->name }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions Container -->
                    <div id="questionsContainer">
                        @if($questions->count() > 0)
                        <div class="row">
                            @foreach($questions as $index => $question)
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm question-card">
                                    @php
                                    $selectedLanguageId = request('language_id', $languages->first()->id);
                                    $translation = $question->translations->where('language_id', $selectedLanguageId)->first();
                                    $questionNumber = ($questions->currentPage() - 1) * $questions->perPage() + $index + 1;
                                    @endphp

                                    @if($translation && $translation->image)
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $translation->image) }}"
                                            class="card-img-top"
                                            style="height: 150px; object-fit: cover;"
                                            alt="Question Image">
                                        <div class="position-absolute top-0 start-0 p-2">
                                            <span class="badge bg-dark bg-opacity-75 fs-6">
                                                #{{ $questionNumber }}
                                            </span>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-3 flex-grow-1">
                                            @if(!($translation && $translation->image))
                                            <div class="mb-2">
                                                <span class="badge bg-primary fs-6">
                                                    #{{ $questionNumber }}
                                                </span>
                                            </div>
                                            @endif
                                            <p class="card-text text-muted mb-0">
                                                {{ $translation ? $translation->text : __('No translation available') }}
                                            </p>
                                        </div>

                                        <div class="mt-auto">
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                    class="btn btn-outline-info btn-sm flex-fill view-question-btn"
                                                    data-question-id="{{ $question->id }}"
                                                    data-language-id="{{ $selectedLanguageId }}">
                                                    <i class="icofont icofont-eye me-1"></i>
                                                    <!-- {{ __('View') }} -->
                                                </button>

                                                <a href="{{ route('admin.questions.edit', $question->id) }}"
                                                    class="btn btn-outline-warning btn-sm flex-fill">
                                                    <i class="icofont icofont-edit me-1"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm flex-fill delete-btn"
                                                    data-question-id="{{ $question->id }}">
                                                    <i class="icofont icofont-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $questions->appends(request()->query())->links() }}
                        </div>
                        @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="icofont icofont-question-circle text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted">{{__('No questions found')}}</h5>
                            <p class="text-muted">{{__('Start by creating your first question')}}</p>
                            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                                <i class="icofont icofont-plus me-1"></i>
                                {{__('Create First Question')}}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="icofont icofont-question-circle me-2"></i>
                    {{__('Question Details')}}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="questionModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{__('Loading...')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.translations = {
        attention: '{{ __("Attention!") }}',
        selectLanguage: '{{ __("Please select a language") }}',
        loading: '{{ __("Loading...") }}',
        errorLoading: '{{ __("Error loading questions") }}',
        errorLoadingDetails: '{{ __("Error loading question details") }}',
        question: '{{ __("Question") }}',
        noTranslation: '{{ __("No translation available") }}',
        answerOptions: '{{ __("Answer Options") }}',
        deleteQuestion: '{{ __("Delete Question") }}',
        deleteConfirmText: '{{ __("Are you sure you want to delete this question? This action cannot be undone!") }}',
        confirmDelete: '{{ __("Yes, delete it!") }}',
        cancel: '{{ __("Cancel") }}',
        deleting: '{{ __("Deleting...") }}',
        pleaseWait: '{{ __("Please wait") }}',
        deleted: '{{ __("Deleted!") }}',
        deleteSuccess: '{{ __("Question has been successfully deleted.") }}',
        error: '{{ __("Error!") }}',
        deleteError: '{{ __("An error occurred while deleting the question.") }}',
        serverError: '{{ __("An error occurred while connecting to the server.") }}'
    };
</script>
@vite(['resources/js/admin/questions/index.js'])
@endpush