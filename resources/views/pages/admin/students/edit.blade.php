@extends('layouts.admin.main')
@section('title', __('Edit Student'))
@section('content')
<x-admin.breadcrumb :title="__('Edit Student')">
    <a href="{{ route('admin.students.index') }}" class="btn btn-primary">
        <i class="icofont icofont-arrow-left"></i>
        {{__('Back to Students')}}
    </a>
</x-admin.breadcrumb>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-dark">{{ __('Student Information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.students.update', $student) }}" method="POST" id="studentForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="login" class="form-label">{{ __('Login') }} <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('login') is-invalid @enderror"
                                        id="login"
                                        name="login"
                                        value="{{ old('login', $student->user->login) }}"
                                        placeholder="{{ __('Enter login') }}">
                                    @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">{{ __('New Password') }} <small class="text-muted">({{ __('Leave empty to keep current password') }})</small></label>
                                    <input type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="{{ __('Enter new password') }}">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="{{ __('Confirm new password') }}">
                                    @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="full_name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('full_name') is-invalid @enderror"
                                        id="full_name"
                                        name="full_name"
                                        value="{{ old('full_name', $student->full_name) }}"
                                        placeholder="{{ __('Enter full name') }}">
                                    @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                    <input type="tel"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone', $student->phone) }}"
                                        placeholder="{{ __('Enter phone number') }}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                        id="address"
                                        name="address"
                                        rows="3"
                                        placeholder="{{ __('Enter address') }}">{{ old('address', $student->address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Student info card -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-light">
                                    <strong>{{ __('Registration Info') }}:</strong>
                                    {{ __('Registered on') }}: <span class="badge badge-dark">{{ $student->created_at->format('d/m/Y H:i') }}</span>
                                    @if($student->updated_at != $student->created_at)
                                    | {{ __('Last updated') }}: <span class="badge badge-dark">{{ $student->updated_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="{{ route('admin.students.index') }}" class="btn btn-light me-2">
                                    <i class="icofont icofont-close"></i>
                                    {{ __('Cancel') }}
                                </a>
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info me-2">
                                    <i class="icofont icofont-eye"></i>
                                    {{ __('View Student') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icofont icofont-save"></i>
                                    {{ __('Update Student') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection