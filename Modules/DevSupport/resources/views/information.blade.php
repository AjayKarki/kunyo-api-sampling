@extends('support::layouts.master')

@section('title', 'Application Information')

@section('content')
    <div class="ibox">
        <div class="ibox-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="m-b-md">
                        <h2>Application Information <i class="fa fa-info-circle"></i></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>SSL :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"><span class="label label-{{ $server->ssl_installed ? 'info' : 'primary' }}">{{ $server->ssl_installed ? 'Active' : 'Inactive' }}</span></dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Neputer Version :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">v{{ $app->neputer_version }}</dd>
                        </div>
                    </dl>
                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>App Version :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"> v{{ $app->version }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Php Version :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"> v{{ $server->version }}</dd>
                        </div>
                    </dl>
                    <br>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Server Software :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"> {{ $server->server_software }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Server OS :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"> {{ $server->server_os }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Storage Permission :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"> {{ $app->storage_dir_writable ? 'FINE' : 'ISSUE' }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Cache Permission :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1"> {{ $app->cache_dir_writable ? 'FINE' : 'ISSUE' }}</dd>
                        </div>
                    </dl>

                </div>
                <div class="col-lg-6" id="cluster_info">

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Last Updated :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">16.08.2014 12:15:57</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>App Stage :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{ $app->debug_mode ? 'Development' : 'Production' }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Timezone :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{ $app->timezone }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Database :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{ $server->database_connection_name }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Cache :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{ $server->cache_driver }}</dd>
                        </div>
                    </dl>

                    <dl class="row mb-0">
                        <div class="col-sm-4 text-sm-right">
                            <dt>Session :</dt>
                        </div>
                        <div class="col-sm-8 text-sm-left">
                            <dd class="mb-1">{{ $server->session_driver }}</dd>
                        </div>
                    </dl>
                    <br>

{{--                    <dl class="row mb-0">--}}
{{--                        <div class="col-sm-4 text-sm-right">--}}
{{--                            <dt>Participants :</dt>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-8 text-sm-left">--}}
{{--                            <dd class="project-people mb-1">--}}
{{--                                <a href=""><img alt="image" class="rounded-circle" src="img/a3.jpg"></a>--}}
{{--                                <a href=""><img alt="image" class="rounded-circle" src="img/a1.jpg"></a>--}}
{{--                                <a href=""><img alt="image" class="rounded-circle" src="img/a2.jpg"></a>--}}
{{--                                <a href=""><img alt="image" class="rounded-circle" src="img/a4.jpg"></a>--}}
{{--                                <a href=""><img alt="image" class="rounded-circle" src="img/a5.jpg"></a>--}}
{{--                            </dd>--}}
{{--                        </div>--}}
{{--                    </dl>--}}
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <dl class="row mb-0">
                        <div class="col-sm-2 text-sm-right">
                            <dt>Bug Fixes :</dt>
                        </div>
                        <div class="col-sm-10 text-sm-left">
                            <dd>
                                <div class="progress m-b-1">
                                    <div style="width: 60%;" class="progress-bar progress-bar-striped progress-bar-animated"></div>
                                </div>
                                <small>Project completed in <strong>60%</strong>. Remaining close the project, sign a contract and invoice.</small>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="m-b-md">
                        <h2>Installed Packages <i class="fa fa-gavel"></i></h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Package Name</th>
                            <th>Version</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($packages as $name => $version)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><b>{{ $name }}</b></td>
                            <td><b>{{ $version }}</b></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
