@extends('support::layouts.master')

@section('title', 'Log')

@push('css')
    <style>
        .badge.badge-env,
        .badge.badge-level-all,
        .badge.badge-level-emergency,
        .badge.badge-level-alert,
        .badge.badge-level-critical,
        .badge.badge-level-error,
        .badge.badge-level-warning,
        .badge.badge-level-notice,
        .badge.badge-level-info,
        .badge.badge-level-debug,
        .badge.empty {
            color: #FFF;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
        }

        .badge.badge-level-all,
        .box.level-all {
            background-color: #8A8A8A;
        }

        .badge.badge-level-emergency,
        .box.level-emergency {
            background-color: #B71C1C;
        }

        .badge.badge-level-alert,
        .box.level-alert  {
            background-color: #D32F2F;
        }

        .badge.badge-level-critical,
        .box.level-critical {
            background-color: #F44336;
        }

        .badge.badge-level-error,
        .box.level-error {
            background-color: #FF5722;
        }

        .badge.badge-level-warning,
        .box.level-warning {
            background-color: #FF9100;
        }

        .badge.badge-level-notice,
        .box.level-notice {
            background-color: #4CAF50;
        }

        .badge.badge-level-info,
        .box.level-info {
            background-color: #1976D2;
        }

        .badge.badge-level-debug,
        .box.level-debug {
            background-color: #90CAF9;
        }

        .badge.empty,
        .box.empty {
            background-color: #676a6c;
        }

        .badge.badge-env {
            background-color: #6A1B9A;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <b>Log info <code>{{ $log->date }}</code></b>
                    <div class="group-btns pull-right">
                        <a href="{{ route('support::support.log.download', [$log->date]) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> DOWNLOAD
                        </a>
                        <a href="#delete-log-modal" class="btn btn-sm btn-danger" data-toggle="modal">
                            <i class="fa fa-trash-o"></i> DELETE
                        </a>
                        <a href="{{ route('support::support.log') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed mb-0">
                        <tbody>
                        <tr>
                            <td>File path :</td>
                            <td colspan="7">{{ $log->getPath() }}</td>
                        </tr>
                        <tr>
                            <td>Log entries :</td>
                            <td>
                                <span class="badge badge-primary">{{ $entries->total() }}</span>
                            </td>
                            <td>Size :</td>
                            <td>
                                <span class="badge badge-primary">{{ $log->size() }}</span>
                            </td>
                            <td>Created at :</td>
                            <td>
                                <span class="badge badge-primary">{{ $log->createdAt() }}</span>
                            </td>
                            <td>Updated at :</td>
                            <td>
                                <span class="badge badge-primary">{{ $log->updatedAt() }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <form action="{{ route('support::support.log.search', [$log->date, $level]) }}" method="GET">
                        <div class=form-group">
                            <div class="input-group">
                                <input id="query" name="query" class="form-control" value="{{ $query }}" placeholder="Type here to search">
                                <div class="input-group-append">
                                    @unless (is_null($query))
                                        <a href="{{ route('support::support.log.show', [$log->date]) }}" class="btn btn-secondary">
                                            ({{ $entries->count() }} results) <i class="fa fa-fw fa-times"></i>
                                        </a>
                                    @endunless
                                    <button id="search-btn" class="btn btn-primary">
                                        <span class="fa fa-fw fa-search"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header"><i class="fa fa-fw fa-flag"></i> Levels</div>
                <div class="list-group list-group-flush log-menu">
                    @foreach($log->menu() as $levelKey => $item)
                        @if ($item['count'] === 0)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                                <span class="level-name">{!! $item['icon'] !!} {{ $item['name'] }}</span>
                                <span class="badge empty">{{ $item['count'] }}</span>
                            </a>
                        @else
                            <a href="{{ route('support::support.log.filter', [ 'date' => request()->route('date'), 'level' => $levelKey, ]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center level-{{ $levelKey }}{{ $level === $levelKey ? ' active' : ''}}">
                                <span class="level-name">{!! $item['icon'] !!} {{ $item['name'] }}</span>
                                <span class="badge badge-level-{{ $levelKey }}">{{ $item['count'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-9">

            <div class="card mb-4">
                @if ($entries->hasPages())
                    <div class="card-header">
                        <span class="badge badge-info float-right">
                            Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}
                        </span>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="entries" class="table mb-0">
                        <thead>
                        <tr>
                            <th>ENV</th>
                            <th style="width: 120px;">Level</th>
                            <th style="width: 65px;">Time</th>
                            <th>Header</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($entries as $key => $entry)
                            <tr>
                                <td>
                                    <span class="badge badge-env">{{ $entry->env }}</span>
                                </td>
                                <td>
                                        <span class="badge badge-level-{{ $entry->level }}">
                                            {!! $entry->level() !!}
                                        </span>
                                </td>
                                <td>
                                        <span class="badge badge-secondary">
                                            {{ $entry->datetime->format('H:i:s') }}
                                        </span>
                                </td>
                                <td>
                                    {{ $entry->header }}
                                </td>
                                <td class="text-right">
                                    @if ($entry->hasStack())
                                        <a class="btn btn-sm btn-light" role="button" data-toggle="collapse"
                                           href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> Stack
                                        </a>
                                    @endif

                                    @if ($entry->hasContext())
                                        <a class="btn btn-sm btn-light" role="button" data-toggle="collapse"
                                           href="#log-context-{{ $key }}" aria-expanded="false" aria-controls="log-context-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> Context
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if ($entry->hasStack() || $entry->hasContext())
                                <tr>
                                    <td colspan="5" class="stack py-0">
                                        @if ($entry->hasStack())
                                            <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                                @dump($entry->stack())
                                            </div>
                                        @endif

                                        @if ($entry->hasContext())
                                            <div class="stack-content collapse" id="log-context-{{ $key }}">
                                                <pre>{{ $entry->context() }}</pre>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <span class="badge badge-secondary">No Logs!</span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {!! $entries->appends(compact('query'))->render() !!}
        </div>
    </div>

@endsection

@push('js')

@endpush
