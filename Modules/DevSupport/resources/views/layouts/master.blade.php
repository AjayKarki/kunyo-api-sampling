@include('support::layouts.partials.header')

<body class="top-navigation">

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom white-bg">
                @include('support::layouts.partials.nav')
            </div>
            <div class="wrapper wrapper-content">
                <div class="container">
                    @yield('content')
                </div>
            </div>

            @include('support::layouts.partials.footer')
        </div>
    </div>

    <script src="{{ asset('dist/dev-support/js/backend.js') }}"></script>
    @stack('js')

</body>

</html>
