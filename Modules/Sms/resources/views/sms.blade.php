<div class="tabs-container">
    <ul class="nav nav-tabs" role="tablist">
        <li><a class="nav-link smsTab" data-toggle="tab" href="#aakash"> AakashSms</a></li>
        <li><a class="nav-link smsTab" data-toggle="tab" href="#sparrow"> SparrowSms</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" id="aakash" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.aakash')
            </div>
        </div>
        <div role="tabpanel" id="sparrow" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.sparrow')
            </div>
        </div>
    </div>
</div>


<div class="hr-line-dashed"></div>
<div class="form-group row">
    <div class="pull-left">
        <a data-toggle="collapse" class="col-sm-10" id="sms-settings" href="#SmsSettings"
           role="button" aria-expanded="false" aria-controls="SmsSettings">
            <b>Order Template</b> <i class="fa fa-angle-down rotate-icon"></i>
        </a>
    </div>
</div>
<div class="hr-line-dashed"></div>
<div class="collapse" id="SmsSettings">
    <div class="row form-group">
        <label class="col-sm-12 col-form-label">
            <b>Order is Created .</b>
            <code> Placeholder available : <b><span
                        class="template-placeholder">{!! implode('</span>,<span class="template-placeholder">', \Modules\Sms\Template::$orderIsCreated) !!}</span></b></code>
        </label>
        <div class="col-sm-12">
            <textarea class="form-control sms-template" name="sms[template][order_is_created]" cols="8"
                      rows="2">{{ old('sms.template.order_is_created') ?? $data['settings']['sms']['template']['order_is_created'] ?? config('sms.template.order_is_created') ?? null }}</textarea>
        </div>
        @if($errors->has('sms.template.order_is_created'))
            <label class="error"
                   for="content['sms']['template']['order_is_created']"> {{ $errors->first('sms.template.order_is_created') }}</label>
        @endif
    </div>

    <div class="row form-group">
        <label class="col-sm-12 col-form-label">
            <b>Ordered Received .</b>
            <code> Placeholder available : <b><span
                        class="template-placeholder">{!! implode('</span>,<span class="template-placeholder">', \Modules\Sms\Template::$orderIsReceived) !!}</span></b></code>
        </label>
        <div class="col-sm-12">
            <textarea class="form-control sms-template" name="sms[template][order_is_received]" cols="8"
                      rows="2">{{ old('sms.template.order_is_received') ?? $data['settings']['sms']['template']['order_is_received'] ?? config('sms.template.order_is_received') ?? null }}</textarea>
        </div>
        @if($errors->has('sms.template.order_is_received'))
            <label class="error"
                   for="content['sms']['template']['order_is_received']"> {{ $errors->first('sms.template.order_is_received') }}</label>
        @endif
    </div>

    <div class="row form-group">
        <label class="col-sm-12 col-form-label">
            <b>Ordered Redeemed .</b>
            <code> Placeholder available : <b><span
                        class="template-placeholder">{!! implode('</span>,<span class="template-placeholder">', \Modules\Sms\Template::$orderIsRedeemed) !!}</span></b></code>
        </label>
        <div class="col-sm-12">
            <textarea class="form-control sms-template" name="sms[template][order_is_redeemed]" cols="8"
                      rows="2">{{ old('sms.template.order_is_redeemed') ?? $data['settings']['sms']['template']['order_is_redeemed'] ?? config('sms.template.order_is_redeemed') ?? null }}</textarea>
        </div>
        @if($errors->has('sms.template.order_is_redeemed'))
            <label class="error"
                   for="content['sms']['template']['order_is_redeemed']"> {{ $errors->first('sms.template.order_is_redeemed') }}</label>
        @endif
    </div>
</div>

<div class="hr-line-dashed"></div>
<div class="form-group row">
    <div class="pull-left">
        <a data-toggle="collapse" class="col-sm-10" id="sms-settings" href="#SmsOTPSettings"
           role="button" aria-expanded="false" aria-controls="SmsSettings">
            <b>User Verification Template</b> <i class="fa fa-angle-down rotate-icon"></i>
        </a>
    </div>
</div>
<div class="hr-line-dashed"></div>
<div class="collapse" id="SmsOTPSettings">
    <div class="row form-group">
        <label class="col-sm-12 col-form-label">
            <b>Send Otp Code .</b>
            <code> Placeholder available : <b><span
                        class="template-placeholder">{!! implode('</span>,<span class="template-placeholder">', \Modules\Sms\Template::$sendOtpCode) !!}</span></b></code>
        </label>
        <div class="col-sm-12">
            <textarea class="form-control sms-template" name="sms[template][send_otp_code]" cols="8"
                      rows="2">{{ old('sms.template.send_otp_code') ?? $data['settings']['sms']['template']['send_otp_code'] ?? config('sms.template.send_otp_code') ?? null }}</textarea>
        </div>
        @if($errors->has('sms.template.send_otp_code'))
            <label class="error"
                   for="content['sms']['template']['send_otp_code']"> {{ $errors->first('sms.template.send_otp_code') }}</label>
        @endif
    </div>
    <div class="row form-group">
        <label class="col-sm-12 col-form-label">
            <b>Send Verified sms to phone.</b>
            <code> Placeholder available : <b><span
                        class="template-placeholder">{!! implode('</span>,<span class="template-placeholder">', \Modules\Sms\Template::$phoneIsVerified) !!}</span></b></code>
        </label>
        <div class="col-sm-12">
            <textarea class="form-control sms-template" name="sms[template][phone_is_verified]" cols="8"
                      rows="2">{{ old('sms.template.phone_is_verified') ?? $data['settings']['sms']['template']['phone_is_verified'] ?? config('sms.template.phone_is_verified') ?? null }}</textarea>
        </div>
        @if($errors->has('sms.template.phone_is_verified'))
            <label class="error"
                   for="content['sms']['template']['phone_is_verified']"> {{ $errors->first('sms.template.phone_is_verified') }}</label>
        @endif
    </div>
</div>

