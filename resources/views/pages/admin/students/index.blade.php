@extends('layouts.admin.main')
@section('title', __('Students'))
@section('content')
<x-admin.breadcrumb :title="__('All Students')">
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <i class="icofont icofont-plus"></i>
        {{__('Add Student')}}
    </a>
</x-admin.breadcrumb>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <!-- Per Page Dropdown -->
                        <div class="d-flex align-items-center mb-2 mb-sm-0">
                            <label for="perPage" class="form-label me-2 mb-0">{{ __('Show') }}</label>
                            <select id="perPage" class="form-select form-select-sm w-auto">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="mb-2 mb-sm-0">
                            <input type="text" id="searchInput" class="form-control form-control-sm"
                                placeholder="{{ __('Search...') }}"
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Registered') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr class="student-row">
                                    <td>{{ $students->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-name">
                                                <span class="f-w-500">{{ $student->full_name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($student->phone)
                                        <span class="badge badge-light-primary">{{ $student->phone }}</span>
                                        @else
                                        <span class="text-muted f-12">{{ __('No phone') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="f-light">{{ $student->address ?? __('Not specified') }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <span>{{ $student->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="text align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-info px-2 py-1">
                                                <i class="icon-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-warning px-2 py-1">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger delete-btn px-2 py-1">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="icofont icofont-search"></i> {{ __('No students found') }}
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    <br>
                    <!-- Pagination -->
                    @if($students->hasPages())
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dataTables_info">
                                {{ __('Showing') }} {{ $students->firstItem() }} {{ __('to') }} {{ $students->lastItem() }} {{ __('of') }} {{ $students->total() }} {{ __('entries') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@vite(['resources/js/admin/students/index.js'])
@push('scripts')
<script>
    document.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            let form = this.closest("form");

            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This action cannot be undone!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('Cancel') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

@endsection