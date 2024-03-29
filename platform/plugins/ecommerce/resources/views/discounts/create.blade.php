@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open() !!}
    <div id="main-discount">
        <div class="max-width-1200">
            <discount-component currency="{{ get_application_currency()->symbol }}"
                date-format="{{ config('core.base.general.date_format.date') }}"></discount-component>
        </div>
    </div>
    {!! Form::close() !!}
@stop

@push('header')
    <script>
        'use strict';

        window.trans = window.trans || {};

        window.trans.discount = JSON.parse('{!! addslashes(json_encode(trans('plugins/ecommerce::discount'))) !!}');

        $(document).ready(function() {
            $(document).on('click', function() {
                // $("#select-offers option[value='product-variant']").css('display','none');
                // $("#select-offers option[value='group-products']").css('display','none');
                // $("#select-offers option[value='specific-product']").css('display','none');
                // $("#discount-type-option option[value='same-price']").css('display','none');
                // $("#discount-type-option option[value='shipping']").css('display','none');
            });




            $(document).on('click', 'body', function(e) {
                let container = $('.box-search-advance');

                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.find('.panel').addClass('hidden');
                }
            });
        });
    </script>
    @php
        Assets::addScripts(['form-validation']);
    @endphp
    {!! JsValidator::formRequest(\Botble\Ecommerce\Http\Requests\DiscountRequest::class) !!}
@endpush
