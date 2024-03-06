@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="container">
        <div class="card mb-2">
            <div class="card-header">{{ $questionnaire->title }}</div>
            <div class="card-body">{{ $questionnaire->desc }}</div>
            <div class="card-footer">{{ $questionnaire->created_at->toDateString() }}</div>
        </div>
        <div>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link @if(!request()->filled('active_tab') || (request('active_tab') && !in_array(request('active_tab'),['answers','questions'])) || request('active_tab') == 'questions') active @endif" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                    Questions
                </button>
                <button class="nav-link @if(request('active_tab') == 'answers') active @endif" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                    Answers
                </button>
            </div>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade @if(!request()->filled('active_tab') || (request('active_tab') && !in_array(request('active_tab'),['answers','questions'])) || request('active_tab') == 'questions') show active @endif" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                @foreach($questionnaire->questions as $question)
                    <div class="card mb-3">
                        <div class="card-body">{{ $question->question_text }}</div>
                        <div class="card-footer">{{ $question->answers_count." (".$questionnaire->calculateAnswerPercent($question->answers_count)."%)" }}</div>
                    </div>
                @endforeach
            </div>
            <div class="tab-pane fade @if(request('active_tab') == 'answers') show active @endif" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <td>#</td>
                        <td>name</td>
                        <td>email</td>
                        <td>answer</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($answers as $answer)
                        <tr>
                            <td>{{ $answer->id }}</td>
                            <td>{{ $answer->customer->name }}</td>
                            <td>{{ $answer->customer->email }}</td>
                            <td>{{ $answer->question_option_id ? $answer->option->value : $answer->answer_text }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="mt-3 d-flex justify-content-center">
                    {!! $answers->appends(['active_tab'=>"answers"])->links() !!}
                </div>
            </div>
        </div>
    </div>
    @push('header')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    @endpush
@stop
