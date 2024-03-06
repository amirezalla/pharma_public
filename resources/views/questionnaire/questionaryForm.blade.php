@php
    SeoHelper::setTitle(__('Questionnaire'));
    Theme::fireEventGlobalAssets();
@endphp

{!! Theme::partial('header') !!}
<div class="container">
    <main class="main page-404">
        {{--        <form action="{{ route('questionary.saveanswers') }}" method="POST">--}}
        <form action="{{ route('questionary.save-answers') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="form-group ">
                        <div class="w-100">
                            <strong>{{$questonary->title}}</strong>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group ">
                        <label for="title">{!! nl2br(e($questionnaire->desc)) !!}</label>
                    </div>
                </div>

            </div>


            <div class="container">
                <div class="row">
                    @foreach( $questions as $question)
                        <div class="p-3 col-12 col-md-6 col-lg-4">
                            <div class="card p-2 border-primary-blue">
                                <div class="d-block card-header">
                                    <div class="w-100 mb-2">
                                        <strong>{!! nl2br(e($question->question_text)) !!}</strong>
                                    </div>
                                    <input type="hidden" name="answers[{{ $question->id }}][question_id]"
                                           value="{{ $question->id }}">
                                    @if($question->options->count())
                                        @foreach($question->options as $option)
                                            <label class="w-100 d-flex align-items-center gap-2">
                                                <input type="radio" name="answers[{{ $question->id }}][answer_option_id]"
                                                       @if(old('answers.'.$question->id.'.answer_option_id') == $option->id) checked
                                                       @endif value="{{ $option->id }}" id="answer_{{ $question->id }}" class="h-18px w-18px">
                                                <span class="d-flex">{!! nl2br(e($option->value)) !!}</span>
                                            </label>
                                        @endforeach
                                    @else
                                        <input type="text" name="answers[{{ $loop->iteration }}][answer_text]"
                                               id="answer_{{ $question->id }}"
                                               value="{{ old('answers.'.$loop->iteration.'.answer_text') }}"
                                               class="form-control" required>
                                    @endif

                                </div>

                            </div>
                        </div>


                    @endforeach
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-success mb-3">Conferma</button>
        </form>
    </main>
</div>


{!! Theme::partial('footer') !!}

