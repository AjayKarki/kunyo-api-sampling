<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label for="khalti_is_active">
                <input type="radio" name="gateway[khalti][is_enabled]" value="1" id="khalti_is_active"
                       @if(isset($data['settings']['gateway']['khalti']['is_enabled']) && $data['settings']['gateway']['khalti']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['gateway']['khalti']['is_enabled'])) checked @endif>
                <i></i> Yes </label>
            <label for="khalti_is_inactive">
                <input type="radio" name="gateway[khalti][is_enabled]" id="khalti_is_inactive"
                       value="0"
                       @if(isset($data['settings']['gateway']['khalti']['is_enabled']) && $data['settings']['gateway']['khalti']['is_enabled'] == 0) checked @endif>
                <i></i> No </label>
            <div>
                @if($errors->has('status'))
                    <label class="has-error" for="status">{{ $errors->first('status') }}</label>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="hr-line-dashed"></div>
<div class="form-group row">
    <label class="col-sm-12 col-form-label">Khalti Logo </label>
    <div class="col-sm-12">
        @php($file = [
        'id' => 'image_path',
        'class' => 'form-control dropify img-responsive',
        'data-plugin' => 'dropify',
        'data-height' => '200',
        'data-show-remove'=>'false',
        'data-allowed-file-extensions'=>'pdf png psd jpeg jpg gif',
        'data-disable-remove'=> 'true',
        'data-max-file-size' => '2M',
                     ])

        {!! Form::file('image_khalti', isset($data['settings']['khalti_image'])?array_merge($file,
           ['data-default-file' => url(get_image_url('setting', $data['settings']['khalti_image']))]):$file)
           !!}
    </div>
    @if($errors->has('image_khalti'))
        <label class="error" for="image_khalti"> {{ $errors->first('image_khalti') }}</label>
    @endif
</div>

<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Endpoint <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[khalti][endpoint]', $data['settings']['gateway']['khalti']['endpoint'] ?? config('gateway.khalti.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for khalti', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.khalti.endpoint'))
        <label class="error" for="gateway.khalti.endpoint"> {{ $errors->first('gateway.khalti.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Public Key <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[khalti][publicKey]', $data['settings']['gateway']['khalti']['publicKey'] ?? config('gateway.khalti.publicKey') ?? null, ['class' => 'form-control','placeholder' => 'Enter the publicKey for khalti', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.khalti.publicKey'))
        <label class="error" for="gateway.khalti.publicKey"> {{ $errors->first('gateway.khalti.publicKey') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Secret Key <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[khalti][secretKey]', $data['settings']['gateway']['khalti']['secretKey'] ?? config('gateway.khalti.secretKey') ?? null, ['class' => 'form-control','placeholder' => 'Enter the secretKey for khalti', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.khalti.secretKey'))
        <label class="error" for="gateway.khalti.secretKey"> {{ $errors->first('gateway.khalti.secretKey') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

@include('partials.available-for', ['gateway' => 'khalti'])
