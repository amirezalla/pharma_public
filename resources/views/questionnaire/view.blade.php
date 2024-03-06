@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

@php

    use Botble\Ecommerce\Models\Questionnaire;
    use Botble\Ecommerce\Models\Question;

    $id=$_GET['id'];
    $Q=Questionnaire::find($id);
    $q=Question::where('questionnaire_id',$Q->id)->get();
@endphp

<h3 class="mb-4">
    {{ $Q->title }}
</h3>
@foreach ($q as $question)

<div class="container" style="background-color:#f5f5f5;">

    <h5 class="mb-3">{{ $question->question_text }}</h5>

</div>

@endforeach



@stop
