@php
    use Illuminate\Support\Facades\Crypt;

    Theme::layout('full-width');
    $number1 = mt_rand(1, 9);
    $number2 = mt_rand(1, 9);
    $image = imagecreatetruecolor(60, 30);
    $background = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagefill($image, 0, 0, $background);
    imagestring($image, 5, 5, 5, "$number1 + $number2", $textColor);
    ob_start();
    imagepng($image);
    $contents = ob_get_contents();
    ob_end_clean();
    $dataUri = 'data:image/png;base64,' . base64_encode($contents);
    imagedestroy($image);
    $encryptedAnswer = Crypt::encryptString($number1 + $number2);
    session(['login_form_captcha_answer' => $encryptedAnswer]);
@endphp



<section class="pt-100 pb-100" style="font-size: 120%">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 m-auto">
                @if (isset($_GET['verify_message']))
                    @if ($_GET['verify_message'] == 'true')
                        <div class="alert alert-info">
                            La tua verifica è già stata completata. Devi attendere alcune ore perché l'amministratore
                            approva la tua richiesta di registrazione!
                        </div>
                    @elseif($_GET['verify_message'] == 'neutral')
                        <div class="alert alert-success">
                            La tua verifica è stata completata. Devi attendere alcune ore perché l'amministratore
                            approva la tua richiesta di registrazione!
                        </div>
                    @endif
                @endif
                <div class="login_wrap widget-taber-content p-30 background-white border-radius-10">
                    <div class="padding_eight_all bg-white">
                        <div class="heading_s1 mb-20">
                            <h3 class="mb-20">{{ __('Login') }}</h3>
                            {{--                            <p>{{ __('Please enter your email address and password') }}</p> --}}
                        </div>
                        <form class="form--auth form--login" method="POST" action="{{ route('customer.login.post') }}">
                            @csrf
                            @if (isset($errors) && $errors->has('confirmation'))
                                <div class="alert alert-danger">
                                    <span>{!! $errors->first('confirmation') !!}</span>
                                </div>
                                <br>
                            @endif
                            <div class="form__content">
                                <div class="form-group">
                                    <label for="txt-email"
                                        class="required">{{ __('La tua email o il tuo codice cliente') }}</label>
                                    <input name="email" id="txt-email" type="email" value="{{ old('email') }}"
                                        placeholder="Inserisci la tua email o il tuo codice cliente">
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txt-password1" class="required">Password</label>
                                    <div class="form__password" style="position: relative">
                                        <input type="password" name="password" id="txt-password1"
                                            placeholder="Inserisci la tua password">
                                        <i id="toggle-password" class="fas fa-eye"
                                            style="position: absolute;top:15px;right:15px;cursor: pointer;"></i>
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txt-password" class="required">{{ __('Somma') }}</label>
                                    <div class="row">
                                        <div class="col-12 captcha" style="position: relative">
                                            <div class="captcha-value"
                                                style="position: absolute;
                                            top: 1%;
                                            right: 2%;
                                            background: white;
                                            padding: 11px 20px;
                                            font-weight: bold;
                                            border-radius: 42px;
                                            font-size: 13pt;">
                                                <img src={{ $dataUri }}>
                                            </div>
                                            <div class="form__password">
                                                <input type="text" id="captcha-login"
                                                    placeholder="Scrivi il risultato della somma a fianco">
                                            </div>
                                            <span class="text-danger captcha-error"
                                                style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="login_footer form-group">
                                    <div class="chek-form">
                                        <div class="custome-checkbox">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember-me-checkbox">
                                            <label class="form-check-label"
                                                for="remember-me-checkbox"><span>{{ __('Remember me') }}</span></label>
                                        </div>
                                    </div>
                                    <a class="text-muted" href="{{ route('customer.password.reset') }}"
                                        style="color: #005BA1 !important">{{ __('Forgot password?') }}</a>
                                </div>
                            </div>
                            <div class="login_footer form-group">
                                <div class="chek-form">
                                    <a class="text-muted" href="{{ route('customer.register') }}"
                                        style="color: #005BA1 !important">Non hai ancora ricevuto la mail con le
                                        credenziali di accesso? REGISTRATI!</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit"
                                    class="form--auth--btn btn btn-fill-out btn-block hover-up">{{ __('Login') }}</button>
                            </div>
                            <br>
                            {{--                            <p>{{ __("Don't have an account?") }} <a href="{{ route('customer.register') }}" class="d-inline-block">{{ __('Create one') }}</a></p> --}}

                            <div class="text-left">
                                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
