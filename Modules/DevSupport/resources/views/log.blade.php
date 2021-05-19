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

    <div class="ibox">
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        @foreach($data['headers'] as $key => $header)
                            <th scope="col" class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                @if ($key == 'date')
                                    <span class="badge badge-info">{{ $header }}</span>
                                @else
                                    <span class="badge badge-level-{{ $key }}">
                                    {{ log_styler()->icon($key) }} {{ $header }}
                            </span>
                                @endif
                            </th>
                        @endforeach
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($data['rows'] as $date => $row)
                        <tr>
                            @foreach($row as $key => $value)
                                <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                    @if ($key == 'date')
                                        <span class="badge badge-primary">{{ $value }}</span>
                                    @elseif ($value == 0)
                                        <span class="badge empty">{{ $value }}</span>
                                    @else
                                        <a href="{{ route('support::support.log.search', [$date, $key]) }}">
                                            <span class="badge badge-level-{{ $key }}">{{ $value }}</span>
                                        </a>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-right">
                                <a href="{{ route('support::support.log.show', [$date]) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-search"></i>
                                </a>
                                <a href="{{ route('support::support.log.download', [$date]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-download"></i>
                                </a>
                                <a href="#delete-log-modal" class="btn btn-sm btn-danger" data-log-date="{{ $date }}">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">
                                <span class="badge badge-secondary">{{ trans('log-viewer::general.empty-logs') }}</span>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $data['rows']->render() }}
        </div>
    </div>

    @include('support::partials.delete-modal')
@endsection

@push('js')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            $("a[href='#delete-log-modal']").on('click', function(event) {
                event.preventDefault();
                var date = $(this).data('log-date');
                deleteLogForm.find('input[name=date]').val(date);
                deleteLogModal.find('.modal-body p').html(
                    'Are you sure you want to <span class="badge badge-danger">DELETE</span> this log file <span class="badge badge-primary">' + date + '</span> ?'
                );

                deleteLogModal.modal('show');
            });

            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.reload();
                        }
                        else {
                            alert('AJAX ERROR ! Check the console !');
                            console.error(data);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            deleteLogModal.on('hidden.bs.modal', function() {
                deleteLogForm.find('input[name=date]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
@endpush
