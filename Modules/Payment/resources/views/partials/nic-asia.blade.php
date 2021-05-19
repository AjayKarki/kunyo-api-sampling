<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label for="nicasia_is_active">
                <input type="radio" name="gateway[nicasia][is_enabled]" value="1" id="nicasia_is_active"
                       @if(isset($data['settings']['gateway']['nicasia']['is_enabled']) && $data['settings']['gateway']['nicasia']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['gateway']['nicasia']['is_enabled'])) checked @endif>
                <i></i> Yes </label>
            <label for="nicasia_is_inactive">
                <input type="radio" name="gateway[nicasia][is_enabled]" id="nicasia_is_inactive"
                       value="0"
                       @if(isset($data['settings']['gateway']['nicasia']['is_enabled']) && $data['settings']['gateway']['nicasia']['is_enabled'] == 0) checked @endif>
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
    <label class="col-sm-12 col-form-label">
        Nic Asia Logo
    </label>
    <div class="col-sm-12">
        @php($file = [
        'id' => 'image_path',
        'class' => 'form-control dropify img-responsive',
        'data-plugin' => 'dropify',
        'data-height' => '100',
        'data-show-remove'=>'false',
        'data-allowed-file-extensions'=>'pdf png psd jpeg jpg gif',
        'data-disable-remove'=> 'true',
        'data-max-file-size' => '2M',
                     ])

        {!! Form::file('image_nicasia', isset($data['settings']['nicasia_image'])?array_merge($file,
           ['data-default-file' => url(get_image_url('setting', $data['settings']['nicasia_image']))]):$file)
           !!}
    </div>
    @if($errors->has('image_nicasia'))
        <label class="error" for="image_nicasia"> {{ $errors->first('image_nicasia') }}</label>
    @endif
</div>

<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">
        Endpoint <span class="required">*</span> <br>
        <small class="pull-right"><b><a class="nic-asia-endpoint" href="" data-endpoint="https://testsecureacceptance.cybersource.com/pay">TEST</a> | <a class="nic-asia-endpoint" href="" data-endpoint="https://secureacceptance.cybersource.com/pay">LIVE</a></b></small>
    </label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][endpoint]', $data['settings']['gateway']['nicasia']['endpoint'] ?? config('gateway.nicasia.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.endpoint'))
        <label class="error" for="gateway.nicasia.endpoint"> {{ $errors->first('gateway.nicasia.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">
        Verification Endpoint <span class="required">*</span>
    </label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][verify_endpoint]', $data['settings']['gateway']['nicasia']['verify_endpoint'] ?? config('gateway.nicasia.verify_endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the verify endpoint for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.verify_endpoint'))
        <label class="error" for="gateway.nicasia.verify_endpoint"> {{ $errors->first('gateway.nicasia.verify_endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">
        Merchant Key ID <span class="required">*</span>
    </label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][merchant_key_id]', $data['settings']['gateway']['nicasia']['merchant_key_id'] ?? config('gateway.nicasia.merchant_key_id') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant key id for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.merchant_key_id'))
        <label class="error" for="gateway.nicasia.merchant_key_id"> {{ $errors->first('gateway.nicasia.merchant_key_id') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">
        Merchant Key Secret <span class="required">*</span>
    </label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][merchant_key_secret]', $data['settings']['gateway']['nicasia']['merchant_key_secret'] ?? config('gateway.nicasia.merchant_key_secret') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant key secret for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.merchant_key_secret'))
        <label class="error" for="gateway.nicasia.merchant_key_secret"> {{ $errors->first('gateway.nicasia.merchant_key_secret') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">
        Merchant Id <span class="required">*</span>
    </label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][merchant_id]', $data['settings']['gateway']['nicasia']['merchant_id'] ?? config('gateway.nicasia.merchant_id') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant_id for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.merchant_id'))
        <label class="error" for="gateway.nicasia.merchant_id"> {{ $errors->first('gateway.nicasia.merchant_id') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">
        Currency <span class="required">*</span>
        <small class="pull-right"><b>For live use <code>NPR</code>. For test use <code>USD</code>.</b></small>
    </label>
    <div class="col-sm-12">
        {!! Form::select('gateway[nicasia][currency]',
            \Modules\Payment\Gateway\NicAsia\NicAsiaConfig::CURRENCY,
            $data['settings']['gateway']['nicasia']['currency'] ?? config('gateway.nicasia.currency') ?? null,
            [ 'class' => 'form-control' ]) !!}
    </div>
    @if($errors->has('gateway.nicasia.currency'))
        <label class="error" for="gateway.nicasia.profile_id"> {{ $errors->first('gateway.nicasia.currency') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Card Type <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::select('gateway[nicasia][card_type]',
            \Modules\Payment\Gateway\NicAsia\NicAsiaConfig::CARD_TYPE,
            $data['settings']['gateway']['nicasia']['card_type'] ?? config('gateway.nicasia.card_type') ?? null,
            [ 'class' => 'form-control' ]) !!}
    </div>
    @if($errors->has('gateway.nicasia.card_type'))
        <label class="error" for="gateway.nicasia.profile_id"> {{ $errors->first('gateway.nicasia.card_type') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Profile ID <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][profile_id]', $data['settings']['gateway']['nicasia']['profile_id'] ?? config('gateway.nicasia.profile_id') ?? null, ['class' => 'form-control','placeholder' => 'Enter the profile id for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.profile_id'))
        <label class="error" for="gateway.nicasia.profile_id"> {{ $errors->first('gateway.nicasia.profile_id') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Payment Method <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][payment_method]', $data['settings']['gateway']['nicasia']['payment_method'] ?? config('gateway.nicasia.payment_method') ?? null, ['class' => 'form-control','placeholder' => 'Enter the profile id for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.payment_method'))
        <label class="error" for="gateway.nicasia.payment_method"> {{ $errors->first('gateway.nicasia.payment_method') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Secret Key <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::textarea('gateway[nicasia][secret_key]', $data['settings']['gateway']['nicasia']['secret_key'] ?? config('gateway.nicasia.secret_key') ?? null, ['class' => 'form-control','placeholder' => 'Enter the secret key for nicasia', 'rows' => '3']) !!}
    </div>
    @if($errors->has('gateway.nicasia.secret_key'))
        <label class="error" for="gateway.nicasia.secret_key"> {{ $errors->first('gateway.nicasia.secret_key') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Access Key <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[nicasia][access_key]', $data['settings']['gateway']['nicasia']['access_key'] ?? config('gateway.nicasia.access_key') ?? null, ['class' => 'form-control','placeholder' => 'Enter the access key for nicasia', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.nicasia.access_key'))
        <label class="error" for="gateway.nicasia.access_key"> {{ $errors->first('gateway.nicasia.access_key') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>
