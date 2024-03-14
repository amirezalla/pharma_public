@for ($i = 1; $i < 20; $i++)
    <div class="form-group">
        <label class="control-label">{{ __('Title') }}</label>
        <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control"
            placeholder="Title">
    </div>
    <div class="form-group">
        <label class="control-label">{{ __('Foto :number', ['number' => $i]) }}</label>
        {!! Form::mediaImage('foto' . $i, Arr::get($attributes, 'foto' . $i)) !!}
    </div>
    <div class="form-group">
        <label class="control-label">{{ __('Catalog :number', ['number' => $i]) }}</label>
        {!! Form::mediaFile('catalog' . $i, Arr::get($attributes, 'catalog' . $i)) !!}
    </div>
@endfor
