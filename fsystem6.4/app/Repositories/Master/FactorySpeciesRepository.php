<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Database\Connection;
use App\Models\Master\Collections\FactorySpeciesCollection;
use App\Models\Master\FactorySpecies;

class FactorySpeciesRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Master\FactorySpecies
     */
    private $model;

    /**
     * @param  \App\Models\Master\FactorySpecies $model
     * @return void
     */
    public function __construct(Connection $db, FactorySpecies $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 工場取扱品種マスタを取得
     *
     * @param  array $primary_key
     * @return \App\Models\Master\FactorySpecies
     */
    public function findFactorySpecies(array $primary_key): FactorySpecies
    {
        $query = $this->model->newQuery();
        foreach ($this->model->getKeyName() as $key) {
            $query->where($key, $primary_key[$key]);
        }

        return $query->first();
    }

    /**
     * 工場に紐づく工場取扱品種を取得
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function getFactorySpecies(array $params): FactorySpeciesCollection
    {
        return $this->model
            ->select([
                'factory_species.species_code',
                'factory_species.factory_species_code',
                'factory_species.factory_species_name',
                'factory_species.can_select_on_simulation',
            ])
            ->where('factory_species.factory_code', $params['factory_code'])
            ->sortable(['factory_species_code' => 'ASC'])
            ->with('species')
            ->get();
    }

    /**
     * 工場が一致する工場品種から品種を検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function getSpeciesWithFactoryCode(array $params): FactorySpeciesCollection
    {
        return $this->model
            ->select([
                'species.species_code',
                'species.species_name',
                'species.species_abbreviation'
            ])
            ->join('species', function ($join) {
                $join->on('species.species_code', '=', 'factory_species.species_code');
            })
            ->where('factory_species.factory_code', $params['factory_code'])
            ->groupBy('species.species_code')
            ->orderBy('factory_species.species_code', 'ASC')
            ->get();
    }

    /**
     * 工場取扱品種を登録
     *
     * @param  array $params
     * @return \App\Models\Master\FactorySpecies
     */
    public function create(array $params): FactorySpecies
    {
        return $this->model->create($params);
    }

    /**
     * 工場取扱品種を登録
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  array $params
     * @return \App\Models\Master\FactorySpecies
     */
    public function update(FactorySpecies $factory_species, array $params): FactorySpecies
    {
        $factory_species->fill($params)->save();
        return $factory_species;
    }
}
