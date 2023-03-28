<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Menu;
use App\Models\Master\Collections\MenuCollection;

class MenuRepository
{
    /**
     * @var \App\Models\Master\Menu
     */
    private $model;

    /**
     * @param  \App\Models\Master\Menu
     * @return void
     */
    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    /**
     * メニューの取得
     *
     * @return \App\Models\Menu\Collections\MenuCollection
     */
    public function getAllMenus(): MenuCollection
    {
        return $this->model
            ->select([
                'category',
                'tab_no',
                'tab_code',
                'group_row_no',
                'group_column_no',
                'category_order',
                'tab_name',
                'group_name',
                'category_name',
            ])
            ->orderBy('tab_no', 'ASC')
            ->orderBy('group_column_no', 'ASC')
            ->orderBy('group_row_no', 'ASC')
            ->orderBy('category_order', 'ASC')
            ->get();
    }
}
