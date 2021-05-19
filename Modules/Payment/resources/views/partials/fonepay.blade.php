<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label for="fonepay_is_active">
                <input type="radio" name="gateway[fonepay][is_enabled]" value="1" id="fonepay_is_active"
                       @if(isset($data['settings']['gateway']['fonepay']['is_enabled']) && $data['settings']['gateway']['fonepay']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['gateway']['fonepay']['is_enabled'])) checked @endif>
                <i></i> Yes </label>
            <label for="fonepay_is_inactive">
                <input type="radio" name="gateway[fonepay][is_enabled]" id="fonepay_is_inactive"
                       value="0"
                       @if(isset($data['settings']['gateway']['fonepay']['is_enabled']) && $data['settings']['gateway']['fonepay']['is_enabled'] == 0) checked @endif>
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
    <label class="col-sm-12 col-form-label">FonePay Logo </label>
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

        {!! Form::file('image_fonepay', isset($data['settings']['fonepay_image'])?array_merge($file,
           ['data-default-file' => url(get_image_url('setting', $data['settings']['fonepay_image']))]):$file)
           !!}
    </div>
    @if($errors->has('image_fonepay'))
        <label class="error" for="image_fonepay"> {{ $errors->first('image_fonepay') }}</label>
    @endif
</div>

<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Endpoint <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[fonepay][endpoint]', $data['settings']['gateway']['fonepay']['endpoint'] ?? config('gateway.fonepay.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for fonepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.fonepay.endpoint'))
        <label class="error"
               for="gateway.fonepay.endpoint"> {{ $errors->first('gateway.fonepay.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Merchant Code <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[fonepay][pid]', $data['settings']['gateway']['fonepay']['pid'] ?? config('gateway.fonepay.pid') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant code (pid) for fonepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.fonepay.pid'))
        <label class="error"
               for="gateway.fonepay.pid"> {{ $errors->first('gateway.fonepay.pid') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Secret Key <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[fonepay][secret_key]', $data['settings']['gateway']['fonepay']['secret_key'] ?? config('gateway.fonepay.secret_key') ?? null, ['class' => 'form-control','placeholder' => 'Enter the secret_key for fonepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.fonepay.secret_key'))
        <label class="error"
               for="gateway.fonepay.secret_key"> {{ $errors->first('gateway.fonepay.secret_key') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">MD (P - Payment) <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[fonepay][md]', $data['settings']['gateway']['fonepay']['md'] ?? config('gateway.fonepay.md') ?? null, ['class' => 'form-control','placeholder' => 'Enter the md for fonepay', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.fonepay.md'))
        <label class="error"
               for="gateway.fonepay.md"> {{ $errors->first('gateway.fonepay.md') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">CRN <span class="required">*</span> <br> <code>(Default Value ,NPR need to send for local merchants)</code></label>
    <div class="col-sm-12">
        {!! Form::select('gateway[fonepay][crn]', \Modules\Payment\Gateway\FonePay\FonePayConfig::CRNs(), $data['settings']['gateway']['fonepay']['crn'] ?? config('gateway.fonepay.crn') ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.fonepay.crn'))
        <label class="error"
               for="gateway.fonepay.crn"> {{ $errors->first('gateway.fonepay.crn') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

@include('partials.available-for', ['gateway' => 'fonepay'])
<div class="hr-line-dashed"></div>
