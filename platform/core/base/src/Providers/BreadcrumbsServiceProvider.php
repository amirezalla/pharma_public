<?php

namespace Botble\Base\Providers;

use Botble\Base\Supports\BreadcrumbsGenerator;
use Breadcrumbs;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Route;
use Illuminate\Support\Facades\URL;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Breadcrumbs::register('dashboard.index', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->push(trans('core/base::layouts.dashboard'), route('dashboard.index'));
        });

        /**
         * Register breadcrumbs based on menu stored in session
         */

        Breadcrumbs::register('main', function (BreadcrumbsGenerator $breadcrumbs, $defaultTitle = null) {
            $prefix = '/' . ltrim($this->app->make('request')->route()->getPrefix(), '/');
            $url = URL::current();
            $arMenu = dashboard_menu()->getAll();
            dd($arMenu);

            if (Route::currentRouteName() != 'dashboard.index') {
                $breadcrumbs->parent('dashboard.index');
            }


            $found = false;
            foreach ($arMenu as $menuCategory) {
                // Check the top-level categories...
                if ($found) {
                    break;
                }

                if (! count($menuCategory['children'])) {
                    continue;
                }

                foreach ($menuCategory['children'] as $menuItem) {
                    // Your logic for matching the URLs...
                    if ($found) {
                        $breadcrumbs->push(trans($menuCategory['name']), $menuCategory['url']);
                        $breadcrumbs->push(trans($menuItem['name']), $menuItem['url']);
                        if ($defaultTitle != trans($menuItem['name']) && $defaultTitle != $siteTitle) {
                            $breadcrumbs->push($defaultTitle, $menuItem['url']);
                        }
                        break 2; // Breaks both loops
                    }
                }
            }

            if (! $found) {
                $breadcrumbs->push($defaultTitle, $url);
            }
        });

    }
}
