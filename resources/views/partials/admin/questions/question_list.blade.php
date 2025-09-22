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
                                <!-- {{ __('Edit') }} -->
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
        <p class="text-muted">
            @if(request('search'))
            {{__('No questions found for your search query')}}
            @else
            {{__('Start by creating your first question')}}
            @endif
        </p>
        @if(!request('search'))
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
            <i class="icofont icofont-plus me-1"></i>
            {{__('Create First Question')}}
        </a>
        @endif
    </div>
    @endif
</div>