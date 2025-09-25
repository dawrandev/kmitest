@extends('layouts.admin.main')
@section('title', __('Faculties'))
@section('content')
<x-admin.breadcrumb :title="__('All Faculties')">
    <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
        <i class="icofont icofont-plus"></i>
        {{ __('Add Faculty') }}
    </a>
</x-admin.breadcrumb>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <!-- Faculties Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="facultiesTable">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculties as $faculty)
                                <tr>
                                    <td>{{ $faculties->firstItem() + $loop->index }}</td>
                                    <td>
                                        <span class="f-w-500">
                                            {{ optional($faculty->translations->firstWhere('language_id', currentLanguageId()))->name }}
                                        </span>
                                    </td>
                                    <td class="text align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-outline-warning px-2 py-1">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.faculties.destroy', $faculty) }}" method="POST" class="d-inline delete-form">
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
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="icofont icofont-search"></i> {{ __('No faculties found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <br>

                    <!-- Pagination -->
                    @if($faculties->hasPages())
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dataTables_info">
                                {{ __('Showing') }} {{ $faculties->firstItem() }} {{ __('to') }} {{ $faculties->lastItem() }} {{ __('of') }} {{ $faculties->total() }} {{ __('entries') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $faculties->links() }}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

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