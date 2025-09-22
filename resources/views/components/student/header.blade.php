<div class="sticky-header">
    <header>
        <nav class="navbar navbar-b navbar-trans navbar-expand-xl fixed-top nav-padding" id="sidebar-menu">
            <a class="navbar-brand p-0" href="#">
                <img class="img-fluid w-75" src="{{ asset('assets/images/landing/landing_logo.png') }}" alt="">
            </a>
            <button class="navbar-toggler navabr_btn-set custom_nav" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarDefault" aria-controls="navbarDefault" aria-expanded="false"
                aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
            <div class="navbar-collapse justify-content-end collapse hidenav" id="navbarDefault">
                <ul class="navbar-nav navbar_nav_modify" id="scroll-spy">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.home') }}">{{ __('Home')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.results') }}">{{ __('Results')}}</a>
                    </li>
                    <li class="nav-item d-flex justify-content-center">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline m-0 p-0">
                            @csrf
                            <button type="submit"
                                class="nav-link btn btn-link d-flex align-items-center text-danger p-0">
                                <i class="icofont icofont-logout me-2"></i> {{ __('Logout') }}
                            </button>
                        </form>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="flag-icon flag-icon-{{ app()->getLocale() }}"></i>
                            {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <li>
                                @foreach(config('app.locales') as $code => $name)
                                <a class="dropdown-item" href="{{ route('set.locale', $code) }}">
                                    <i class="flag-icon flag-icon-{{ $code }}"></i> {{ $name }}
                                </a>
                                @endforeach
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</div>