@component('mail::layout')

@slot('header')
@component('mail::header', [ 'url' => url('/') ?? config('app.url') ])
{{ config('app.name') }}
@endcomponent
@endslot

{{ $content }}

@slot('footer')
@component('mail::footer')
&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
@endslot

@endcomponent
