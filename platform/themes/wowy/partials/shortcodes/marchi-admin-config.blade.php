@for ($i = 1; $i < 20; $i++)
    <div class="mt-5 mb-5" style="padding: 10px;background-color:rgba(201, 201, 201, 0.629);">
        <div class="form-group">
            <label class="control-label">{{ __('Titolo :number', ['number' => $i]) }}</label>
            <input type="text" name="title{{ $i }}" value="{{ Arr::get($attributes, 'title' . $i) }}"
                class="form-control" placeholder="Title">
        </div>
        <div class="form-group">
            <label class="control-label">{{ __('Foto :number', ['number' => $i]) }}</label>
            {!! Form::mediaImage('foto' . $i, Arr::get($attributes, 'foto' . $i)) !!}
        </div>
        <div class="form-group">
            <label class="control-label">{{ __('Catalog :number', ['number' => $i]) }}</label>
            {!! Form::mediaFile('catalog' . $i, Arr::get($attributes, 'catalog' . $i)) !!}
        </div>
    </div>
@endfor
