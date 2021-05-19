<div class="col-sm-6 col-md-12 col-lg-4 mb-3 col-custom">
    <div class="ibox ibox-custom">
        <div class="ibox-content">
            @php
                $color = config('support.logger.style.color.'.$level);
            @endphp

            <h5 style="{{ $color ? 'color: '.$color.';' : '' }}">{!! log_styler()->icon($level) !!} {{ $item['name'] }}</h5>
            <h2>{{ $item['percent'] }}%</h2>
            <div class="progress progress-mini">
                <div style="width: {{ $item['percent'] }}%;{{ $color ? 'background-color: '.$color.';' : '' }}" class="progress-bar"></div>
            </div>

            <div class="m-t-sm small">{{ $item['count'] }} entries - {!! $item['percent'] !!} %</div>
        </div>
    </div>
</div>
