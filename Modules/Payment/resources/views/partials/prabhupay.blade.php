<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label for="prabhupay_is_active">
                <input type="radio" name="gateway[prabhupay][is_enabled]" value="1" id="prabhupay_is_active"
                       @if(isset($data['settings']['gateway']['prabhupay']['is_enabled']) && $data['settings']['gateway']['prabhupay']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['gateway']['prabhupay']['is_enabled'])) checked @endif>
                <i></i> Yes </label>
            <label for="prabhupay_is_inactive">
                <input type="radio" name="gateway[prabhupay][is_enabled]" id="prabhupay_is_inactive"
                       value="0"
                       @if(isset($data['settings']['gateway']['prabhupay']['is_enabled']) && $data['settings']['gateway']['prabhupay']['is_enabled'] == 0) checked @endif>
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

        {!! Form::file('image_prabhupay', isset($data['settings']['prabhupay_image'])?array_merge($file,
           ['data-default-file' => url(get_image_url('setting', $data['settings']['prabhupay_image']))]):$file)
           !!}
    </div>
    @if($errors->has('image_prabhupay'))
        <label class="error" for="image_prabhupay"> {{ $errors->first('image_prabhupay') }}</label>
    @endif
</div>

<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Endpoint <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[prabhupay][endpoint]', $data['settings']['gateway']['prabhupay']['endpoint'] ?? config('gateway.prabhupay.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for prabhupay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.prabhupay.endpoint'))
        <label class="error"
               for="gateway.prabhupay.endpoint"> {{ $errors->first('gateway.prabhupay.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Merchant Id <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[prabhupay][merchantId]', $data['settings']['gateway']['prabhupay']['merchantId'] ?? config('gateway.prabhupay.merchantId') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant id for prabhupay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.prabhupay.merchantId'))
        <label class="error"
               for="gateway.prabhupay.merchantId"> {{ $errors->first('gateway.prabhupay.merchantId') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Password <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[prabhupay][password]', $data['settings']['gateway']['prabhupay']['password'] ?? config('gateway.prabhupay.password') ?? null, ['class' => 'form-control','placeholder' => 'Enter the password for prabhupay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.prabhupay.password'))
        <label class="error"
               for="gateway.prabhupay.password"> {{ $errors->first('gateway.prabhupay.password') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

@include('partials.available-for', ['gateway' => 'prabhupay'])
