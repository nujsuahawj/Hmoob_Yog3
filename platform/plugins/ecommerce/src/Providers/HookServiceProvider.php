<?php

namespace Botble\Ecommerce\Providers;

use Assets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Menu;
use Throwable;

class HookServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 12);
        }

        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 208, 2);
    }

    /**
     * Register sidebar options in menu
     *
     * @throws Throwable
     */
    public function registerMenuOptions()
    {
        /**
         * @var Collection $brands
         */
        $brands = Menu::generateSelect([
            'model'   => $this->app->make(BrandInterface::class)->getModel(),
            'type'    => Brand::class,
            'theme'   => false,
            'options' => [
                'class' => 'list-item',
            ],
        ]);

        if ($brands) {
            echo view('plugins/ecommerce::brands.partials.menu-options', compact('brands'));
        }

        $productCategories = Menu::generateSelect([
            'model'   => $this->app->make(ProductCategoryInterface::class)->getModel(),
            'type'    => ProductCategory::class,
            'theme'   => false,
            'options' => ['class' => 'list-item'],
        ]);

        if ($productCategories) {
            echo view('plugins/ecommerce::product-categories.partials.menu-options', compact('productCategories'));
        }

        return true;
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function registerDashboardWidgets($widgets, $widgetSettings)
    {
        if (!Auth::user()->hasPermission('ecommerce.report.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly(['vendor/core/plugins/ecommerce/js/report.js']);

        return (new DashboardWidgetInstance)
            ->setPermission('ecommerce.report.index')
            ->setKey('widget_ecommerce_report_general')
            ->setTitle(trans('plugins/ecommerce::ecommerce.name'))
            ->setIcon('fas fa-shopping-basket')
            ->setColor('#7ad03a')
            ->setRoute(route('ecommerce.report.dashboard-widget.general'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }
}
