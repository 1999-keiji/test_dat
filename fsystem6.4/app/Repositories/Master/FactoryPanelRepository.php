<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\FactoryPanel;

class FactoryPanelRepository
{
    /**
     * @var \App\Models\Master\FactoryPanel
     */
    private $model;

    /**
     * @param \App\Models\Master\FactoryPanel $model
     * @return void
     */
    public function __construct(FactoryPanel $model)
    {
        $this->model = $model;
    }

    /**
     * 工場パネルマスタの登録
     *
     * @param  string $factory_code
     * @param  int $number_of_holes
     * @return \App\Models\Master\FactoryPanel
     */
    public function create(string $factory_code, int $number_of_holes): FactoryPanel
    {
        return $this->model->create([
            'factory_code' => $factory_code,
            'number_of_holes' => $number_of_holes
        ]);
    }
}
