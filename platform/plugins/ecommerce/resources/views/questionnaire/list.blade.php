@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="py-3 d-flex justify-content-end">
        <a href="{{ route('admin.ecommerce.questionnaires.create') }}" class="btn btn-primary">Nuovo Questionario</a>
    </div>
    @if(session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>
                Titolo
            </th>
            <th>
                Stato
            </th>
            <th>
                Data di inizio
            </th>
            <th>
                Data di scadenza
            </th>
            <th>
                Azioni
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($questionnaires as $questionnaire)
            <tr>
                <td>
                    {{ $questionnaire->title }}
                </td>
                <td>
                    <span
                        class="text-white py-1 px-2 rounded-1 {{ $questionnaire->is_active ? 'bg-success' : 'bg-danger' }}">{{ $questionnaire->is_active ? 'Attivo' : 'Inattivo' }}</span>
                </td>
                {{--                <td>{{ $questionnaire->start_at }}</td>--}}
                <td>{{ Carbon\Carbon::parse($questionnaire->start_at)->format('d-m-Y') }}</td>
                <td>{{ Carbon\Carbon::parse($questionnaire->end_at)->format('d-m-Y') }}</td>
                {{--                <td>{{ $questionnaire->end_at }}</td>--}}
                <td>
                    {{--                <a class="btn btn-primary mr-2" href="https://dev.marigo.collaudo.biz/admin/ecommerce/questionnaire/viewQuestionnaire?id={{ $Q->id }}" class="viewQuestionnaire"><i class="fa fa-eye"></i></a>--}}
                    <div class="d-flex">
                        <form action="{{ route('admin.ecommerce.questionnaires.delete',$questionnaire->id) }}"
                              method="post" class="js-form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                        @if($questionnaire->is_active)
                            <form action="{{ route('admin.ecommerce.questionnaires.inactive',$questionnaire->id) }}"
                                  method="post" class="js-form-status">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-xmark"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.ecommerce.questionnaires.active',$questionnaire->id) }}"
                                  method="post" class="js-form-status">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i>
                                </button>
                            </form>
                        @endif
                        <a class="btn btn-primary mr-2"
                           href="{{ route('admin.ecommerce.questionnaires.edit',$questionnaire->id) }}"><i
                                class="fa fa-edit"></i></a>
                        <a class="btn btn-dark mr-2"
                           href="{{ route('admin.ecommerce.questionnaires.show',$questionnaire->id) }}"><i
                                class="fa fa-eye text-white"></i></a>
                        <button class="btn btn-success mr-2 btn-open-mail-modal"
                                data-url="{{ route('admin.ecommerce.questionnaires.send-email-to-customers',$questionnaire->id) }}">
                            <i class="fa fa-envelope"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        <div class="">
            {{ $questionnaires->links() }}
        </div>
    </div>
    <div class="modal fade" id="questionnaireModal">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Elenco destinatari</h5>
                    <button type="button" class="close" onclick="closeQuestionnaireModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group d-flex">
                        <label class="block mr-10">
                            <input type="checkbox" name="select_all">
                        </label>
                        <label class="w-100 block">
                            <input type="text" class="search-input form-control"
                                   data-url="{{ route('admin.ecommerce.questionnaires.ajax-users') }}"
                                   placeholder="Search ...">
                        </label>
                    </div>
                    <div class="spinner text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="txt-error text-danger" style="display: none">aaa</div>
                    <button type="button" class="btn btn-primary modal-submit">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    @endpush
    @push('footer')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            let selected_customers = [];
            let expect_customers = [];
            let selected_url = null;
            let search_timer = null;
            let selected_all = false;
            $(document).ready(function () {
                $(document).on('click', '#questionnaireModal .pagination a', function (event) {
                    event.preventDefault();
                    $('li').removeClass('active');
                    $(this).parent('li').addClass('active');
                    getModalData($(this).attr('href'));
                });
                $(document).on('input', '#questionnaireModal .search-input', function (event) {
                    clearTimeout(search_timer);
                    const self = this;
                    let url = $(self).data('url');
                    search_timer = setTimeout(function () {
                        if (self.value && self.value.toString().length) {
                            url = url + "?s=" + self.value;
                        }
                        getModalData(url);
                    }, 500);
                });
                $(document).on('click', '#questionnaireModal .modal-submit', function (event) {
                    if (((selected_customers && selected_customers.length) || selected_all) && selected_url) {
                        $.ajax({
                            url: selected_url,
                            type: "post",
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: selected_customers.length ? selected_customers : null,
                                expect_customers: expect_customers.length ? expect_customers : null,
                                selected_all: selected_all ? 1 : 0
                            },
                            beforeSend: function (data) {
                                $('.txt-error').hide();
                            }
                        }).done(function (data) {
                            closeQuestionnaireModal();
                        }).fail(function (jqXHR, ajaxOptions, thrownError) {
                            const error = jqXHR.responseJSON.errors.ids;
                            if (jqXHR.status === 422 && error && error[0]) {
                                $('.txt-error').show().text(error[0]);
                            }
                        });
                    }
                });
                $(document).on('change', '#questionnaireModal table tr input[type="checkbox"]', function (event) {
                    const val = parseInt(this.value);
                    if(selected_all){
                        const index = expect_customers.indexOf(val);
                        if (index !== -1) {
                            expect_customers.splice(index, 1);
                        }else {
                            expect_customers.push(val);
                        }
                    }
                    if (selected_customers && selected_customers.includes(val)) {
                        const index = selected_customers.indexOf(val);
                        if (index !== -1) {
                            selected_customers.splice(index, 1);
                        }
                    } else {
                        selected_customers.push(parseInt(this.value));
                    }
                });
                $(document).on('change', '#questionnaireModal input[name="select_all"]', function (event) {
                    if (event.target.checked) {
                        selected_all = true;
                    } else {
                        selected_all = false;
                        expect_customers = [];
                    }
                    document.querySelectorAll('#questionnaireModal table tr input[type="checkbox"]').forEach(function (element) {
                        element.checked = event.target.checked;
                    });
                });
                $(document).on('submit', '.js-form-status', function (e) {
                    let form = this;
                    e.preventDefault();
                    Swal.fire({
                        title: 'Sei sicuro di cambiare lo stato di questo questionario?',
                        showCancelButton: true,
                        cancelButtonText: 'cancella',
                        confirmButtonText: 'si',
                        denyButtonText: `No`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
                $(document).on('submit', '.js-form-delete', function (e) {
                    let form = this;
                    e.preventDefault();
                    Swal.fire({
                        title: 'Sei sicuro di eliminare questo questionario?',
                        showCancelButton: true,
                        cancelButtonText: 'cancella',
                        confirmButtonText: 'si',
                        denyButtonText: `No`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
                $(document).on('click', '.btn-open-mail-modal', function (e) {
                    selected_customers = [];
                    $('#questionnaireModal').modal('show');
                    selected_url = $(this).data('url');
                    getModalData('{{ route('admin.ecommerce.questionnaires.ajax-users') }}')
                });

            });

            function closeQuestionnaireModal() {
                $('#questionnaireModal').modal('hide');
                $('.txt-error').hide();
                selected_customers = [];
                selected_url = null;
            }

            function getModalData(url) {
                $.ajax({
                    url: url,
                    type: "get",
                    beforeSend: function (data) {
                        $('#questionnaireModal .content').remove();
                        $('#questionnaireModal .spinner').show();
                    }
                }).done(function (data) {
                    $('#questionnaireModal .spinner').hide();
                    $('#questionnaireModal .modal-body').append(data.table);
                    if ((selected_customers && selected_customers.length) || selected_all) {
                        document.querySelectorAll('#questionnaireModal table tr input[type="checkbox"]').forEach(function (element) {
                            if (selected_customers.includes(parseInt(element.value)) || selected_all) {
                                element.checked = true;
                            }
                        });
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $('#questionnaireModal .spinner').hide();
                });
            }
        </script>
    @endpush
@stop
