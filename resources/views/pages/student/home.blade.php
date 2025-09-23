@extends('layouts.student.main')
@vite(['resources/css/student/home.css'])
@section('title', __('Medical Test'))
@section('content')
<section class="section-space cuba-demo-section layout" id="layout">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 wow pulse">
                <div class="cuba-demo-content">
                    <div class="couting">
                        <div class="container-fluid">
                            <!-- Header qismi -->
                            <div class="row mb-5">
                                <div class="col-12 text-center">
                                    <h1 class="display-4 mb-3 text-primary">{{__('Medical Test')}}</h1>
                                    <p class="lead text-muted text-center">{{ __('Test your knowledge and find out your level') }}</p>
                                </div>
                            </div>
                            <!-- til tanlash -->
                            <div class="row justify-content-center mb-5">
                                <div class="col-lg-8 col-md-10">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-4">
                                            <h5 class="text-center mb-4">{{ __('Choose Language') }}</h5>
                                            <div class="btn-group w-100" role="group">
                                                @foreach (getLanguages() as $key => $language)
                                                <input type="radio" class="btn-check language-radio"
                                                    name="language" id="lang_{{ $language->id }}"
                                                    value="{{ $language->id }}"
                                                    data-language-name="{{ $language->name }}"
                                                    {{ $key == 0 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary px-4 py-3" for="lang_{{ $language->id }}">
                                                    <i class="icon-world"></i>
                                                    {{ $language->name }}
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Asosiy test kartasi -->
                            <div class="row justify-content-center">
                                <div class="col-lg-8 col-md-10">
                                    <div class="card shadow-lg border-0 cke_tpl_item_main">
                                        <div class="card-header bg-primary text-white p-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-center">
                                                    <h3 class="mb-1 fw-bold">{{ __('Medical Test') }}</h3>
                                                </div>
                                                <span class="badge bg-light text-primary fs-6 px-3 py-2" id="selected-language">
                                                    {{ getLanguages()->first()->name ?? __('Uzbek') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-body p-5">
                                            <!-- Test ma'lumotlari -->
                                            <div class="row mb-4">
                                                <div class="col-md-4 text-center">
                                                    <div class="cke_tpl_stat">
                                                        <i data-feather="help-circle" style="width: 32px; height: 32px;" class="text-primary mb-2"></i>
                                                        <div class="fw-bold fs-4">10</div>
                                                        <div class="text-muted">{{ __('Number of questions') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="cke_tpl_stat">
                                                        <i data-feather="clock" style="width: 32px; height: 32px;" class="text-success mb-2"></i>
                                                        <div class="fw-bold fs-4">15</div>
                                                        <div class="text-muted">{{ __('Minutes') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="cke_tpl_stat">
                                                        <i data-feather="x-circle" style="width: 32px; height: 32px;" class="text-danger mb-2"></i>
                                                        <div class="fw-bold fs-4">1</div>
                                                        <div class="text-muted">{{ __('Error Limit') }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Boshlash tugmasi -->
                                            <div class="text-center">
                                                <button class="btn btn-primary btn-lg px-5 py-3 cke_tpl_start_btn" onclick="startTest()">
                                                    <i class="icon-control-play" class="me-2" style="width: 20px; height: 20px;"></i>
                                                    {{ __('Start') }}
                                                </button>
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        {{ __("Once started, the test cannot be revisited") }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-5">
                                <div class="col-lg-8 col-md-10">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-4 text-center">
                                            <h6 class="text-primary mb-3">{{ __('Tips') }}</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <small class="text-muted">
                                                        <i data-feather="lightbulb" class="text-warning me-1" style="width: 14px; height: 14px;"></i>
                                                        {{ __("Read the questions carefully") }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <small class="text-muted">
                                                        <i data-feather="clock" class="text-info me-1" style="width: 14px; height: 14px;"></i>
                                                        {{ __("Manage your time properly") }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <small class="text-muted">
                                                        <i data-feather="heart" class="text-danger me-1" style="width: 14px; height: 14px;"></i>
                                                        {{ __("Work in a calm environment") }}
                                                    </small>
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
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const languageRadios = document.querySelectorAll('.language-radio');
        const selectedLanguageSpan = document.getElementById('selected-language');

        languageRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    selectedLanguageSpan.textContent = this.getAttribute('data-language-name');
                }
            });
        });
    });

    function startTest() {
        const selectedLanguage = document.querySelector('input[name="language"]:checked');

        if (!selectedLanguage) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("please_select_language") }}'
            });
            return;
        }

        const startButton = document.querySelector('.cke_tpl_start_btn');
        startButton.disabled = true;
        startButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("starting") }}';

        fetch('{{ route("student.test.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    language_id: selectedLanguage.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.message || '{{ __("error_occurred") }}'
                    });
                    startButton.disabled = false;
                    startButton.innerHTML = '<i class="icon-control-play me-2"></i>{{ __("Start Test") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("error_occurred") }}'
                });
                startButton.disabled = false;
                startButton.innerHTML = '<i class="icon-control-play me-2"></i>{{ __("Start Test") }}';
            });
    }
</script>
@endsection