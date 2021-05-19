const mix = require('laravel-mix');

mix.js('resources/js/kunyo.js', 'public/dist/js')
    .sass('resources/sass/kunyo.scss', 'public/dist/css');

mix.styles([
    'resources/assets/css/font-awesome.min.css',
    'resources/assets/css/animate.css',
    'resources/assets/css/plugins/sweetalert2/sweetalert2.min.css',
    'resources/assets/css/sweetalert.css',
    'resources/assets/css/plugins/toastr/toastr.min.css',
    'resources/assets/css/plugins/ladda/ladda-themeless.min.css',
    'resources/assets/js/plugins/x-editable/bootstrap-editable.css',
    'resources/assets/css/style.css',
], 'public/dist/css/backend.css');

mix.scripts([
    'resources/assets/js/plugins/metisMenu/jquery.metisMenu.js',
    'resources/assets/js/plugins/slimscroll/jquery.slimscroll.min.js',
    'resources/assets/js/inspinia.js',
    'resources/assets/js/plugins/pace/pace.min.js',
    'resources/assets/js/plugins/sweetalert2/sweetalert2.min.js',
    'resources/assets/js/sweetalert.min.js',
    'resources/assets/js/plugins/toastr/toastr.min.js',
    'resources/assets/js/plugins/ladda/spin.min.js',
    'resources/assets/js/plugins/ladda/ladda.min.js',
    'resources/assets/js/plugins/ladda/ladda.jquery.min.js',
    'resources/assets/js/plugins/x-editable/bootstrap-editable.min.js',
], 'public/dist/js/backend.js');

mix.styles([
    'resources/assets/css/plugins/dataTables/datatables.min.css',
    'resources/assets/css/plugins/iCheck/iCheck.css',
    'resources/assets/css/plugins/select2/select2.min.css',
    'resources/assets/css/plugins/dropify/dropify.min.css',
    'resources/assets/css/vendor.css',
], 'public/dist/css/plugin.css');

mix.scripts([
    'resources/assets/js/plugins/dataTables/datatables.min.js',
    'resources/assets/js/plugins/dataTables/dataTables.bootstrap4.min.js',
    'resources/assets/js/plugins/dataTables/buttons.colVis.min.js',
    'resources/assets/js/jquery-validate-1-19-1.js',
    'resources/assets/js/plugins/iCheck/icheck.min.js',
    'resources/assets/js/plugins/dropify/dropify.min.js',
    'resources/assets/js/plugins/select2/select2.full.min.js',
    'resources/assets/js/vendor.js',
], 'public/dist/js/plugin.js');

mix.styles([
    'resources/assets/css/list.css'
], 'public/dist/css/list.css');

mix.scripts([
    'resources/assets/js/list.js'
], 'public/dist/js/list.js');

mix.scripts([
    'resources/assets/js/bulk-action-manager.js'
], 'public/dist/js/bulk-action-manager.min.js');

mix.scripts([
    'resources/assets/js/show.js'
], 'public/dist/js/show.js');

mix.styles([
    'resources/assets/js/plugins/jquery-ui/jquery-ui.min.css',
    'resources/assets/css/menu-builder.css'
], 'public/dist/css/menu-builder.css');

mix.scripts([
    'resources/assets/js/plugins/jquery-ui/jquery-ui.min.js',
    'resources/assets/js/jquery.multisortable.js',
    'resources/assets/js/menu-builder.js',
], 'public/dist/js/menu-builder.js');

mix.styles([
    'resources/assets/js/plugins/jquery-ui/jquery-ui.min.css',
    'resources/assets/css/customizer.css'
], 'public/dist/css/customizer.css');

mix.scripts([
    'resources/assets/js/plugins/jquery-ui/jquery-ui.min.js',
    'resources/assets/js/jquery.multisortable.js',
    'resources/assets/js/customizer.js',
], 'public/dist/js/customizer.js');

mix.styles([
    'resources/assets/css/plugins/c3/c3.min.css',
    'resources/assets/css/plugins/morris/morris-0.4.3.min.css',
], 'public/dist/css/statistics.css');

mix.scripts([
    'resources/assets/js/plugins/morris/raphael-2.1.0.min.js',
    'resources/assets/js/plugins/morris/morris.js',
], 'public/dist/js/morris.js');

mix.copy('resources/assets/js/plugins/morris/raphael-2.1.0.min.js', 'public/dist/js/raphael.js');
mix.copy('resources/assets/js/plugins/morris/morris.js', 'public/dist/js/morris.js');

mix.scripts([
    'resources/assets/js/plugins/d3/d3.min.js',
    'resources/assets/js/plugins/c3/c3.min.js',
    'resources/assets/js/statistics.js',
], 'public/dist/js/statistics.js');

mix.scripts([
    'resources/assets/js/plugins/d3/d3.min.js',
    'resources/assets/js/plugins/c3/c3.min.js',
], 'public/dist/js/statistics-vendor.js')
mix.js([
    'resources/assets/js/g2a-vue.js'
], 'public/dist/js/g2a.js');


mix.scripts([
    'resources/assets/js/plugins/d3/d3.min.js',
    'resources/assets/js/plugins/c3/c3.min.js',
], 'public/dist/js/statistics-vendor.js')

mix.js([
    'resources/assets/js/vue.js'
], 'public/dist/js/vue.js');
