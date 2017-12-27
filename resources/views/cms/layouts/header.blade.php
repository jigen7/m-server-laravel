<nav class="navbar navbar-inverse" id="header-nav">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="navbar-brand">Masarap CMS</div>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>Today is {{ date('F d, Y') }}</li>
                @if (Auth::check())
                <li>Welcome, <span>{{ Auth::user()->firstname}}</span></li>
                <li><a href="{{ URL::to('cms/logout') }}">Logout</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>