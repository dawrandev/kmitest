<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.student.head')
    @stack('styles')
</head>

<body class="landing-page">
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper landing-page">
        @include('components.student.header')

        @yield('content')
    </div>

    {{-- SweetAlert messages --}}
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Muvaffaqiyatli',
            text: '{{ session('
            success ') }}',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Xatolik',
            text: '{{ session('
            error ') }}',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    @include('components.student.footer')
    </div>
    @include('partials.student.script')
    @stack('scripts')
</body>

</html>