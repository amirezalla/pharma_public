@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="container">
        <div class="questionnaire_section">
            @if(session()->has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('admin.ecommerce.questionnaires.store') }}" method="post" class="w-100 js-form">
                @csrf
                <div class="mb-3">
                    <label for="questionnaire_title">Titolo</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}"
                           id="questionnaire_title">
                    @error('title')
                    <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="questionnaire_desc">Descrizione</label>
                    <textarea name="desc" class="form-control" id="questionnaire_desc" cols="30"
                              rows="10">{{ old('desc') }}</textarea>
                    @error('desc')
                    <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row mb-2">
                    <div class="col-12 col-md-6 mb-2">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <label for="start_at">DATA DI INIZIO</label>
                            <input type="date" name="start_at" value="{{ old('start_at') }}" id="start_at">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-2">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <label for="end_at">DATA DI SCADENZA</label>
                            <input type="date" name="end_at" value="{{ old('end_at') }}" id="end_at">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="py-2 border-bottom">Domande</div>
                    <div class="questions js-questions d-flex flex-column">
                        @if(old('questions') && count(old('questions')))
                            @foreach(old('questions') as $i => $question)
                                <div data-id="{{ $i }}" class="question-item px-2 py-2 rounded-3 border mb-3">
                                    <label class="d-block w-100">
                                        <textarea class="form-control"
                                                  name="questions[{{ $i }}][value]">{{ $question['value'] }}</textarea>
                                        @error('questions.'.$i)
                                        <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </label>
                                    <div class="mt-1 d-flex justify-content-end">
                                        <button type="button" class="btn btn-danger js-btn-remove-question">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="d-flex margin-right-10">Aggiungi risposta predefinita</div>
                                            <button type="button" class="btn btn-success js-btn-add-question-option">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="js-question-options">
                                            @if(array_key_exists('option',$question) && is_array($question['option']) && count($question['option']))
                                                @foreach($question['option'] as $key_option=>$option)
                                                    <div class="js-question-option pb-2">
                                                        <label class="d-block w-100">
                                                    <textarea class="form-control"
                                                              name="questions[{{ $i }}][option][{{ $key_option }}]">{{ $option }}</textarea>
                                                            @error('questions.'.$i.'.option.'.$key_option)
                                                            <div class="text-danger text-small">{{ $message }}</div>
                                                            @enderror
                                                        </label>
                                                        <div class="mt-1 d-flex justify-content-end">
                                                            <button type="button"
                                                                    class="btn btn-danger js-btn-remove-question-option">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="button" class="btn btn-success js-btn-add-question">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary col-12">
                    Salva
                </button>
            </form>
        </div>

    </div>
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    @endpush
    @push('footer')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function () {
                $(document).on('submit', '.js-form', function (e) {
                    let form = this;
                    e.preventDefault();
                    const start_at = $('#start_at').val();
                    const end_at = $('#end_at').val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: "{{ route('admin.ecommerce.questionnaires.check-active-changes') }}",
                        data: {
                            start_at: start_at,
                            end_at: end_at,
                        },
                        type: 'POST',
                        dataType: 'json',
                        error: function (xhr, status, error) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                var html = '';
                                if (errors) {
                                    Object.keys(errors).forEach(function (key) {
                                        html = html + '<div>' + errors[key][0] + '</div>';
                                    });
                                    Swal.fire({
                                        icon: 'error',
                                        html: html
                                    });
                                }
                            }
                        },
                        success: function (response) {
                            console.log(response)
                            if (response.questionnaire.activeQuestionnaireDates && response.questionnaire.activeQuestionnaireDates.end_at && response.questionnaire.activeQuestionnaireDates.start_at) {
                                Swal.fire({
                                    html:
                                        '<div>ATTENZIONE! abbiamo rilevato un questionario attivo oppure previsto per intervallo di tempo selezionato: </div>' +
                                        '<div>*' + response.questionnaire.el.title + ' </div>' +
                                        '<div>Puoi cambiare le date del questionario</div>' +

                                        '<div>*' + response.questionnaire.el.title + ' </div>' +

                                        '<div> premendo CONFERMA oppure ritornare all editing di questo questionario premendo CANCELLA </div>' +
                                        '<div> Se confermi questa programmazione, le nuove date del precedente questionario saranno cambiate </div>' +
                                        '<div>Inizio: ' + response.questionnaire.activeQuestionnaireDates.start_at + '</div>' +
                                        '<div>Scadenza: ' + response.questionnaire.activeQuestionnaireDates.end_at + '</div>',
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    confirmButtonText: 'CONFERMA',
                                    cancelButtonText: 'CANCELLA',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            } else {
                                form.submit();
                            }
                        }
                    });
                });
                $(document).on('click', '.js-btn-add-question', function () {
                    const questions = $('.js-questions');
                    const i = Math.abs(questions.find('.question-item').length + 1) * -1;
                    questions.append('<div data-id="' + i + '" class="question-item px-2 py-2 rounded-3 border mb-3">' +
                        '<label class="d-block w-100">' +
                        '<textarea class="form-control" name="questions[' + i + '][value]"></textarea>' +
                        '</label>' +
                        '<div class="mt-1 d-flex justify-content-end">' +
                        '<button type="button" class="btn btn-danger js-btn-remove-question">' +
                        '<i class="fa fa-minus"></i>' +
                        '</button>' +
                        '</div>' +
                        '<div class="mt-3">' +
                        '<div class="d-flex align-items-center mb-2">' +
                        '<div class="d-flex margin-right-10">Aggiungi risposta predefinita</div>' +
                        '<button type="button" class="btn btn-success js-btn-add-question-option">' +
                        '<i class="fa fa-plus"></i>' +
                        '</button>' +
                        '</div>' +
                        '<div class="js-question-options"></div>' +
                        '</div>' +
                        '</div>');
                });
                $(document).on('click', '.js-btn-add-question-option', function () {
                    const questionOptions = $(this).closest('.question-item').find('.js-question-options');
                    const id = $(this).closest('.question-item').data('id');
                    const i = Math.abs(questionOptions.find('.js-question-option').length + 1) * -1;
                    questionOptions.append('<div class="js-question-option pb-2">' +
                        '<label class="d-block w-100">' +
                        '<textarea class="form-control" name="questions[' + id + '][option][' + i + ']"></textarea>' +
                        '</label>' +
                        '<div class="mt-1 d-flex justify-content-end">' +
                        '<button type="button" class="btn btn-danger js-btn-remove-question-option">' +
                        '<i class="fa fa-minus"></i>' +
                        '</button>' +
                        '</div>' +
                        '</div>');
                });
                $(document).on('click', '.js-btn-remove-question', function (e) {
                    $(this).closest('.question-item').remove();
                });
                $(document).on('click', '.js-btn-remove-question-option', function (e) {
                    $(this).closest('.js-question-option').remove();
                });
            });
        </script>
    @endpush
@stop
