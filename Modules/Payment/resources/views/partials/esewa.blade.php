<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label
                for="esewa_is_active">
                <input type="radio" name="gateway[esewa][is_enabled]" value="1" id="esewa_is_active"
                       @if(isset($data['settings']['gateway']['esewa']['is_enabled']) && $data['settings']['gateway']['esewa']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['gateway']['esewa']['is_enabled'])) checked @endif>
                <i></i> Yes </label>
            <label
                for="esewa_is_inactive">
                <input type="radio" name="gateway[esewa][is_enabled]" id="esewa_is_inactive" value="0"
                       @if(isset($data['settings']['gateway']['esewa']['is_enabled']) && $data['settings']['gateway']['esewa']['is_enabled'] == 0) checked @endif>
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
    <label class="col-sm-12 col-form-label">Esewa Logo </label>
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

        {!! Form::file('image_esewa', isset($data['settings']['esewa_image'])?array_merge($file,
           ['data-default-file' => url(get_image_url('setting', $data['settings']['esewa_image']))]):$file)
           !!}
    </div>
    @if($errors->has('image_esewa'))
        <label class="error" for="image_esewa"> {{ $errors->first('image_esewa') }}</label>
    @endif
</div>

<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Endpoint URL <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[esewa][endpoint]', $data['settings']['gateway']['esewa']['endpoint'] ?? config('gateway.esewa.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for esewa', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.esewa.endpoint'))
        <label class="error" for="gateway.esewa.endpoint"> {{ $errors->first('gateway.esewa.endpoint') }}</label>
    @endif
</div>

<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Merchant ID <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('gateway[esewa][merchant_id]', $data['settings']['gateway']['esewa']['merchant_id'] ?? config('gateway.esewa.merchant_id') ?? null, ['class' => 'form-control','placeholder' => 'Enter the merchant id for esewa', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('gateway.esewa.merchant_id'))
        <label class="error" for="gateway.esewa.merchant_id"> {{ $errors->first('gateway.esewa.merchant_id') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

@include('partials.available-for', ['gateway' => 'esewa'])
