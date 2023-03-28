<?php

declare(strict_types=1);

namespace App\Models\Master\Collections;

use Illuminate\Database\Eloquent\Collection;

class MenuCollection extends Collection
{
    /**
     * タブごとにグループ化
     *
     * @return \App\Models\Master\Collections\MenuCollection
     */
    public function groupByTab(): MenuCollection
    {
        $tabs = $this->groupBy('tab_no')
            ->map(function ($grouped, $tab_no) {
                return (object)[
                    'tab_no' => $tab_no,
                    'tab_code' => $grouped->first()->tab_code,
                    'tab_name' => $grouped->first()->tab_name,
                    'groups' => $grouped->groupByColumn()->map(function ($grouped) {
                        return $grouped->groupByRow()->map(function ($grouped) {
                            return (object)[
                                'group_name' => $grouped->first()->group_name,
                                'categories' => $grouped->all()
                            ];
                        });
                    })
                ];
            })
            ->values()
            ->all();

        return new MenuCollection($tabs);
    }

    /**
     * 列ごとにグループ化
     *
     * @return \App\Models\Master\Collections\MenuCollection
     */
    public function groupByColumn(): MenuCollection
    {
        return $this->groupBy('group_column_no')->values();
    }

    /**
     * 行ごとにグループ化
     *
     * @return \App\Models\Master\Collections\MenuCollection
     */
    public function groupByRow(): MenuCollection
    {
        return $this->groupBy('group_row_no')->values();
    }
}
