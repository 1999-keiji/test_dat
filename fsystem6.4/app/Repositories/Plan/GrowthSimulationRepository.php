<?php

declare(strict_types=1);

namespace App\Repositories\Plan;

use Illuminate\Pagination\LengthAwarePaginator;
use Cake\Chronos\Chronos;
use App\Models\Plan\GrowthSimulation;

class GrowthSimulationRepository
{
    /**
     * @var \App\Models\Plan\GrowthSimulation
     */
    private $model;

    /**
     * @param  \App\Models\Plan\GrowthSimulation $model
     * @return void
     */
    public function __construct(GrowthSimulation $model)
    {
        $this->model = $model;
    }

    /**
     * 生産シミュレーションを条件に応じて検索
     *
     * @param  array $params
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params, array $order): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'growth_simulation.factory_code',
                'factories.factory_abbreviation',
                'growth_simulation.simulation_id',
                'growth_simulation.factory_species_code',
                'factory_species.factory_species_name',
                'growth_simulation.simulation_name',
                'create_user.user_name as created_name',
                'growth_simulation.work_by',
                'growth_simulation.work_at',
                'work_user.user_name as work_name',
                'growth_simulation.simulation_preparation_start_at',
                'growth_simulation.simulation_preparation_comp_at'
            ])
            ->leftJoin('factories', 'growth_simulation.factory_code', '=', 'factories.factory_code')
            ->leftJoin('factory_species', function ($join) {
                $join->on('growth_simulation.factory_code', '=', 'factory_species.factory_code')
                    ->on('growth_simulation.factory_species_code', '=', 'factory_species.factory_species_code');
            })
            ->leftJoin('users as work_user', 'growth_simulation.work_by', '=', 'work_user.user_code')
            ->join('users as create_user', 'growth_simulation.created_by', '=', 'create_user.user_code')
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('growth_simulation.factory_code', $factory_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_species_code = $params['factory_species_code'] ?? null) {
                    $query->where('growth_simulation.factory_species_code', $factory_species_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($simulation_name = $params['simulation_name'] ?? null) {
                    $query->where('growth_simulation.simulation_name', 'LIKE', "%{$simulation_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($user_name = $params['user_name'] ?? null) {
                    $query->where('create_user.user_name', 'LIKE', "%{$user_name}%");
                }
            })
            ->whereNull('growth_simulation.fixed_start_at')
            ->whereNull('growth_simulation.fixed_comp_at')
            ->affiliatedFactories('growth_simulation');

        if (count($order) === 0) {
            $query->orderBy('growth_simulation.simulation_preparation_comp_at', 'DESC')
                ->orderBy('growth_simulation.factory_code', 'ASC')
                ->orderBy('growth_simulation.factory_species_code', 'ASC')
                ->orderBy('growth_simulation.created_at', 'DESC');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * 確定された生産シミュレーションを条件に応じて検索
     *
     * @param  array $params
     * @param  array $order
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchFixed(array $params, array $order): LengthAwarePaginator
    {
        $query = $this->model
            ->select([
                'growth_simulation.factory_code',
                'growth_simulation.simulation_id',
                'factories.factory_name',
                'factories.factory_abbreviation',
                'growth_simulation.factory_species_code',
                'factory_species.factory_species_name',
                'growth_simulation.simulation_name',
                'growth_simulation.fixed_start_at',
                'growth_simulation.fixed_comp_at',
                'users.user_name as fixed_name'
            ])
            ->leftJoin('factories', 'growth_simulation.factory_code', '=', 'factories.factory_code')
            ->leftJoin('factory_species', function ($join) {
                $join->on('growth_simulation.factory_code', '=', 'factory_species.factory_code')
                    ->on('growth_simulation.factory_species_code', '=', 'factory_species.factory_species_code');
            })
            ->leftJoin('users', 'growth_simulation.fixed_by', '=', 'users.user_code')
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code'] ?? null) {
                    $query->where('growth_simulation.factory_code', $factory_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_species_code = $params['factory_species_code'] ?? null) {
                    $query->where('growth_simulation.factory_species_code', $factory_species_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($simulation_name = $params['simulation_name']) {
                    $query->where('growth_simulation.simulation_name', 'LIKE', "%{$simulation_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($fixed_at_begin = $params['fixed_at_begin']) {
                    $query->where(
                        'growth_simulation.fixed_comp_at',
                        '>=',
                        Chronos::parse($fixed_at_begin)->startOfDay()
                    );
                }
                if ($fixed_at_end = $params['fixed_at_end']) {
                    $query->where(
                        'growth_simulation.fixed_comp_at',
                        '<=',
                        Chronos::parse($fixed_at_end)->endOfDay()
                    );
                }
            })
            ->where(function ($query) use ($params) {
                if ($user_name = $params['user_name']) {
                    $query->where('users.user_name', 'LIKE', "%{$user_name}%");
                }
            })
            ->where(function ($query) {
                $query->whereNotNull('growth_simulation.fixed_start_at')
                    ->orWhereNotNull('growth_simulation.fixed_comp_at');
            })
            ->affiliatedFactories('growth_simulation');

        if (count($order) === 0) {
            $query->orderBy('growth_simulation.fixed_comp_at', 'DESC')
                ->orderBy('growth_simulation.fixed_start_at', 'DESC')
                ->orderBy('growth_simulation.factory_code', 'ASC')
                ->orderBy('growth_simulation.factory_species_code', 'ASC');
        }
        if (count($order) !== 0) {
            $query->orderBy($order['sort'], $order['order']);
        }

        return $query->paginate();
    }

    /**
     * 生産シミュレーション登録
     *
     * @param  array $params
     * @return \App\Models\Plan\GrowthSimulation
     */
    public function create(array $params): GrowthSimulation
    {
        $current_simulation_id = $this->model
            ->where('factory_code', $params['factory_code'])
            ->max('simulation_id') ?: 0;

        $params['simulation_id'] = $current_simulation_id + 1;
        return $this->model->create($params);
    }
}
