@extends('support::layouts.master')

@section('title', 'Home')

@push('css')
    <style>
        .mb-3, .my-3 {
            margin-bottom: 0rem!important;
        }

        .ibox-custom {
            margin-bottom: 12px;
        }

        .col-custom {
            padding-right: 0px;
            padding-left: 0px;
        }
</style>
@endpush

@section('content')
    <div class="ibox">
        <div class="ibox-content">
            <div>
                <div>
                    <span>Server Response Time</span>
                    <small class="float-right">{{ number_format((microtime(true) - LARAVEL_START), 2, '.', '') }} / 60 second</small>
                </div>
                <div class="progress progress-small">
                    <div style="width: {{ number_format((microtime(true) - LARAVEL_START), 2, '.', '') / 60 }}%;" class="progress-bar"></div>
                </div>

                <div>
                    <span>Log Size</span>
                    <small class="float-right">10 MB</small>
                </div>
                <div class="progress progress-small">
                    <div style="width: {{ log_file_size() }}%;" class="progress-bar"></div>
                </div>

                <div>
                    <span>Database Size</span>
                    <small class="float-right">1 GB</small>
                </div>
                <div class="progress progress-small">
                    <div style="width: 20%;" class="progress-bar progress-bar-danger"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-3">

        </div>

        <div class="col-md-6 col-lg-9">
            <div class="row">
                @foreach($data['percents'] as $level => $item)
                    @include('support::partials.i-box')
                @endforeach
            </div>
        </div>
    </div>

{{--    <div class="ibox ">--}}
{{--        <div class="ibox-title">--}}
{{--            <h5>Log Issues</h5>--}}
{{--        </div>--}}

{{--        <div class="ibox-content">--}}

{{--            <div class="activity-stream">--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-pencil"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a5.jpg">--}}
{{--                                <span>Karen Miggel</span>--}}
{{--                                <span class="date">Today at 01:32:40 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new note to the <a href="#">Martex</a>  project.--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-commenting-o"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a4.jpg">--}}
{{--                                <span>John Mikkens</span>--}}
{{--                                <span class="date">Yesterday at 10:00:20 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Commented on <a href="#">Ariana</a> profile.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a2.jpg">--}}
{{--                                <img src="img/a3.jpg">--}}
{{--                                <img src="img/a4.jpg">--}}
{{--                                <span>Mike Johnson, Monica Smith and Karen Dortmund</span>--}}
{{--                                <span class="date">Yesterday at 02:13:20 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Changed status of third stage in the <a href="#">Vertex</a> project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a6.jpg">--}}
{{--                                <span>Jessica Smith</span>--}}
{{--                                <span class="date">Yesterday at 08:14:41 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new files to own file sharing place.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-send bg-primary"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a7.jpg">--}}
{{--                                <img src="img/a1.jpg">--}}
{{--                                <span>Martha Farter and Mike Rodgers</span>--}}
{{--                                <span class="date">Yesterday at 04:18:13 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Sent email to all users participating in new project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-tag bg-warning"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a7.jpg">--}}
{{--                                <span>Mark Mickens</span>--}}
{{--                                <span class="date">Yesterday at 06:00:30 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Has been taged in the latest comments about the new project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a8.jpg">--}}
{{--                                <span>Mike Johnson</span>--}}
{{--                                <span class="date">Yesterday at 02:13:20 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Changed status of second stage in the latest project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a1.jpg">--}}
{{--                                <span>Jessica Smith</span>--}}
{{--                                <span class="date">Yesterday at 08:14:41 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new files to own file sharing place.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a6.jpg">--}}
{{--                                <span>Jessica Smith</span>--}}
{{--                                <span class="date">Yesterday at 08:14:41 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new files to own file sharing place.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-send"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a7.jpg">--}}
{{--                                <span>Martha Farter</span>--}}
{{--                                <span class="date">Yesterday at 04:18:13 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Sent email to all users participating in new project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-sliders bg-success"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a2.jpg">--}}
{{--                                <span>Mark Mickens</span>--}}
{{--                                <span class="date">Yesterday at 06:00:30 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Has been taged in the latest comments about the new project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a8.jpg">--}}
{{--                                <span>Mike Johnson</span>--}}
{{--                                <span class="date">Yesterday at 02:13:20 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Changed status of second stage in the latest project.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a1.jpg">--}}
{{--                                <span>Jessica Smith</span>--}}
{{--                                <span class="date">Yesterday at 08:14:41 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new files to own file sharing place.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-pencil"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a5.jpg">--}}
{{--                                <img src="img/a2.jpg">--}}
{{--                                <span>Karen Johnson and Sasha Miggel</span>--}}
{{--                                <span class="date">Today at 01:32:40 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new note to the <a href="#">Martex</a>  project.--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-commenting-o"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a4.jpg">--}}
{{--                                <span>John Mikkens</span>--}}
{{--                                <span class="date">Yesterday at 10:00:20 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Commented on <a href="#">Ariana</a> profile.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a2.jpg">--}}
{{--                                <span>Mike Johnson</span>--}}
{{--                                <span class="date">Yesterday at 02:13:20 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Changed status of third stage in the <a href="#">Vertex</a> project.--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="stream">--}}
{{--                    <div class="stream-badge">--}}
{{--                        <i class="fa fa-circle"></i>--}}
{{--                    </div>--}}
{{--                    <div class="stream-panel">--}}
{{--                        <div class="stream-info">--}}
{{--                            <a href="#">--}}
{{--                                <img src="img/a6.jpg">--}}
{{--                                <span>Jessica Smith</span>--}}
{{--                                <span class="date">Yesterday at 08:14:41 am</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        Add new files to own file sharing place.--}}

{{--                    </div>--}}
{{--                </div>--}}


{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
@endsection

@push('js')
@endpush
