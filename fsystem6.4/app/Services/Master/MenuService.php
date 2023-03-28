<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Models\Master\Collections\MenuCollection;
use App\Repositories\Master\MenuRepository;

class MenuService
{
    /**
     * @var \App\Repositories\Master\MenuRepository
     */
    private $menu_repo;

    /**
     * @param  \App\Repositories\Master\MenuRepository $menu_repo
     * @return void
     */
    public function __construct(MenuRepository $menu_repo)
    {
        $this->menu_repo = $menu_repo;
    }

    /**
     * メニューの取得
     *
     * @return \App\Models\Menu\Collections\MenuCollection
     */
    public function getAllTabs(): MenuCollection
    {
        return $this->menu_repo->getAllMenus()->groupByTab();
    }
}
