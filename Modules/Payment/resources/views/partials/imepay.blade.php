<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label for="imepay_is_active">
                <input type="radio" name="gateway[imepay][is_enabled]"
                       value="1" id="imepay_is_active"
                       @if(isset($data['settings']['gateway']['imepay']['is_enabled']) && $data['settings']['gateway']['imepay']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['gateway']['imepay']['is_enabled'])) checked @endif>
                <i></i> Yes </label>
            <label for="imepay_is_inactive">
                <input type="radio" name="gateway[imepay][is_enabled]" id="imepay_is_inactive"
                       value="0"
                       @if(isset($data['settings']['gateway']['imepay']['is_enabled']) && $data['settings']['gateway']['imepay']['is_enabled'] == 0) checked @endif>
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
    <label class="col-sm-12 col-form-label">ImePay Logo </label>
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

        {!! Form::file('image_ime_pay', isset($data['settings']['ime_pay_image'])?array_merge($file,
           ['data-default-file' => url(get_image_url('setting', $data['settings']['ime_pay_image']))]):$file)
           !!}
    </div>
    @if($errors->has('image_ime_pay'))
        <label class="error" for="image_ime_pay"> {{ $errors->first('image_ime_pay') }}</label>
    @endif
</div>


<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Endpoint URL <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[imepay][endpoint]', $data['settings']['gateway']['imepay']['endpoint'] ?? config('gateway.imepay.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for imepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('payement.imepay.endpoint'))
        <label class="error" for="payement.imepay.endpoint"> {{ $errors->first('payement.imepay.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Merchant Code <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[imepay][merchant_code]', $data['settings']['gateway']['imepay']['merchant_code'] ?? config('gateway.imepay.merchant_code') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant code for imepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('payement.imepay.merchant_code'))
        <label class="error"
               for="payement.imepay.merchant_code"> {{ $errors->first('payement.imepay.merchant_code') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Api User <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[imepay][apiuser]', $data['settings']['gateway']['imepay']['apiuser'] ?? config('gateway.imepay.apiuser') ?? null, ['class' => 'form-control','placeholder' => 'Enter the api user for imepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('payement.imepay.apiuser'))
        <label class="error" for="payement.imepay.apiuser"> {{ $errors->first('payement.imepay.apiuser') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Password <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[imepay][password]', $data['settings']['gateway']['imepay']['password'] ?? config('gateway.imepay.password') ?? null, ['class' => 'form-control','placeholder' => 'Enter the password for imepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('payement.imepay.password'))
        <label class="error" for="payement.imepay.password"> {{ $errors->first('payement.imepay.password') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Module <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[imepay][module]', $data['settings']['gateway']['imepay']['module'] ?? config('gateway.imepay.module') ?? null, ['class' => 'form-control','placeholder' => 'Enter the module for imepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('payement.imepay.module'))
        <label class="error" for="payement.imepay.module"> {{ $errors->first('payement.imepay.module') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

@include('partials.available-for', ['gateway' => 'imepay'])
