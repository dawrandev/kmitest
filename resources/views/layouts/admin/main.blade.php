<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.admin.head')

    @stack('css')
</head>

<body>
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('components.admin.header')
        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            @include('components.admin.sidebar')
            <div class="page-body">
                <!-- Container-fluid starts-->
                @yield('content')
                <!-- Container-fluid Ends-->
            </div>
            @include('components.admin.footer')
        </div>
    </div>
    @include('partials.admin.script')
    @vite(['resources/js/alert.js'])
    @stack('scripts')
</body>

</html>