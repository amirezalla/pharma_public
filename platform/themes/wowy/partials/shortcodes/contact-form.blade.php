<style>
    .input-style {
        position: relative;
    }

    .input-wrapper input:required::after {
        content: "*";
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: red;
        font-weight: bold;
    }
</style>
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
    session(['contact_form_captcha_answer' => $encryptedAnswer]);

@endphp
<section class="mt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-10 m-auto">
                <div class="contact-from-area  padding-20-row-col wow tmFadeInUp animated" style="visibility: visible;">
                    <h3 style="color: #005BA1" class="mb-10 text-center">{{ __('Modulo di richiesta informazioni') }}</h3>
                    <p class="text-muted text-center font-sm">
                        {{ __('Per chiedere maggiori delucidazioni sui nostri servizi, strumentazioni, reagenti, caratteristiche specifiche o quant`altro, puoi inviarci una richiesta utilizzando il modulo sottostante.') }}
                    </p>
                    <p style="color:#005BA1" class="font-sm mb-30 text-center"> <b> Un nostro responsabile ti
                            ricontatterà il prima possibile. </b>
                    </p>
                    <div class="contact-message contact-success-message mt-30 alert alert-success"
                        style="display: none"></div>
                    {!! Form::open([
                        'route' => 'public.send.contact',
                        'class' => 'contact-form-style text-center contact-form',
                        'method' => 'POST',
                    ]) !!}
                    {!! apply_filters('pre_contact_form', null) !!}
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="input-style mb-20">
                                <input required name="name" value="{{ old('name') }}"
                                    placeholder="{{ __('Ragione Sociale *') }}" type="text">
                                <div class="field-error-message" id="nameError"
                                    style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="input-style mb-20">
                                <input required type="email" name="email" value="{{ old('email') }}"
                                    placeholder="{{ __('Email *') }}">
                                <div class="field-error-message" id="emailError"
                                    style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></div>

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="input-style mb-20">
                                <input name="address" value="{{ old('address') }}" placeholder="{{ __('Indirizzo') }}"
                                    type="text">
                                <div class="field-error-message" id="addressError"
                                    style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="input-style mb-20">
                                <input name="phone" value="{{ old('phone') }}" placeholder="{{ __('Telefono') }}"
                                    type="tel">
                                <div class="field-error-message" id="phoneError"
                                    style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></div>

                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="input-style mb-20">
                                <select id="subject"
                                    style="background: var(--color-grey-9);
                                    border: 2px solid var(--color-grey-9);
                                    border-radius: 50px;
                                    box-shadow: none;
                                    color: var(--color-body);
                                    font-size: 13px;
                                    height: 45px;
                                    padding-left: 20px;
                                    width: 100%;"
                                    name="subject">
                                    <option value="0">Scegli un argomento *</option>
                                    <option value="informazioni_generiche">Informazioni generiche</option>
                                    <option value="commerciale">Commerciale</option>
                                    <option value="assistenza_tecnica">Assistenza Tecnica</option>
                                    <option value="lavora_con_noi">Lavora con noi</option>
                                </select>
                            </div>
                            <div class="field-error-message" id="subjectError"
                                style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></div>

                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="textarea-style">
                                <textarea required name="content" placeholder="{{ __('Messaggio *') }}">{{ old('content') }}</textarea>
                                <div class="field-error-message" id="contentError"
                                    style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></div>

                            </div>

                            <div class="form-group mt-10">
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
                                            <img src="{{ $dataUri }}" />
                                        </div>
                                        <div class="form__password">
                                            <input name='captcha' type="text" id="captcha-contact"
                                                placeholder="Scrivi il risultato della somma a fianco">
                                        </div>
                                        <span class="text-danger captcha-error"
                                            style="color:red;font-size:0.8em;text-align:left;margin-left:20px"></span>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 text-left">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="privacyPolicy">
                                    <label class="form-check-label" for="privacyPolicy">Accetto la <a
                                            href="/cookie-policy" target="_blank">Politica sulla Privacy</a>. <span
                                            style="color:red"> *</span></label>
                                </div>
                                <small id="errorMessage" class="form-text text-danger"
                                    style="display: none;font-size:small">Devi accettare la politica sulla privacy per
                                    procedere.</small>
                            </div>

                            {!! apply_filters('after_contact_form', null) !!}
                            <button class="submit submit-auto-width mt-30 submit-contact-form" id="contact-form-btn"
                                type="submit">{{ __('Invia messaggio') }}</button>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <div class="contact-message contact-error-message mt-30" style="display: none"></div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
