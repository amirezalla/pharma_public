@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mt-5">
            <h2 class="d-inline">Elenco delle email inviate</h2>
            <form method="GET" action="{{ route('admin.ecommerce.mail-log.filter') }}" class="mt-2 mb-2">
                <div class="form-row row">
                    
                    <div class="form-group col-md-4">
                        <label for="from_date">Cerca:</label>
                        <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="Search for Nome,codice cliente, oggetto...">
                    </div>
                <div class="form-group col-md-3">
                    <label for="from_date">Da:</label>
                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ request('from_date') }}">
                </div>
        
                <!-- To Date Input -->
                <div class="form-group col-md-3">
                    <label for="to_date">A:</label>
                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ request('to_date') }}">
                </div>
                
                <!-- Filter Button -->
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>

            </form>
            <table class="mt-3 table table-striped table-hover">
                <thead>
                <tr>
                    <th>Codice cliente</th>
                    <th>Nome cliente</th>
                    <th>Email destinatario</th>
                    <th>Oggetto</th>
                    <th>Data invio</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $log)
                    <tr data-id="{{ $log->id }}" class="logte-row">
                        <td>{{ $log->codice_cliente }}</td>
                        <td>{{ $log->nome_cliente }}</td>
                        <td class="log-piva">{{$log->email_destinatario }}</td>
                        <td>{{ $log->oggetto }}</td>
                        @php
                        $timestamp = strtotime($log->data_invio);
                        @endphp
                        <td>{{date("d/m/Y H:i", $timestamp )}}</td>
                        <td>
                            {{-- <button class="delete-log btn btn-danger" onclick="deletelog({{ $log->id }})"><i class="fa fa-trash"></i></button> --}}
                            <button class="download-log btn btn-primary" onclick="downloadLog({{ $log->id }})"><i class="fa fa-download"></i></button>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $list->appends(request()->all())->links() }}

        </div>
    </div>
</div>
@stop
