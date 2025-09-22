@extends('layouts.admin.main')
@section('title', __('Student Details'))
@section('content')
<x-admin.breadcrumb :title="__('Student Details')">
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-primary me-2">
        <i class="icofont icofont-arrow-left"></i>
        {{__('Back to Students')}}
    </a>
    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning">
        <i class="icofont icofont-edit"></i>
        {{__('Edit Student')}}
    </a>
</x-admin.breadcrumb>

<div class="container-fluid">
    <div class="row">
        <!-- Additional Info Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-dark">
                            <i class="icofont icofont-chart-histogram"></i>
                            {{ __('Activity Summary') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <div class="display-6 text-primary">
                                        <i class="icofont icofont-book"></i>
                                    </div>
                                    <div class="fw-bold">0</div>
                                    <small class="text-muted">{{ __('Courses Enrolled') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <div class="display-6 text-success">
                                        <i class="icofont icofont-check"></i>
                                    </div>
                                    <div class="fw-bold">0</div>
                                    <small class="text-muted">{{ __('Completed Tasks') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <div class="display-6 text-warning">
                                        <i class="icofont icofont-star"></i>
                                    </div>
                                    <div class="fw-bold">0%</div>
                                    <small class="text-muted">{{ __('Average Score') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <div class="display-6 text-info">
                                        <i class="icofont icofont-clock-time"></i>
                                    </div>
                                    <div class="fw-bold">0h</div>
                                    <small class="text-muted">{{ __('Study Time') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Student Information Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="icofont icofont-student"></i>
                        {{ __('Student Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-muted small">{{ __('Full Name') }}</label>
                                <div class="fw-bold fs-5 text-dark">
                                    <i class="icofont icofont-user text-primary me-2"></i>
                                    {{ $student->full_name }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small">{{ __('Login') }}</label>
                                <div class="fw-semibold">
                                    <i class="icofont icofont-key text-info me-2"></i>
                                    {{ $student->user->login }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small">{{ __('Phone') }}</label>
                                <div class="fw-semibold">
                                    @if($student->phone)
                                    <i class="icofont icofont-phone text-success me-2"></i>
                                    <a href="tel:{{ $student->phone }}" class="text-decoration-none">{{ $student->phone }}</a>
                                    @else
                                    <i class="icofont icofont-phone text-muted me-2"></i>
                                    <span class="text-muted">{{ __('Not provided') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-muted small">{{ __('Address') }}</label>
                                <div class="fw-semibold">
                                    @if($student->address)
                                    <i class="icofont icofont-location-pin text-warning me-2"></i>
                                    <span>{{ $student->address }}</span>
                                    @else
                                    <i class="icofont icofont-location-pin text-muted me-2"></i>
                                    <span class="text-muted">{{ __('Not provided') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small">{{ __('Status') }}</label>
                                <div>
                                    <span class="badge bg-success fs-6">
                                        <i class="icofont icofont-check-circle me-1"></i>
                                        {{ __('Active') }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small">{{ __('Student ID') }}</label>
                                <div class="fw-semibold">
                                    <i class="icofont icofont-id-card text-secondary me-2"></i>
                                    #{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration & Activity Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="icofont icofont-info-circle"></i>
                        {{ __('Registration Info') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Registered on') }}</small>
                        <div class="fw-semibold">
                            <i class="icofont icofont-calendar text-primary me-1"></i>
                            {{ $student->created_at->format('d/m/Y') }}
                        </div>
                        <div class="small text-muted">
                            <i class="icofont icofont-clock-time me-1"></i>
                            {{ $student->created_at->format('H:i') }}
                        </div>
                    </div>

                    @if($student->updated_at != $student->created_at)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('Last updated') }}</small>
                        <div class="fw-semibold">
                            <i class="icofont icofont-calendar text-warning me-1"></i>
                            {{ $student->updated_at->format('d/m/Y') }}
                        </div>
                        <div class="small text-muted">
                            <i class="icofont icofont-clock-time me-1"></i>
                            {{ $student->updated_at->format('H:i') }}
                        </div>
                    </div>
                    @endif

                    <hr class="my-3">

                    <div class="mb-3">
                        <small class="text-muted">{{ __('Account Age') }}</small>
                        <div class="fw-semibold text-success">
                            <i class="icofont icofont-ui-timer me-1"></i>
                            {{ $student->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="icofont icofont-lightning-alt"></i>
                        {{ __('Quick Actions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-warning btn-sm">
                            <i class="icofont icofont-edit"></i>
                            {{ __('Edit Student') }}
                        </a>

                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            <i class="icofont icofont-key"></i>
                            {{ __('Reset Password') }}
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="icofont icofont-delete"></i>
                            {{ __('Delete Student') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="icofont icofont-key"></i>
                    {{ __('Reset Password') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="icofont icofont-warning"></i>
                    {{ __('Are you sure you want to reset the password for') }} <strong>{{ $student->full_name }}</strong>?
                </div>
                <p class="text-muted">{{ __('A new temporary password will be generated and the student will need to change it on first login.') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="icofont icofont-close"></i>
                    {{ __('Cancel') }}
                </button>
                <form action="#" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="icofont icofont-key"></i>
                        {{ __('Reset Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="icofont icofont-warning"></i>
                    {{ __('Delete Student') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="icofont icofont-warning-alt"></i>
                    {{ __('This action cannot be undone!') }}
                </div>
                <p>{{ __('Are you sure you want to permanently delete') }} <strong>{{ $student->full_name }}</strong>?</p>
                <ul class="text-muted">
                    <li>{{ __('All student data will be permanently deleted') }}</li>
                    <li>{{ __('Course enrollments will be removed') }}</li>
                    <li>{{ __('Progress and scores will be lost') }}</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="icofont icofont-close"></i>
                    {{ __('Cancel') }}
                </button>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you absolutely sure?') }}')">
                        <i class="icofont icofont-delete"></i>
                        {{ __('Delete Forever') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection