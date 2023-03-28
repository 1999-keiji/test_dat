<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use App\Models\Master\FactorySpecies;
use App\Models\Plan\BedState;
use App\ValueObjects\Date\WorkingDate;

class BedStateRepository
{
    /**
     * @var \App\Models\Plan\BedState
     */
    private $model;

    /**
     * @param  \App\Models\Plan\BedState $model
     * @return void
     */
    public function __construct(BedState $model)
    {
        $this->model = $model;
    }

    /**
     * ベッド状況の取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\ValueObjects\Date\WorkingDate $start_of_week
     * @return \App\Models\Plan\BedState|null
     */
    public function findBedState(FactorySpecies $factory_species, WorkingDate $start_of_week): ?BedState
    {
        return $this->model->where('factory_code', $factory_species->factory_code)
            ->where('factory_species_code', $factory_species->factory_species_code)
            ->where('start_of_week', $start_of_week->format('Y-m-d'))
            ->first();
    }

    /**
     * ベッド状況を条件に応じて検索
     *
     * @param  array $params
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchBedStates(array $params, array $order): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'bed_states.factory_code',
                'factories.factory_abbreviation',
                'bed_states.factory_species_code',
                'factory_species.factory_species_name',
                'bed_states.start_of_week',
                'bed_states.started_preparation_at',
                'bed_states.completed_preparation_at'
            ])
            ->leftJoin('factories', 'factories.factory_code', '=', 'bed_states.factory_code')
            ->leftJoin('factory_species', function ($join) {
                $join->on('factory_species.factory_code', '=', 'bed_states.factory_code')
                    ->on('factory_species.factory_species_code', '=', 'bed_states.factory_species_code');
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('bed_states.factory_code', $factory_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_species_code = $params['factory_species_code'] ?? null) {
                    $query->where('bed_states.factory_species_code', $factory_species_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($start_of_week = $params['start_of_week'] ?? null) {
                    $query->where('bed_states.start_of_week', $start_of_week);
                }
            })
            ->affiliatedFactories('bed_states');

        if (count($order) === 0) {
            $query->orderBy('bed_states.factory_code', 'ASC')
                ->orderBy('bed_states.factory_species_code', 'ASC')
                ->orderBy('bed_states.start_of_week', 'DESC');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * ベッド状況の登録
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\ValueObjects\Date\WorkingDate $start_of_week
     * @return \App\Models\Plan\BedState
     */
    public function create(FactorySpecies $factory_species, WorkingDate $start_of_week): BedState
    {
        return $this->model->create([
            'factory_code' => $factory_species->factory_code,
            'factory_species_code' => $factory_species->factory_species_code,
            'start_of_week' => $start_of_week->format('Y-m-d'),
            'started_preparation_at' => Chronos::now()->format('Y-m-d H:i:s')
        ]);
    }
}
