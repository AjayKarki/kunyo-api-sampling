<div class="form-group row">
    <label class="col-sm-12 col-form-label is_required"><b>Is Enabled</b></label>
    <div class="col-sm-12">
        <div class="i-checks mt-2">
            <label
                for="aakash_is_active">
                <input type="radio" name="sms[aakash][is_enabled]" value="1" id="aakash_is_active"
                       @if(isset($data['settings']['sms']['aakash']['is_enabled']) && $data['settings']['sms']['aakash']['is_enabled'] == 1) checked
                       @elseif(!isset($data['settings']['sms']['aakash']['is_enabled'])) checked @endif>

                <i></i> Yes </label>
            <label
                for="aakash_is_inactive">
                <input type="radio" name="sms[aakash][is_enabled]" id="aakash_is_inactive" value="0"
                       @if(isset($data['settings']['sms']['aakash']['is_enabled']) && $data['settings']['sms']['aakash']['is_enabled'] == 0) checked @endif>
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
        {!! Form::text('sms[aakash][endpoint]', $data['settings']['sms']['aakash']['endpoint'] ?? config('sms.aakash.endpoint') ?? null, ['class' => 'form-control','placeholder' => 'Enter the endpoint for aakash', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('sms.aakash.endpoint'))
        <label class="error" for="sms.aakash.endpoint"> {{ $errors->first('sms.aakash.endpoint') }}</label>
    @endif
</div>
<div class="hr-line-dashed"></div>

<div class="form-group row">
    <label class="col-sm-12 col-form-label">Token <span class="required">*</span></label>
    <div class="col-sm-12">
        {!! Form::text('sms[aakash][token]', $data['settings']['sms']['aakash']['token'] ?? config('sms.aakash.token') ?? null, ['class' => 'form-control','placeholder' => 'Enter the token for aakash', 'autocomplete' => 'off']) !!}
    </div>
    @if($errors->has('sms.aakash.token'))
        <label class="error" for="sms.aakash.token"> {{ $errors->first('sms.aakash.token') }}</label>
    @endif
</div>

