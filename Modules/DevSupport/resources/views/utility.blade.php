@extends('support::layouts.master')

@section('title', 'Utility')

@section('content')
    <div class="ibox">
        <div class="ibox-content">
            <a class="btn btn-primary" href="{{ route('admin.quick-settings', [ 'pattern' => \Foundation\Lib\QuickSetting::PATTERN_CLEAR_CACHE, ]) }}">
                <i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold"> Clear Cache</span>
            </a>

            <a class="btn btn-primary" href="{{ route('admin.quick-settings', [ 'pattern' => \Foundation\Lib\QuickSetting::PATTERN_CLEAR_LOG, ]) }}">
                <i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold"> Clear Log</span>
            </a>

            <a class="btn btn-primary" href="{{ route('admin.quick-settings', [ 'pattern' => \Foundation\Lib\QuickSetting::PATTERN_CLEAR_VIEW, ]) }}">
                <i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold"> Clear View</span>
            </a>
        </div>
    </div>

@endsection

@push('js')
@endpush
