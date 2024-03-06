@php
    SeoHelper::setTitle(__('Thank you'));
    Theme::fireEventGlobalAssets();
@endphp

{!! Theme::partial('header') !!}
<div class="container">
    <main class="main">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="py-3 d-flex justify-content-center">
            <a href="{{ url('/') }}" class="">{{ trans('plugins/ecommerce::questionnire.back_to_home') }}</a>
        </div>
    </main>
</div>


{!! Theme::partial('footer') !!}

