<?php

declare(strict_types=1);

namespace App\Services\FactoryProductionWork;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use App\Models\Master\FactorySpecies;
use App\Models\Plan\Collections\PanelStateCollection;
use App\Repositories\Plan\PanelStateRepository;

class ActivityResultsService
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repository
     * @return void
     */
    public function __construct(
        AuthManager $auth,
        Connection $db,
        PanelStateRepository $panel_state_repo
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->panel_state_repo = $panel_state_repo;
    }

    /**
     * 指定された工場、作業日に栽培中の工場品種の情報を取得
     *
     * @param  array $params
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getActivityResults(array $params): PanelStateCollection
    {
        return $this->panel_state_repo->getActivityResults($params);
    }

    /**
     * 指定された工場品種、作業日のパネル状況を取得
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  array $params
     */
    public function getActivityPanels(FactorySpecies $factory_species, array $params)
    {
        return $this->panel_state_repo->getActivityPanels($factory_species, $params);
    }

    /**
     * パネル状況の更新
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  array $params
     * @return void
     */
    public function update(FactorySpecies $factory_species, array $params): void
    {
        $this->db->transaction(function () use ($factory_species, $params) {
            foreach ($params['panel_id'] as $idx => $panel_id) {
                $this->panel_state_repo->updatePanelStatus([
                    'factory_code' => $factory_species->factory_code,
                    'panel_id' => $panel_id,
                    'bed_row' => $params['row'],
                    'bed_column' => $params['column'],
                    'panel_status' => $params['panel_status'][$idx],
                    'using_hole_count' => $params['using_hole_count'][$idx] ?: 0,
                    'updated_by' => $this->auth->user()->user_code
                ]);
            }
        });
    }
}
