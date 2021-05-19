<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label
                for="sparrowsms_is_active">
                <input type="radio" name="sms[sparrowsms][is_enabled]" value="1" id="sparrowsms_is_active"
                       @if(isset($data['settings']['sms']['sparrowsms']['is_enabled']) && $data['settings']['sms']['sparrowsms']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['sms']['sparrowsms']['is_enabled'])) checked @endif>

                <i></i> Yes </label>
            <label
                for="sparrowsms_is_inactive">
                <input type="radio" name="sms[sparrowsms][is_enabled]" id="sparrowsms_is_inactive" value="0"
                       @if(isset($data['settings']['sms']['sparrowsms']['is_enabled']) && $data['settings']['sms']['sparrowsms']['is_enabled'] == 0) checked @endif>
                <i></i> No </label>
            <div>
                @if($errors->has('status'))
                    <label class="has-error" for="status">{{ $errors->first('status') }}</label>
                @endif
            </div>
        </div>

    </div>
</div>
<div class="form-group row">
    <label class="col-sm-12 col-form-label">Endpoint URL <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('sms[sparrowsms][endpoint]', $data['settings']['sms']['sparrowsms']['endpoint'] ?? config('sms.sparrowsms.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for sparrowsms', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('sms.sparrowsms.endpoint'))
        <label class="error" for="sms.sparrowsms.endpoint"> {{ $errors->first('sms.sparrowsms.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Token <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('sms[sparrowsms][token]', $data['settings']['sms']['sparrowsms']['token'] ?? config('sms.sparrowsms.token') ?? null, ['class' => 'form-control','placeholder' => 'Enter the token for sparrowsms', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('sms.sparrowsms.token'))
        <label class="error" for="sms.sparrowsms.token"> {{ $errors->first('sms.sparrowsms.token') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Identity <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('sms[sparrowsms][identity]', $data['settings']['sms']['sparrowsms']['identity'] ?? config('sms.sparrowsms.identity') ?? null, ['class' => 'form-control','placeholder' => 'Enter the api user for sparrowsms', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('sms.sparrowsms.identity'))
        <label class="error" for="sms.sparrowsms.identity"> {{ $errors->first('sms.sparrowsms.identity') }}</label>
    @endif
</div>
