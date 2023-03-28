<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\FactoryGrowingStage;
use App\Models\Master\FactorySpecies;

class FactoryGrowingStageRepository
{
    /**
     * @var \App\Models\Plan\FactoryGrowingStage
     */
    private $model;

    /**
     * @param  \App\Models\Plan\FactoryGrowingStage $model
     * @return void
     */
    public function __construct(FactoryGrowingStage $model)
    {
        $this->model = $model;
    }

    /**
     * 工場生育ステージマスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactoryGrowingStage
     */
    public function create($params): FactoryGrowingStage
    {
        return $this->model->create($params);
    }

    /**
     * 工場生育ステージマスタの削除
     *
     * @param \App\Models\Master\FactorySpecies $factory_species
     * @param array $params
     */
    public function delete(FactorySpecies $factory_species)
    {
        return $this->model
            ->where('factory_code', $factory_species->factory_code)
            ->where('factory_species_code', $factory_species->factory_species_code)
            ->delete();
    }
}
