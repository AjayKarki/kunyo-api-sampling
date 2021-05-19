<nav class="navbar navbar-expand-lg navbar-static-top" role="navigation">

    <a href="{{ route('support::support.home') }}" class="navbar-brand"><i class="fa fa-bug fa-2x"></i> Neputer Support</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-reorder"></i>
    </button>

    <div class="navbar-collapse collapse" id="navbar">
        <ul class="nav navbar-nav mr-auto">
            <li class="{{ request()->is('support/dashboard') ? 'active' : '' }}">
                <a aria-expanded="false" title="Dashboard" role="button" href="{{ route('support::support.home') }}"> <i class="fa fa-2x fa-home"></i> </a>
            </li>
            <li class="{{ request()->is('support/log') ? 'active' : '' }}">
                <a aria-expanded="false" title="Log & issues" role="button" href="{{ route('support::support.log') }}"> <i class="fa fa-2x fa-file-text-o" aria-hidden="true"></i> </a>
            </li>
            <li class="{{ request()->is('support/utility') ? 'active' : '' }}">
                <a aria-expanded="false" title="Application Utility" role="button" href="{{ route('support::support.utility') }}"> <i class="fa fa-2x fa-gavel"></i> </a>
            </li>
            <li class="{{ request()->is('support/information') ? 'active' : '' }}">
                <a aria-expanded="false" title="Application Information" role="button" href="{{ route('support::support.information') }}"> <i class="fa fa-2x fa-info-circle"></i> </a>
            </li>
        </ul>
        <ul class="nav navbar-top-links navbar-right">
            @if (Route::has('admin.dashboard.index'))
            <li>
                <a href="{{ route('admin.dashboard.index') }}">
                    <i class="fa fa-tachometer"></i> Dashboard
                </a>
            </li>
            @endif
            <li>
                <a href="javascript:void(0);"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <i class="fa fa-power-off"></i> Log out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {!! csrf_field() !!}
                </form>
            </li>
        </ul>
    </div>
</nav>
