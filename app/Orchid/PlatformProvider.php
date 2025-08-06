<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', []);
        $dashboard->registerResource('scripts', []);
        
        view()->share('brandName', 'amuz');
        view()->share('brandLogo', 'amuz');
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('대시보드')
                ->icon('bs.speedometer2')
                ->title('주요 기능')
                ->route('platform.main'),

            Menu::make('카테고리 관리')
                ->icon('bs.folder')
                ->route('platform.categories')
                ->title('상품 관리'),

            Menu::make('상품 관리')
                ->icon('bs.box-seam')
                ->route('platform.products'),

            Menu::make('주문 관리')
                ->icon('bs.cart3')
                ->route('platform.orders')
                ->title('주문 관리')
                ->divider(),

            Menu::make(__('사용자 관리'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('시스템 관리')),

            Menu::make(__('권한 관리'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
