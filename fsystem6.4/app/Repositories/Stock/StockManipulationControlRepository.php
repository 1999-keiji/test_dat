<?php

declare(strict_types=1);

namespace App\Repositories\Stock;

use Cake\Chronos\Chronos;
use App\Models\Stock\StockManipulationControl;

class StockManipulationControlRepository
{
    /**
     * @var \App\Models\Stock\StockManipulationControl
     */
    private $model;

    /**
     * @param  \App\Models\Stock\StockManipulationControl $model
     * @return void
     */
    public function __construct(StockManipulationControl $model)
    {
        $this->model = $model;
    }

    /**
     * 在庫操作のロック
     *
     * @param  string $factory_code
     * @return void
     */
    public function lockStockManipulation(string $factory_code): void
    {
        $stock_manipulation_control = $this->model->firstOrCreate(compact('factory_code'));

        $stock_manipulation_control->stock_control_flag = true;
        $stock_manipulation_control->control_start_at = Chronos::now();
        $stock_manipulation_control->control_comp_at = null;
        $stock_manipulation_control->save();
    }

    /**
     * 在庫操作のロック解除
     *
     * @param  string $factory_code
     * @return void
     */
    public function unlockStockManipulation(string $factory_code): void
    {
        $stock_manipulation_control = $this->model->find($factory_code);

        $stock_manipulation_control->stock_control_flag = false;
        $stock_manipulation_control->control_comp_at = Chronos::now();
        $stock_manipulation_control->save();
    }
}
