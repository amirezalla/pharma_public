@php
    $menus = dashboard_menu()->getAll();
@endphp
@foreach ($menus as $menu)
    @php $menu = apply_filters(BASE_FILTER_DASHBOARD_MENU, $menu); @endphp
    @if (
  $menu['id']=='cms-plugins-ads' ||
   $menu['id']=='cms-plugins-newsletter' ||


   $menu['id']=='cms-plugins-location'||
   $menu['id']=='cms-core-plugins'
   || $menu['id']=='cms-plugin-translation'
|| $menu['id']=='cms-core-tools'



   )


    @else
        <li class="nav-item @if ($menu['active']) active @endif" id="{{ $menu['id'] }}">
            <a href="{{ $menu['url'] }}" class="nav-link nav-toggle">
                <i class="{{ $menu['icon'] }}"></i>
                <span class="title">
                {{ !is_array(trans($menu['name'])) ? trans($menu['name']) : null }}
                    {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $menu['id']) !!}</span>
                @if (isset($menu['children']) && count($menu['children'])) <span class="arrow @if ($menu['active']) open @endif"></span> @endif
            </a>
            @if (isset($menu['children']) && count($menu['children']))
                <ul class="sub-menu @if (!$menu['active']) hidden-ul @endif">
                    @foreach ($menu['children'] as $item)
                        <li class="nav-item @if ($item['active']) active @endif" id="{{ $item['id'] }}">
                            <a href="{{ $item['url'] }}" class="nav-link">
                                <i class="{{ $item['icon'] }}"></i>
                                {{ trans($item['name']) }}
                                {!! apply_filters(BASE_FILTER_APPEND_MENU_NAME, null, $item['id']) !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>

    @endif


@endforeach
<li class="nav-item" id="0">
    <a href="{{ route('admin.ecommerce.questionnaires.index') }}" class="nav-link nav-toggle">
        <i class="fa fa-question"></i>
        <span class="title">Questionari</span>
    </a>
</li>
<li class="nav-item" id='1'>
    <a href="{{ route('admin.ecommerce.mail-log.list') }}" class="nav-link nav-toggle">
        <i class="fa fa-envelope" aria-hidden="true"></i>
        <span class="title">Mail Log</span>
    </a>
</li>

<li class="nav-item" id="2">
    <a href="{{ route('admin.ecommerce.offerte.list-view') }}" class="nav-link nav-toggle">
        <i class="fa fa-percent" aria-hidden="true"></i>
        <span class="title">Offerte</span>
    </a>
</li>
<li class="nav-item" id="3">
    <a class="nav-link nav-toggle">
        <i class="fas fa-shipping-fast" aria-hidden="true"></i>
        <span class="title">Spedizione</span>
        <span class="arrow "></span>
    </a>
    <ul class="sub-menu hidden-ul">
        <li class="nav-item">

            <a href="{{ route('admin.ecommerce.spedizione.view') }}" class="nav-link">
                <span class="title">Configurazione</span>
            </a>


        </li>
        <li class="nav-item">
            <a href="{{ route('admin.ecommerce.spedizione.create-offer') }}" class="nav-link">
                <i class=""></i>
                Create
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.ecommerce.spedizione.list') }}" class="nav-link">
                <i class=""></i>
                Coupons List
            </a>
        </li>


    </ul>
</li>
<li class="nav-item" id="4">
    <a href="{{ route('admin.ecommerce.offertype.view') }}" class="nav-link nav-toggle">
{{--    <a href="" class="nav-link nav-toggle">--}}
        <i class="fab fa-periscope"></i>
        <span class="title">Suggeriti</span>
    </a>
</li>
{{--<li class="nav-item" id="3">--}}
{{--    <a class="nav-link nav-toggle">--}}
{{--        <i class="fas fa-shipping-fast" aria-hidden="true"></i>--}}
{{--        <span class="title">Spedizione</span>--}}
{{--        <span class="arrow "></span>--}}
{{--    </a>--}}
{{--    <ul class="sub-menu hidden-ul">--}}
{{--        <li class="nav-item">--}}

{{--            <a href="{{ route('admin.ecommerce.spedizione.shipping.form') }}" class="nav-link">--}}
{{--                <span class="title">Configurazione</span>--}}
{{--            </a>--}}


{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('admin.ecommerce.spedizione.create-offer') }}" class="nav-link">--}}
{{--                <i class=""></i>--}}
{{--                Create--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('admin.ecommerce.spedizione.list') }}" class="nav-link">--}}
{{--                <i class=""></i>--}}
{{--                Coupons List--}}
{{--            </a>--}}
{{--        </li>--}}


    </ul>
</li>

{{--<li class="nav-item" id="1">--}}
{{--    <a href="{{ route('admin.ecommerce.offertype.view') }}" class="nav-link nav-toggle">--}}
{{--        <i class="fab fa-periscope"></i>--}}
{{--        <span class="title">Suggeriti</span>--}}
{{--    </a>--}}
{{--</li>--}}
{{--<li class="nav-item" id="2">--}}
{{--    <a href="{{ route('admin.ecommerce.offerte.list-view') }}" class="nav-link nav-toggle">--}}
{{--        <i class="fa fa-percent" aria-hidden="true"></i>--}}
{{--        <span class="title">Offerte</span>--}}
{{--    </a>--}}
{{--</li>--}}
