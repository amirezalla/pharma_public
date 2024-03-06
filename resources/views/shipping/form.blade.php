@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

<div class="container mt-5">
    <h2>Calculate Shipping Cost</h2>
    <form action="{{ route('admin.ecommerce.spedizione.calculate.shipping') }}" method="POST">
@csrf

<div class="form-group">
    <label for="region">Customer Region:</label>
    <select class="form-control" id="region" name="region">
        <option value="Campania e Lazio">Campania e Lazio</option>
        <option value="All other regions">All other regions</option>
        <!-- Add more regions if needed -->
    </select>
</div>

<div class="form-group">
    <label for="customer_type">Customer Type:</label>
    <select class="form-control" id="customer_type" name="customer_type">
        <option value="Farmacia, Parafarmacia, Altro Pharma">Farmacia, Parafarmacia, Altro Pharma</option>
        <option value="Studio Medico e Dentista">Studio Medico e Dentista</option>
        <!-- Add more customer types if needed -->
    </select>
</div>

<div class="form-group">
    <label for="order_amount">Order Amount:</label>
    <input type="number" class="form-control" id="order_amount" name="order_amount" placeholder="Enter order amount in euros" required>
</div>

<button type="submit" class="btn btn-primary">Calculate</button>
</form>
</div>

