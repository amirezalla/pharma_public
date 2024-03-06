@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

    <style>
        .panel {
            position: absolute;
            background-color: white;
            padding: 15px;
            align-content: space-between;
            display: inline-block;
            z-index: 99999;
            min-width: 300px;
            transform: translateX(10px);
            top: 35px;
            border: none;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px !important
        }

        .list-search-data .clearfix .row {
            padding: 15px;
            cursor: pointer;
        }
    </style>

    <div class="row" style='display:flex;align-items:self-end;padding:8px;'>
        <div class="col-12">
            <label for="scontoPercentuale">Codice Coupon</label>
            <input type="text" name="couponcode" class="form-control" id="couponcode" placeholder="Codice Coupon"
                oninput="addSPPrefix(this)">
        </div>
        <div class="row mt-10" style='display:flex;align-items:self-end;padding:8px;'>
            <div class="col-6">
                <h5>Selezionare il tipo di coupon </h5>
                <select id="couponType" class="form-select">
                    <option value="1">Percentuale</option>
                    <option value="2">Amount</option>
                    <option value="3">Gratuito</option>
                </select>
            </div>
            <div class="col-6">
                <div id="amountInput" class="input-group">
                    <input type="text" name='amount' class="form-control" placeholder="Percentage" />
                </div>
            </div>
        </div>
        <div class="row" style='display:flex;align-items:self-end;padding:8px;'>
            <div class="col-md-12">
                <h5>Cliente</h5>
                <div class="div-select-user" style="position: relative">
                    <div class="box-search-advance product" style="min-width:310px;">
                        <input type="text"
                            class="form-control next-input textbox-advancesearch product user-search-realtime"
                            placeholder="Cerca cliente" aria-invalid="false">
                    </div>
                    <div class="panel panel-default d-none">
                        <div class="panel-body">
                            <div class="list-search-data">
                                <ul class="clearfix">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover table-bordered">
                    <tbody class='users-row'>

                    </tbody>
                </table>
                <hr>
            </div>
        </div>
        <div class="row" style='display:flex;align-items:self-end;padding:8px;'>
            <div class="col-md-6 mt-3">
                <h5>Min order</h5>
                <input type="number" class="form-control" id="min_price" min="0" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6 mt-3">
                <h5>Max Order</h5>
                <input class="form-control" type="number" id="max_price">
            </div>
        </div>
        <div class="row" style='display:flex;align-items:self-end;padding:8px;'>
            <div class="col-md-6 mt-3">
                <h5> Data d{{ "'" }}inizio</h5>
                <input class="form-control" type="date" id="start_date" min="<?= date('Y-m-d') ?>"
                    value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6 mt-3">
                <h5>Data di scadenza</h5>
                <input class="form-control" type="date" id="expiring_date" min="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class='row'>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="once">
                <label class="form-check-label" for="once">
                    this coupon is only once per user
                </label>
            </div>
        </div>
        <div class='row'>

            <button class='btn btn-primary col-12' id='submitBtn'>Submit</button>

        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('keyup', '.user-search-realtime', function() {
            const keyword = $(this).val()
            console.log(keyword);

            axios
                .get("https://marigopharma.marigo.collaudo.biz/admin/customers/get-list-customers-for-search", {
                    params: {
                        keyword: $(this).val(),
                    }
                })
                .then((response) => {
                    $(".div-select-user .panel").removeClass('d-none');
                    $(".div-select-user .panel").removeClass('hidden');
                    const cliente = response.data.data.data;
                    $('.div-select-user .panel .panel-body .list-search-data .clearfix').html('');
                    cliente.forEach(element => {
                        $('.div-select-user .panel .panel-body .list-search-data .clearfix').append(
                            "<li class='row user-select-btn' data-id='" + element.id +
                            "' data-codice='" + element.codice + "'>" + element.name + "<br>");
                    });
                    console.log(response.data);
                })
                .catch((err) => console.log(err));

        });



        $(document).on("click", ".user-select-btn", function() {
            var id = $(this).attr('data-id');
            var codice = $(this).attr('data-codice');
            var name = $(this).text();
            $('.users-row').append(`
    <tr id=${id} data-user-id=${id}>
        <td>${codice}</td>
        <td>${name}</td>
        <td><button class='btn btn-danger deleteUser_table' data-id='${id}'><i class='fa fa-trash'></i></button></td>
    </tr>
    `);
        });

        $('.user-search-realtime').on('focus', function() {
            $('.div-select-user .panel').removeClass('hidden');
        });


        $('body').click(function(evt) {
            let container = $('.div-select-user');
            if (evt.target.className == "user-search-realtime")
                container.find('.panel').removeClass('hidden');
            if ($(evt.target).closest('.user-search-realtime').length)
                return;
            container.find('.panel').addClass('hidden');

        });


        $(document).on('click', '.deleteUser_table', function() {
            var id = $(this).attr('data-id');
            $("tr#" + id).remove();
        });
    </script>
    <script>
        function addSPPrefix(inputElement) {
            const userInput = inputElement.value;
            const prefix = "SP-";
            if (!userInput.startsWith(prefix)) {
                inputElement.value = prefix + userInput;
            }
        }

        document.getElementById('couponType').addEventListener('change', function() {
            const selectedValue = this.value;
            let inputElement = document.querySelector('input[name="amount"]');

            switch (selectedValue) {
                case '1':
                    inputElement.placeholder = 'Enter Percentage';
                    inputElement.disabled = false;
                    break;
                case '2':
                    inputElement.placeholder = 'Enter Amount';
                    inputElement.disabled = false;
                    break;
                case '3':
                    inputElement.disabled = true;
                    break;
                default:
                    // Handle default case if needed
                    break;
            }
        });

        document.getElementById('submitBtn').addEventListener('click', function() {
            let couponcode = document.getElementById('couponcode');
            let amount = document.querySelector('input[name="amount"]');
            let min_price = document.getElementById('min_price');
            let max_price = document.getElementById('max_price');
            let start_date = document.getElementById('start_date');
            let expiring_date = document.getElementById('expiring_date');
            let couponType = document.getElementById('couponType');
            let once = document.getElementById('once')

            if (!couponcode.value.trim() || !start_date.value.trim() || !expiring_date.value.trim() || !couponType
                .value.trim()) {
                alert("Please fill out all required fields!");
                return;
            }

            let users = [];

            document.querySelectorAll('.users-row tr').forEach(row => {
                let userId = row.getAttribute('data-user-id');
                if (userId) users.push(userId);
            });

            // If users array is empty, set users to 'all'
            if (users.length === 0) users = 'all';

            axios.post("/admin/spc_store", {
                    users: users,
                    couponcode: couponcode.value,
                    amount: amount.value,
                    min_price: min_price.value,
                    max_price: max_price.value,
                    start_date: start_date.value,
                    expiring_date: expiring_date.value,
                    couponType: couponType.value,
                    once: once.checked

                })
                .then(function(response) {
                    // Check if the response indicates success (you may need to adjust this condition)
                    if (response.status === 200) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your request was successful.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to a specific page
                                window.location.href =
                                '/admin/ecommerce/spedizione/list'; // Replace with your specific page URL
                            }
                        });
                    } else {
                        // Handle other response statuses or errors
                        console.log('Error: ', response);
                    }
                })
                .catch(function(error) {
                    // Handle Axios POST request errors
                    console.log('Error: ', error);
                });
        });
    </script>



@stop
