<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-20 mb-20">
            <div class="card">
                @if(isset($already_active))
                <div class="card-header">Il tuo account è già attivato.</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    Vai alla pagina di accesso ed effettua il login.
                    <br>
                        <a href="/login" class="btn btn-primary border-0 text-white text-sm">Accedi</a>
                </div>

                @else
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }}.
                        <a href="{{ url()->current().'?email='.$_GET['email'] }}" class="btn btn-primary border-0 text-white text-sm">{{ __('click here to request another') }}</a>
                </div>

                @endif
                

            </div>
        </div>
    </div>
</div>
