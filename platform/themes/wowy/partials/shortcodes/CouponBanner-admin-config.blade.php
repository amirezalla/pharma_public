<div class="form-group">
    <label class="control-label">{{ __('Text') }}</label>
    <textarea type="text" name="text" value="{{ Arr::get($attributes, 'text') }}" class="form-control" placeholder="text"></textarea>
    <label class="control-label">{{ __('Coupon') }}</label>
    <input type="text" name="coupon" value="{{ Arr::get($attributes, 'coupon') }}" class="form-control" placeholder="Coupon">
</div>