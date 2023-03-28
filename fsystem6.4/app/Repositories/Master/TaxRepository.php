<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Tax;
use App\Models\Master\Collections\TaxCollection;

class TaxRepository
{
    /**
     * @var \App\Models\Master\Tax
     */
    private $model;

    /**
     * @param \App\Models\Master\Tax $model
     * @return void
     */
    public function __construct(Tax $model)
    {
        $this->model = $model;
    }

    /**
     * すべての消費税率を取得
     *
     * @return \App\Models\Master\Collections\TaxCollection
     */
    public function getAllConsumptionTaxRates(): TaxCollection
    {
        return $this->model->all();
    }
}
