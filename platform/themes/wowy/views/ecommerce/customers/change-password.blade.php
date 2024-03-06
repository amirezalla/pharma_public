@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Change password') }}</h5>
        </div>
        <div class="card-body">
            {!! Form::open(['route' => 'customer.post.change-password', 'method' => 'post']) !!}
                <div class="form-group @if ($errors->has('old_password')) has-error @endif" style='position:relative'>
                    <label class="required" for="old_password">{{ __('Current password') }}:</label>
                    <input required type="password" class="form-control square" name="old_password" id="old_password"
                        placeholder="{{ __('Current password') }}">
                        <i id="toggle-old-password" class="fas fa-eye" style="position: absolute;top:45px;right:15px;cursor: pointer;"></i>

                    {!! Form::error('old_password', $errors) !!}
                </div>
                <div class="form-group @if ($errors->has('password')) has-error @endif" style='position:relative'>
                    <label class="required" for="password">{{ __('New password') }}:</label>
                    <input required type="password" class="form-control square" name="password" id="password"
                        placeholder="{{ __('New password') }}">
                        <i id="toggle-password" class="fas fa-eye" style="position: absolute;top:45px;right:15px;cursor: pointer;"></i>

                    {!! Form::error('password', $errors) !!}
                </div>
                <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif" style='position:relative'>
                    <label class="required" for="password_confirmation">{{ __('Password confirmation') }}:</label>
                    <input required type="password" class="form-control square" name="password_confirmation" id="password_confirmation"
                        placeholder="{{ __('Password confirmation') }}">
                        <i id="toggle-password-confirmation" class="fas fa-eye" style="position: absolute;top:45px;right:15px;cursor: pointer;"></i>
                    {!! Form::error('password_confirmation', $errors) !!}
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-fill-out submit">{{ __('Change password') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
