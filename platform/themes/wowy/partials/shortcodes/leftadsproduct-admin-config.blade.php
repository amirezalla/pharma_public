
    <div class="form-group">
        <label class="control-label">{{ __('Icon') }}</label>
        {!! Form::mediaImage('icon', Arr::get($attributes, 'icon' )) !!}
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Title') }}</label>
        <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Subtitle') }}</label>
        <input type="text" name="subtitle" value="{{ Arr::get($attributes, 'subtitle') }}" class="form-control"
               placeholder="{{ __('Subtitle') }}">
    </div>


    <div class="form-group">
        <label class="control-label">{{ __('Limit') }}</label>
        <input type="number" name="limit" value="{{ Arr::get($attributes, 'limit') }}" class="form-control" placeholder="{{ __('Limit') }}">
    </div>
    <div class="form-group">
        <label class="control-label">{{ __('Product category ID') }}</label>
        {!! Form::customSelect('category_id', $categories, Arr::get($attributes, 'category_id')) !!}
    </div>

