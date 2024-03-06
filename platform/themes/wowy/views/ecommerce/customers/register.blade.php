@php
    Theme::layout('full-width');
@endphp
@php

    use Illuminate\Support\Facades\Crypt;

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
    session(['register_form_captcha_answer' => $encryptedAnswer]);

@endphp
<section class="pt-100 pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 m-auto">

                <div class=" login_wrap widget-taber-content p-30 background-white border-radius-10">
                    <div class="padding_eight_all bg-white">
                        <div class="heading_s1 mb-20">
                            <h3 class="mb-20">{{ __('Register') }}</h3>
                            <p>{{ __('Please fill in the information below') }}</p>
                        </div>

                        <form class="form--auth" id='registration-form' method="POST"
                            action="{{ route('customer.register.post') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="hidden" class="form-control" id="customer_id" name="customer_id"
                                        required>
                                    <input type="hidden" id="is_default" name="is_default" value="Yes" required>
                                    <input type="hidden" class="form-control" id="country" name="country"
                                        value="IT">

                                    <div class="form-group">
                                        <label for="txt-name" class="required">{{ __('Name') }}</label>
                                        <input class="form-control" name="name" id="txt-name" type="text"
                                            value="{{ old('name') }}" placeholder="Inserisci il tuo nome">
                                        @if ($errors->has('name'))
                                            <span class="text-danger">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="txt-email" class="required">{{ __('Email Address') }}</label>
                                        <input class="form-control email-controll-registration" name="email"
                                            id="txt-email" type="email" value="{{ old('email') }}"
                                            placeholder="Inserisci la tua email ">
                                        <span id='realtime-email-error' class="invalid-feedback"></span>
                                        @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group" style="position: relative">
                                        <label for="txt-password" class="required">Password</label>
                                        <input class="form-control" type="password" name="password" id="txt-password"
                                            placeholder="{{ __('Please enter your password') }}">
                                        <i id="toggle-password" class="fas fa-eye"
                                            style="position: absolute;top:45px;right:15px;cursor: pointer;"></i>
                                        @if ($errors->has('password'))
                                            <span
                                                class="text-danger password-error">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group" style="position: relative">
                                        <label for="txt-password-confirmation"
                                            class="required">{{ __('Password confirmation') }}</label>
                                        <input class="form-control" type="password" name="password_confirmation"
                                            id="txt-password-confirmation"
                                            placeholder="{{ __('Please enter your password confirmation') }}">
                                        <i id="toggle-password-confirmation" class="fas fa-eye"
                                            style="position: absolute;top:45px;right:15px;cursor: pointer;"></i>
                                        @if ($errors->has('password_confirmation'))
                                            <span
                                                class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                        @endif
                                    </div>
                                    @if (is_plugin_active('captcha') &&
                                            setting('enable_captcha') &&
                                            get_ecommerce_setting('enable_recaptcha_in_register_page', 0))
                                        <div class="form-group">
                                            {!! Captcha::display() !!}
                                        </div>
                                    @endif
                                    <!-- Is Default -->

                                    {{--            <div class="form-group"> --}}
                                    {{--                <label for="is_default">Is Default?</label> --}}
                                    {{--                <select class="form-control" id="is_default" name="is_default" type="hid"> --}}
                                    {{--                    <option value="0">No</option> --}}
                                    {{--                    <option value="1">Yes</option> --}}
                                    {{--                </select> --}}
                                    {{--            </div> --}}
                                    <div class="form-group">
                                        <label for="txt-name" class="required">Tipo di cliente</label>
                                        <select class="form-control" name="type" required>
                                            <option value="Farmacia">Farmacia</option>
                                            <option value="Farmacia">Parafarmacia</option>
                                            <option value="Farmacia">Dentista</option>
                                            <option value="Farmacia">Studio Medico</option>
                                            <option value="Farmacia">Altro Pharma</option>


                                        </select>
                                        @if ($errors->has('tipodicliente'))
                                            <span class="text-danger">{{ $errors->first('tipodicliente') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <div class="ps-checkbox">

                                        </div>
                                        @if ($errors->has('agree_terms_and_policy'))
                                            <span
                                                class="text-danger">{{ $errors->first('agree_terms_and_policy') }}</span>
                                        @endif
                                    </div>

                                    <div class="login_footer form-group">
                                        <div class="chek-form">
                                            <div class="custome-checkbox">
                                                <input type="hidden" name="agree_terms_and_policy" value="0">
                                                <input class="form-check-input" type="checkbox"
                                                    name="agree_terms_and_policy" id="agree-terms-and-policy"
                                                    value="1" @if (old('agree_terms_and_policy') == 1) checked @endif>
                                                <label class="form-check-label"
                                                    for="agree-terms-and-policy"><span>Accetto la <a
                                                            href="/cookie-policy-1">Politica sulla Privacy.</a><span
                                                            style="color:red;">*</span></span></label>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                                <div class="col-lg-6">

                                    <!-- Phone -->
                                    <div class="form-group">
                                        <label for="phone" class="required">Telefono</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            placeholder="Inserisci il tuo numero di telefono">
                                    </div>



                                    <!-- State -->
                                    <div class="form-group">
                                        <label for="state" class="required">Regione</label>
                                        {{--            <input type="text" class="form-control" id="state" name="state"> --}}
                                        <select id="state" name="state" class="form-control">
                                            <option value="abruzzo">Abruzzo</option>
                                            <option value="basilicata">Basilicata</option>
                                            <option value="calabria">Calabria</option>
                                            <option value="campania">Campania</option>
                                            <option value="emiliaRomagna">Emilia-Romagna</option>
                                            <option value="friuliVeneziaGiulia">Friuli Venezia Giulia</option>
                                            <option value="lazio">Lazio</option>
                                            <option value="liguria">Liguria</option>
                                            <option value="lombardia">Lombardia</option>
                                            <option value="marche">Marche</option>
                                            <option value="molise">Molise</option>
                                            <option value="piemonte">Piemonte</option>
                                            <option value="puglia">Puglia</option>
                                            <option value="sardegna">Sardegna</option>
                                            <option value="sicilia">Sicilia</option>
                                            <option value="toscana">Toscana</option>
                                            <option value="trentinoAltoAdige">Trentino-Alto Adige</option>
                                            <option value="umbria">Umbria</option>
                                            <option value="valleDAosta">Valle d'Aosta</option>
                                            <option value="veneto">Veneto</option>
                                        </select>

                                    </div>

                                    <!-- City -->
                                    <div class="form-group">
                                        <label for="city" class="required">Città</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            placeholder="Inserisci la tua Città">
                                    </div>

                                    <!-- Address -->
                                    <div class="form-group">
                                        <label for="address" class="required">Indirizzo</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            placeholder="Inserisci il tuo indirizzo" required>
                                    </div>
                                    <!-- Zip Code -->
                                    <div class="form-group">
                                        <label for="zip_code" class="required">CAP</label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code"
                                            placeholder="Inserisci il tuo CAP">
                                    </div>

                                    <div class="form-group">
                                        <label for="txt-password" class="required">Somma</label>
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
                                                    <input type="text" id="captcha-register"
                                                        placeholder="Scrivi il risultato della somma a fianco">
                                                </div>
                                                <span class="text-danger captcha-error"
                                                    style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2">
                                            <button type="submit"
                                                class="register--btn--submit btn btn-fill-out btn-block hover-up">{{ __('Register') }}</button>
                                        </div>

                                        <br>
                                        <p>{{ __('Have an account already?') }} <a
                                                href="{{ route('customer.login') }}"
                                                class="d-inline-block">{{ __('Login') }}</a></p>

                                        <div class="text-left">
                                            {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
                                        </div>
                                    </div>
                                    {{--        <div class="form-group"> --}}
                                    {{--            <label for="txt-password" class="required">{{ __('Somma') }}</label> --}}
                                    {{--            <div class="row"> --}}
                                    {{--                <div class="col-12 captcha"> --}}
                                    {{--                    <div class="captcha-value"> --}}
                                    {{--                        <img src="{{ $dataUri }}"/> --}}
                                    {{--                    </div> --}}
                                    {{--                    <div class="form__password"> --}}
                                    {{--                        <input type="text" name='captcha' id="captcha" placeholder="Scrivi il risultato della somma a fianco"> --}}
                                    {{--                    </div> --}}
                                    {{--                    <span class="text-danger captcha-error"></span> --}}

                                    {{--                </div> --}}
                                    {{--            </div> --}}
                                    {{--        </div> --}}
                                </div>





                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
