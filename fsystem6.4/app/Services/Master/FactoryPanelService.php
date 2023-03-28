<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Models\Master\Factory;
use App\Models\Master\FactoryPanel;
use App\Repositories\Master\FactoryPanelRepository;

class FactoryPanelService
{
    /**
     * @var \App\Repositories\Master\FactoryPanelRepository
     */
    private $factory_panel_repo;

    /**
     * @param  \App\Repositories\Master\FactoryPanelRepository $factory_panel_repo
     * @return void
     */
    public function __construct(FactoryPanelRepository $factory_panel_repo)
    {
        $this->factory_panel_repo = $factory_panel_repo;
    }

    /**
     * 工場パネルの登録
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  int $number_of_holes
     * @return \App\Models\Master\FactoryPanel
     */
    public function createFactoryPanel(Factory $factory, int $number_of_holes): FactoryPanel
    {
        return $this->factory_panel_repo->create($factory->factory_code, $number_of_holes);
    }

    /**
     * 工場パネルの削除
     *
     * @param  \App\Models\Master\FactoryPanel $factory_panel
     * @return void
     */
    public function deleteFactoryPanel(FactoryPanel $factory_panel): void
    {
        $factory_panel->delete();
    }
}
