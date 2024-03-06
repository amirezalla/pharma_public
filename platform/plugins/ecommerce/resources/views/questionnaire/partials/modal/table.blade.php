<div class="content">
    <table class="table table">
        <thead>
        <tr>
            <th>#</th>
            <th>codice</th>
            <th>nome</th>
            <th>email</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>
                    <label>
                        <input type="checkbox" name="customer" value="{{ $item->id }}">
                    </label>
                </td>
                <td>{{ $item->codice }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $items->appends(request()->all())->links() }}
    </div>
</div>
