@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

@php

    use Botble\Ecommerce\Models\Questionnaire;

    $Qs=Questionnaire::all();

@endphp
<div class="py-3 d-flex">
    <a href="{{ route('questionary.create') }}">Creare</a>
</div>
@if($msg)
<div class="alert alert-success" role="alert">
    Questionnaire added successfully!
</div>
@endif

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>
                Title
            </th>
            <th>
                Operations
            </th>
        </tr>

    </thead>
    <tbody>
        @foreach ($Qs as $Q )
        <tr>
            <td>
                {{ $Q->title }}
            </td>
            <td>
                <a class="btn btn-primary mr-2" href="https://marigolab.it/admin/ecommerce/questionnaire/viewQuestionnaire?id={{ $Q->id }}" class="viewQuestionnaire"><i class="fa fa-eye"></i></a>
                <button class="btn btn-danger" class="removeQuestionnaire" data-value={{ $Q->id }}><i class="fa fa-trash"></i></button>
            </td>
        </tr>




        @endforeach
</tbody>
</table>
@stop
