<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {!! BaseHelper::googleFonts(
        'https://fonts.googleapis.com/css2?family=' .
            urlencode(theme_option('font_text', 'Poppins')) .
            ':ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap',
    ) !!}

    <style>
        :root {
            --font-text: {{ theme_option('font_text', 'Poppins') }}, sans-serif;
            --color-brand: {{ theme_option('color_brand', '#5897fb') }};
            --color-brand-2: {{ theme_option('color_brand_2', '#3256e0') }};
            --color-primary: {{ theme_option('color_primary', '#3f81eb') }};
            --color-secondary: {{ theme_option('color_secondary', '#41506b') }};
            --color-warning: {{ theme_option('color_warning', '#ffb300') }};
            --color-danger: {{ theme_option('color_danger', '#ff3551') }};
            --color-success: {{ theme_option('color_success', '#3ed092') }};
            --color-info: {{ theme_option('color_info', '#18a1b7') }};
            --color-text: {{ theme_option('color_text', '#4f5d77') }};
            --color-heading: {{ theme_option('color_heading', '#222222') }};
            --color-grey-1: {{ theme_option('color_grey_1', '#111111') }};
            --color-grey-2: {{ theme_option('color_grey_2', '#242424') }};
            --color-grey-4: {{ theme_option('color_grey_4', '#90908e') }};
            --color-grey-9: {{ theme_option('color_grey_9', '#f4f5f9') }};
            --color-muted: {{ theme_option('color_muted', '#8e8e90') }};
            --color-body: {{ theme_option('color_body', '#4f5d77') }};
        }
    </style>
    {{--        @dd(Theme::header()) --}}
    {!! Theme::header() !!}

    @php
        $headerStyle = theme_option('header_style') ?: '';
        $page = Theme::get('page');
        if ($page) {
            $headerStyle = $page->getMetaData('header_style', true) ?: $headerStyle;
        }
        $headerStyle = $headerStyle && in_array($headerStyle, array_keys(get_layout_header_styles())) ? $headerStyle : '';
    @endphp
</head>

<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif
    class="@if (BaseHelper::siteLanguageDirection() == 'rtl') rtl @endif header_full_true wowy-template css_scrollbar lazy_icons btnt4_style_2 zoom_tp_2 css_scrollbar template-index wowy_toolbar_true hover_img2 swatch_style_rounded swatch_list_size_small label_style_rounded wrapper_full_width header_full_true header_sticky_true hide_scrolld_true des_header_3 h_banner_true top_bar_true prs_bordered_grid_1 search_pos_canvas lazyload @if (Theme::get('bodyClass')) {{ Theme::get('bodyClass') }} @endif">
    {!! apply_filters(THEME_FRONT_BODY, null) !!}
    <div id="alert-container"></div>

    {!! Theme::partial('preloader') !!}

    <header class="header-area header-height-2 {{ $headerStyle }}">
        <div class="header header-top-ptb-1 d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center ">
                    <div class="col-xl-7 col-lg-7 col-md-7 ">
                        <div class="header-info">
                            <ul>
                                @if (theme_option('hotline'))
                                    <li><i class="fa fa-phone-alt mr-5"></i><a
                                            style="text-transform:uppercase !important"
                                            href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a>
                                    </li>
                                @endif

                                @if (is_plugin_active('ecommerce') && EcommerceHelper::isOrderTrackingEnabled())
                                    <li><i class="fa fa-phone-alt mr-3"></i><a
                                            style="text-transform:uppercase !important"
                                            href="tel:+39 0815344611">{{ __('+39 0815344611') }}</a></li>
                                    <li><i class="fa fa-envelope mr-5"></i><a
                                            style="text-transform:uppercase !important"
                                            href="">{{ __('info@marigopharma.it ') }}</a></li>
                                    <li><i class="fa fa-map-pin mr-5"></i><a style="text-transform:uppercase !important"
                                            href="">{{ __('Via Bagnulo, 168 - Piano di Sorrento (NA)') }}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>



                    @if (is_plugin_active('ecommerce') || is_plugin_active('language'))
                        <div
                            class="d-flex justify-content-end align-items-center col-lg-5 col-xl-5 col-md-5 float-end ">
                            <div class="header-info header-info-right">
                                <ul>
                                    @if (is_plugin_active('language'))
                                        {!! Theme::partial('language-switcher') !!}
                                    @endif

                                    @if (is_plugin_active('ecommerce'))

                                        @if (auth('customer')->check())
                                            <li>
                                                <i class="fa fa-user mr-5"
                                                    style="color: rgb(0, 92, 158) !important"></i>
                                                <a class="profile-action-2"
                                                    href="{{ route('customer.overview') }}">Bentornato!
                                                    {{ auth('customer')->user()->name }}</a>
                                                <ul class="profile-dropdown-wrap profile-dropdown-hm2">
                                                    <div class="row" style="width:100%"> <a
                                                            class="bg-light item-profile-wrap"
                                                            href="https://marigopharma.marigo.collaudo.biz/customer/edit-account">Il
                                                            mio profilo</a></div>
                                                    <div class="row" style="width:100%"><a
                                                            class="bg-light item-profile-wrap"
                                                            href="https://marigopharma.marigo.collaudo.biz/customer/orders">I
                                                            miei ordini</a></div>
                                                    <div class="row" style="width:100%"><a
                                                            class="bg-light item-profile-wrap"
                                                            href="https://marigopharma.marigo.collaudo.biz/products?userid={{ auth('customer')->user()->id }}&preferiti_page=1&wishlist=1">I
                                                            miei preferiti</a></div>
                                                    <div class="row" style="width:100%"> <a
                                                            class="bg-light item-profile-wrap"
                                                            href="https://marigopharma.marigo.collaudo.biz/customer/change-password">Modifica
                                                            password</a></div>
                                                    <div class="row" style="width:100%"> <a
                                                            class="bg-light item-profile-wrap" target="_blank"
                                                            href="https://marigopharma.marigo.collaudo.biz/storage/2124-condizioni-generali-di-vendita-webview.pdf">Condizioni
                                                            Generali</a></div>
                                                    <div class="row" style="width:100%"><a
                                                            style="background: #51B448;color:white !important"
                                                            class="item-profile-wrap"
                                                            href="https://marigopharma.marigo.collaudo.biz/customer/logout">Esci</a>
                                                    </div>

                                                </ul>
                                            </li>
                                        @else
                                            <li style='font-size:larger'>
                                                <i class="fa fa-user mr-5"
                                                    style="color: rgb(0, 92, 158) !important"></i>
                                                <a
                                                    href="{{ route('customer.login') }}">{{ __('Log In / Sign Up') }}</a>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
            <div class="container">
                <div class="header-wrap header-space-between">
                    @if (theme_option('logo'))
                        <div class="logo logo-width-1">
                            <a href="{{ route('public.index') }}"><img
                                    src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                    alt="{{ theme_option('site_title') }}"></a>
                        </div>
                    @endif
                    @if (is_plugin_active('ecommerce') && isset($categories))
                        <div class="search-style-2">
                            <form action="{{ route('public.products') }}" method="get">
                                <div class="form-group--icon">
                                    <div class="product-cat-label">{{ __('All Categories') }}</div>
                                    <select class="product-category-select" name="categories[]">
                                        <option value="">{{ __('All Categories') }}</option>
                                        {!! Theme::partial('product-categories-select', ['categories' => $categories, 'indent' => null]) !!}
                                    </select>
                                </div>
                                <input type="text" name="q" style="border-radius:0px 50px 50px 0"
                                    placeholder="{{ __('Search for items…') }}" autocomplete="off">
                                <button type="submit"> <i class="far fa-search"></i> </button>
                            </form>
                        </div>
                        <div class="header-action-right">
                            <div class="header-action-2">
                                @if (auth('customer')->user() !== null)
                                    <div class="header-action-icon-2">

                                        <a class="mini-cart-icon"
                                            href="@if (auth('customer')->check()) {{ route('public.cart') }} @else {{ route('customer.login') }} @endif">
                                            <img alt="{{ __('Cart') }}"
                                                src="{{ Theme::asset()->url('images/icons/icon-cart.svg') }}">
                                            <span class="pro-count blue">{{ Cart::instance('cart')->count() }}</span>
                                        </a>


                                        <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                            {!! Theme::partial('cart-panel') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="header-bottom header-bottom-bg-color sticky-bar gray-bg sticky-blue-bg">
            <div class="container">
                <div class="header-wrap header-space-between position-relative main-nav">
                    @if ($logo = theme_option('logo_light') ?: theme_option('logo'))
                        <div class="logo logo-width-1 d-block d-lg-none">
                            <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl($logo) }}"
                                    alt="{{ theme_option('site_title') }}"></a>
                        </div>
                    @endif

                    @if (theme_option('enabled_browse_categories_on_header', 'yes') == 'yes')
                        @php
                            $openBrowse = $page && $page->template == 'homepage' && $page->getMetaData('expanding_product_categories_on_the_homepage', true) == 'yes';
                            $cantCloseBrowse = $openBrowse && $headerStyle == 'header-style-2';
                        @endphp
                        <div class="main-categories-wrap d-none d-lg-block">
                            <a class="categories-button-active @if ($openBrowse) open @endif @if ($cantCloseBrowse) cant-close @endif"
                                href="#">
                                <span class="fa fa-list"></span> {{ __('Browse Categories') }} <i
                                    class="down far fa-chevron-down"></i> <i class="up far fa-chevron-up"></i>
                            </a>
                            <div
                                class="categories-dropdown-wrap categories-dropdown-active-large @if ($openBrowse) default-open open @endif">
                                <ul>
                                    {!! Theme::partial('product-categories-dropdown', ['categories' => $categories, 'more' => false]) !!}
                                    @if (count($categories) > 10)
                                        <li>
                                            <ul class="more_slide_open">
                                                {!! Theme::partial('product-categories-dropdown', ['categories' => $categories, 'more' => true]) !!}
                                            </ul>
                                        </li>
                                    @endif
                                </ul>

                                @if (count($categories) > 10)
                                    <div class="more_categories">{{ __('Show more...') }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div
                        class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block main-menu-light-white hover-boder hover-boder-white">
                        <nav>
                            {!! Menu::renderMenuLocation('main-menu', [
                                'view' => 'main-menu',
                            ]) !!}
                        </nav>
                    </div>

                    @if (theme_option('hotline'))
                        <div class="hotline d-none d-lg-block">
                            <p><i class="fa fa-phone-alt"></i><span>{{ __('Hotline') }}</span>
                                {{ theme_option('hotline') }}</p>
                        </div>
                    @endif

                    @if (is_plugin_active('ecommerce'))
                        <div class="header-action-right d-block d-lg-none">
                            <div class="header-action-2">
                                @if (EcommerceHelper::isCompareEnabled())
                                    <div class="header-action-icon-2">
                                        <a href="{{ route('public.compare') }}" class="compare-count">
                                            <img class="svgInject" alt="{{ __('Compare') }}"
                                                src="{{ Theme::asset()->url('images/icons/icon-compare-white.svg') }}">
                                            <span
                                                class="pro-count white"><span>{{ Cart::instance('compare')->count() }}</span></span>
                                        </a>
                                    </div>
                                @endif
                                @if (EcommerceHelper::isWishlistEnabled())
                                    <div class="header-action-icon-2">
                                        <a href="{{ route('public.wishlist') }}" class="wishlist-count">
                                            <img alt="wowy"
                                                src="{{ Theme::asset()->url('images/icons/icon-heart-white.svg') }}">
                                            <span class="pro-count white">
                                                @if (auth('customer')->check())
                                                    <span>{{ auth('customer')->user()->wishlist()->count() }}</span>
                                                @else
                                                    <span>{{ Cart::instance('wishlist')->count() }}</span>
                                                @endif
                                            </span>
                                        </a>
                                    </div>
                                @endif
                                <div class="header-action-icon-2">
                                    <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                                        <img alt="cart"
                                            src="{{ Theme::asset()->url('images/icons/icon-cart-white.svg') }}">
                                        <span class="pro-count white">{{ Cart::instance('cart')->count() }}</span>
                                    </a>
                                    <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                        {!! Theme::partial('cart-panel') !!}
                                    </div>
                                </div>
                                <div class="header-action-icon-2">
                                    <a href="{{ route('customer.login') }}">
                                        <img alt="wowy"
                                            src="{{ Theme::asset()->url('images/icons/icon-user-white.svg') }}">
                                    </a>
                                </div>
                                <div class="header-action-icon-2 d-block d-lg-none">
                                    <div class="burger-icon burger-icon-white">
                                        <span class="burger-icon-top"></span>
                                        <span class="burger-icon-mid"></span>
                                        <span class="burger-icon-bottom"></span>
                                    </div>
                                </div>
                            </div>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </header>
    <div class="mobile-header-active mobile-header-wrapper-style">
        <div class="mobile-header-wrapper-inner">
            <div class="mobile-header-top">
                @if (theme_option('logo'))
                    <div class="mobile-header-logo">
                        <a href="{{ route('public.index') }}"><img
                                src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                alt="{{ theme_option('site_title') }}"></a>
                    </div>
                @endif
                <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                    <button class="close-style search-close">
                        <i class="icon-top"></i>
                        <i class="icon-bottom"></i>
                    </button>
                </div>
            </div>
            @if (is_plugin_active('ecommerce') && isset($categories))
                <div class="mobile-header-content-area">
                    <div class="mobile-search search-style-3 mobile-header-border">
                        <form action="{{ route('public.products') }}">
                            <input type="text" name="q" placeholder="{{ __('Search...') }}">
                            <button type="submit"> <i class="far fa-search"></i> </button>
                        </form>
                    </div>
                    <div class="mobile-menu-wrap mobile-header-border">
                        <div class="main-categories-wrap mobile-header-border">
                            <a class="categories-button-active-2" href="#">
                                <span class="far fa-bars"></span> {{ __('Browse Categories') }} <i
                                    class="down far fa-chevron-down"></i>
                            </a>
                            <div class="categories-dropdown-wrap categories-dropdown-active-small">
                                <ul>
                                    @foreach ($categories as $category)
                                        <li>
                                            <a href="{{ $category->url }}">
                                                @if ($category->getMetaData('icon_image', true))
                                                    <img src="{{ RvMedia::getImageUrl($category->getMetaData('icon_image', true)) }}"
                                                        alt="{{ $category->name }}" width="18" height="18">
                                                @elseif ($category->getMetaData('icon', true))
                                                    <i class="{{ $category->getMetaData('icon', true) }}"></i>
                                                @endif {{ $category->name }}

                                                @if ($category->activeChildren->count() > 0)
                                                    <span class="menu-expand"><i
                                                            class="down far fa-chevron-down"></i></span>
                                                @endif
                                            </a>
                                            @if ($category->activeChildren->count() > 0)
                                                <ul class="dropdown" style="display: none">
                                                    @foreach ($category->activeChildren as $childCategory)
                                                        <li><a
                                                                href="{{ $childCategory->url }}">{{ $childCategory->name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!-- mobile menu start -->
                        <nav>
                            {!! Menu::renderMenuLocation('main-menu', [
                                'options' => ['class' => 'mobile-menu'],
                                'view' => 'mobile-menu',
                            ]) !!}
                        </nav>
                        <!-- mobile menu end -->
                    </div>
                    <div class="mobile-header-info-wrap mobile-header-border">
                        @if (is_plugin_active('language'))
                            <div class="single-mobile-header-info">
                                <a class="mobile-language-active" href="#">{{ __('Language') }} <span><i
                                            class="far fa-angle-down"></i></span></a>
                                <div class="lang-curr-dropdown lang-dropdown-active">
                                    <ul>
                                        @php
                                            $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);
                                        @endphp

                                        @foreach (Language::getSupportedLocales() as $localeCode => $properties)
                                            <li><a rel="alternate" hreflang="{{ $localeCode }}"
                                                    href="{{ $showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode) }}">{!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                                                    {{ $properties['lang_name'] }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif



                        @if (is_plugin_active('ecommerce'))
                            <div class="single-mobile-header-info">
                                @if (auth('customer')->check())
                                    <a
                                        href="{{ route('customer.overview') }}">{{ auth('customer')->user()->name }}</a>
                                @else
                                    <a href="{{ route('customer.login') }}">{{ __('Log In / Sign Up') }}</a>
                                @endif
                            </div>
                        @endif

                        @if (theme_option('hotline'))
                            <div class="single-mobile-header-info">
                                <a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a>
                            </div>
                        @endif
                    </div>

                    @if (theme_option('social_links'))
                        <div class="mobile-social-icon">
                            @foreach (json_decode(theme_option('social_links'), true) as $socialLink)
                                @if (count($socialLink) == 4)
                                    <a href="{{ $socialLink[2]['value'] }}" title="{{ $socialLink[0]['value'] }}"
                                        style="background-color: {{ $socialLink[3]['value'] }}; border: 1px solid {{ $socialLink[3]['value'] }};">
                                        <i class="{{ $socialLink[1]['value'] }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
