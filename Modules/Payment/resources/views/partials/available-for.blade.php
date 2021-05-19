<div class="form-group">
    <label for="roles">Available For <span class="required">*</span></label>

    {!! Form::select('gateway[' . $gateway . '][available_for][]', $data['products'], $data['settings']['gateway'][$gateway]['available_for'] ?? null, [ 'class' => 'form-control select2_available', 'multiple' =>'multiple',]) !!}
    @error("payement.{$gateway}.available_for")
        <label class="has-error" for="roles">{{ $message }}</label>
    @enderror
</div>
