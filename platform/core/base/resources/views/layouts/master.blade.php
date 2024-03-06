@extends('core/base::layouts.base')

@section ('page')
    @include('core/base::layouts.partials.svg-icon')

    <div class="page-wrapper">

        @include('core/base::layouts.partials.top-header')
        <div class="clearfix"></div>
        <div class="page-container">
            <div class="page-sidebar-wrapper">
                <div class="page-sidebar navbar-collapse collapse">
                    <div class="sidebar">
                        <div class="sidebar-content">
                            <ul class="page-sidebar-menu page-header-fixed {{ session()->get('sidebar-menu-toggle') ? 'page-sidebar-menu-closed' : '' }}" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                                @include('core/base::layouts.partials.sidebar')
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-content-wrapper">
                <div class="page-content @if (Route::currentRouteName() == 'media.index') rv-media-integrate-wrapper @endif" style="min-height: 100vh">
                    {!! Breadcrumbs::render('main', page_title()->getTitle(false)) !!}
                    <div class="clearfix"></div>
                    <div id="main">
                        @yield('content')
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        @include('core/base::layouts.partials.footer')
    </div>
@stop

@section('javascript')
    @include('core/media::partials.media')
@endsection

@push('footer')
    @routes
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>

        $(document).on('click', '.download-log', function (evt) {
        var id =  $(this).closest('tr').data('id');
        axios.post(`/admin/ecommerce/mail-log/download`, {id: id})
            .then((response) => {
                const blob = new Blob([response.data], {type: 'text/html'});
                console.log(response.data, blob)
                // Create a temporary URL for the Blob object
                const url = URL.createObjectURL(blob);
                // Create an anchor element and trigger the download
                const a = document.createElement('a');
                a.href = url;
                a.download = id+'_email.html';
                document.body.appendChild(a);
                a.click();
                // Cleanup: Revoke the temporary URL and remove the anchor element
                URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }).catch(function(error) {
                // Check if the status code is 404 and the message is as expected
                if (error.response && error.response.status === 404 && error.response.data.message === "Offer not found for the given user ID") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message,
                    });
                }
            });

    });



    $(document).on("click",".resetUserPass",function(){
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var emailElement = $(this).closest('tr').children('.column-key-email');

        // Assuming the email is a text inside the emailElement
        var emailValue = emailElement.text().trim();
        console.log(emailValue);
        if (1) {
        axios({
            method: 'post',
            url: "{{ route('customer.password.request') }}",
            data: {
                email:emailValue
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        }).then(function(response){
            Swal.fire({
                title: 'Success!',
                text: "Una reimpostazione della password è stata inviata all'utente previsto",
                icon: 'success'
            });
        })
            .catch(function (error) {
              console.log(error);
              // Handle any errors
            });
        }
    })

    $(document).on("click",".welcomeMail",function(){

        var emailElement = $(this).closest('tr').children('.column-key-email');

        // Assuming the email is a text inside the emailElement
        var emailValue = emailElement.text().trim();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        axios({
            method: 'post',
            url: "/welcome_mail",
            data: {
                email:emailValue
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        }).then(function(response){
            Swal.fire({
                title: 'Success!',
                text: "Il messaggio di benvenuto è stato inviato all'utente.",
                icon: 'success'
            });
        })
        .catch(function (error) {
            console.log(error);
        });


    });
    </script>
@endpush
