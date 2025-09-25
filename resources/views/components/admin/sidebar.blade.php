<div class="sidebar-wrapper">
    <div>
        <div class="logo-wrapper"><a href="{{ asset('index.html') }}"><img class="img-fluid for-light" src="{{ asset('../assets/images/logo/logo.png') }}" alt=""><img class="img-fluid for-dark" src="{{ asset('../assets/images/logo/logo_dark.png') }}" alt=""></a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper"><a href="{{ asset('index.html') }}"><img class="img-fluid" src="{{ asset('../assets/images/logo/logo-icon.png') }}" alt=""></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="{{ asset('index.html') }}"><img class="img-fluid" src="{{ asset('../assets/images/logo/logo-icon.png') }}" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav" href="#"><i data-feather="monitor"> </i><span>{{__('Dashboard') }}</span></a></li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#"><i data-feather="users"></i><span>{{__('Students')}}</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('admin.students.index') }}">{{__('All Students')}}</a></li>
                            <li><a href="{{ route('admin.students.create') }}">{{__('Create Student')}}</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#"><i data-feather="file-text"></i><span>{{__('Questions')}}</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('admin.questions.index') }}">{{__('All Questions')}}</a></li>
                            <li><a href="{{ route('admin.questions.create') }}">{{__('Question and Answers')}}</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#"><i data-feather="grid"></i><span>{{__('Faculties')}}</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('admin.faculties.index') }}">{{__('All Faculties')}}</a></li>
                            <li><a href="{{ route('admin.faculties.create') }}">{{__('Create Faculty')}}</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="#"><i data-feather="layers"></i><span>{{__('Groups')}}</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('admin.groups.index') }}">{{__('All Groups')}}</a></li>
                            <li><a href="{{ route('admin.groups.create') }}">{{__('Create Group')}}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>