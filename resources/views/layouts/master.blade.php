<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }} | {{ $title }}</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{ asset('/') }}css/vendor.min.css" rel="stylesheet" />
    <link href="{{ asset('/') }}css/apple/app.min.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/ionicons/css/ionicons.min.css" rel="stylesheet" />
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{ asset('/') }}plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/bootstrap-calendar/css/bootstrap_calendar.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/nvd3/build/nv.d3.css" rel="stylesheet" />
    <!-- ================== END page-css ================== -->

    @stack('style')
</head>

<body>
    <div id="app" class="app app-header-fixed app-sidebar-fixed">
        <div id="header" class="app-header">
            <div class="navbar-header">
                <a href="/" class="navbar-brand">
                    <span class="navbar-logo">
                        <img src="{{ asset('img/logo/kalbe.png') }}" width="20" alt="kalbe logo">
                    </span>
                    <b class="me-1">Kalbe</b> <span style="color: #83bb43">Morinaga</span></a>
                </a>
                <button type="button" class="navbar-mobile-toggler" data-toggle="app-sidebar-mobile">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <div class="navbar-nav">
                @auth
                <div class="navbar-item navbar-user dropdown">
                    <a href="#" class="navbar-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="{{ asset('/') }}img/user/user-13.jpg" alt="" />
                        <span>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <b class="caret"></b>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end me-1">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">Log Out</a>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
                @else
                <div class="navbar-item navbar-user">
                    <a href="#modal-dialog" class="btn btn-success" data-bs-toggle="modal"><i class="fas fa-sign-in-alt"></i> Login</a>
                </div>
                @endauth
            </div>
        </div>

        <div id="sidebar" class="app-sidebar">
            <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
                <div class="menu">
                    <div id="appSidebarProfileMenu" class="collapse">
                    </div>

                    <div class="menu-header">Navigation</div>
                    @auth
                    <div class="menu-item {{ request()->is('dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-home bg-blue"></i>
                            </div>
                            <div class="menu-text">Dashboard</div>
                        </a>
                    </div>

                    <div class="menu-item {{ request()->is('devices*') ? 'active' : '' }}">
                        <a href="{{ route('devices.index') }}" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-tablet bg-indigo"></i>
                            </div>
                            <div class="menu-text">Device</div>
                        </a>
                    </div>

                    <div class="menu-item {{ request()->is('karyawan*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.index') }}" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-users bg-warning"></i>
                            </div>
                            <div class="menu-text">Karyawan</div>
                        </a>
                    </div>

                    <div class="menu-item {{ request()->is('logs*') ? 'active' : '' }}">
                        <a href="{{ route('logs.index') }}" class="menu-link">
                            <div class="menu-icon">
                                <i class="fas fa-clock bg-secondary"></i>
                            </div>
                            <div class="menu-text">Logs</div>
                        </a>
                    </div>
                    @endauth
                    <div class="menu-item d-flex">
                        <a href="javascript:;" class="app-sidebar-minify-btn ms-auto" data-toggle="app-sidebar-minify"><i class="ion-ios-arrow-back"></i>
                            <div class="menu-text">Collapse</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-sidebar-bg"></div>
        <div class="app-sidebar-mobile-backdrop"><a href="#" data-dismiss="app-sidebar-mobile" class="stretched-link"></a></div>

        <div id="content" class="app-content">

            @auth
            <ol class="breadcrumb float-xl-end">
                @foreach($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item"><a href="javascript:;">{{ $breadcrumb }}</a></li>
                @endforeach
            </ol>
            @endauth

            <h1 class="page-header">{{ $title }}</h1>

            @yield('content')

        </div>

        <a href="javascript:;" class="btn btn-icon btn-circle btn-primary btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
    </div>

    @guest
    <div class="modal fade" id="modal-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Login</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="username">Username or Email</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username or Email">

                            @error('username')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">

                            @error('password')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-success">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endguest

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('/') }}js/vendor.min.js"></script>
    <script src="{{ asset('/') }}js/app.min.js"></script>
    <script src="{{ asset('/') }}js/theme/apple.min.js"></script>
    <!-- ================== END core-js ================== -->

    <!-- ================== BEGIN page-js ================== -->
    <script src="{{ asset('/') }}plugins/d3/d3.min.js"></script>
    <script src="{{ asset('/') }}plugins/nvd3/build/nv.d3.min.js"></script>
    <script src="{{ asset('/') }}plugins/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="{{ asset('/') }}plugins/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
    <script src="{{ asset('/') }}plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
    <script src="{{ asset('/') }}js/demo/dashboard-v2.js"></script>
    <!-- ================== END page-js ================== -->

    @stack('script')
</body>

</html>