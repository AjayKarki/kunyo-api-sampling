const mix = require('laravel-mix');

mix.copyDirectory('Modules/DevSupport/resources/assets/img', 'public/dist/dev-support/img');
mix.copyDirectory('Modules/DevSupport/resources/assets/fonts', 'public/dist/dev-support/fonts');

mix.styles([
    'Modules/DevSupport/resources/assets/css/bootstrap.min.css',
    'resources/assets/css/font-awesome.min.css',
    'resources/assets/css/animate.css',
    'resources/assets/css/plugins/sweetalert2/sweetalert2.min.css',
    'resources/assets/css/sweetalert.css',
    'resources/assets/css/plugins/toastr/toastr.min.css',
    'Modules/DevSupport/resources/assets/css/style.css',
], 'public/dist/dev-support/css/backend.css');

mix.scripts([
    'Modules/DevSupport/resources/assets/js/jquery-3.1.1.min.js',
    'Modules/DevSupport/resources/assets/js/popper.min.js',
    'Modules/DevSupport/resources/assets/js/bootstrap.js',
    'resources/assets/js/plugins/metisMenu/jquery.metisMenu.js',
    'resources/assets/js/plugins/slimscroll/jquery.slimscroll.min.js',
    'Modules/DevSupport/resources/assets/js/inspinia.js',
    'resources/assets/js/plugins/pace/pace.min.js',
    'resources/assets/js/plugins/sweetalert2/sweetalert2.min.js',
    'resources/assets/js/sweetalert.min.js',
    'resources/assets/js/plugins/toastr/toastr.min.js'
], 'public/dist/dev-support/js/backend.js');

mix.scripts([
    'Modules/DevSupport/resources/assets/js/inspinia.js',
    'resources/assets/js/plugins/pace/pace.min.js',
    'resources/assets/js/plugins/sweetalert2/sweetalert2.min.js',
    'resources/assets/js/sweetalert.min.js',
    'resources/assets/js/plugins/toastr/toastr.min.js'
], 'public/dist/dev-support/js/support.js');
