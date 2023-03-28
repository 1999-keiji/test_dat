<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Collections\FactoryCollection;
use App\Models\Master\Collections\FactoryGrowingStageCollection;
use App\Models\Plan\Collections\ArrangementDetailStateCollection;
use App\Models\Plan\Collections\ArrangementStateCollection;
use App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection;
use App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection;
use App\Repositories\Master\EndUserRepository;
use App\Repositories\Master\EndUserFactoryRepository;
use App\Repositories\Master\FactoryRepository;
use App\Repositories\Master\FactoryBedRepository;
use App\Repositories\Master\FactoryColumnRepository;
use App\Repositories\Master\FactoryWarehouseRepository;
use App\Repositories\Master\FactoryWorkingDayRepository;
use App\Repositories\Master\WarehouseRepository;
use App\ValueObjects\Enum\DisplayKubun;

class FactoryService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \App\Repositories\Master\FactoryRepository
     */
    private $factory_repo;

    /**
     * @var \App\Repositories\Master\FactoryWorkingDayRepository
     */
    private $factory_working_day_repo;

    /**
     * @var \App\Repositories\Master\FactoryColumnRepository
     */
    private $factory_column_repo;

    /**
     * @var \App\Repositories\Master\FactoryBedRepository
     */
    private $factory_bed_repo;

    /**
     * @var \App\Repositories\Master\FactoryWarehouseRepository
     */
    private $factory_warehouse_repo;

    /**
     * @var \App\Repositories\Master\EndUserRepository
     */
    private $end_user_repo;

    /**
     * @var \App\Repositories\Master\EndUserFactoryRepository
     */
    private $end_user_factory_repo;

    /**
     * @var \App\Repositories\Master\WarehouseRepository
     */
    private $warehouse_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \App\Repositories\Master\FactoryRepository $factory_repo
     * @param  \App\Repositories\Master\FactoryWorkingDayRepository $factory_working_day_repo
     * @param  \App\Repositories\Master\FactoryColumnRepository $factory_column_repo
     * @param  \App\Repositories\Master\FactoryBedRepository $factory_bed_repo
     * @param  \App\Repositories\Master\FactoryWarehouseRepository $factory_warehouse_repo
     * @param  \App\Repositories\Master\EndUserRepository $end_user_repo
     * @param  \App\Repositories\Master\EndUserFactpryRepository $end_user_factory_repo
     * @param  \App\Repositories\Master\WarehouseRepository $warehouse_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        AuthManager $auth,
        FactoryRepository $factory_repo,
        FactoryWorkingDayRepository $factory_working_day_repo,
        FactoryColumnRepository $factory_column_repo,
        FactoryBedRepository $factory_bed_repo,
        FactoryWarehouseRepository $factory_warehouse_repo,
        EndUserRepository $end_user_repo,
        EndUserFactoryRepository $end_user_factory_repo,
        WarehouseRepository $warehouse_repo
    ) {
        $this->db = $db;
        $this->auth = $auth;
        $this->factory_repo = $factory_repo;
        $this->factory_working_day_repo = $factory_working_day_repo;
        $this->factory_column_repo = $factory_column_repo;
        $this->factory_bed_repo = $factory_bed_repo;
        $this->factory_warehouse_repo = $factory_warehouse_repo;
        $this->end_user_repo = $end_user_repo;
        $this->end_user_factory_repo = $end_user_factory_repo;
        $this->warehouse_repo = $warehouse_repo;
    }

    /**
     * 工場マスタの取得
     *
     * @param  string $factory_code
     * @return \App\Models\Master\Factory
     */
    public function find(string $factory_code): Factory
    {
        return $this->factory_repo->find($factory_code);
    }

    /**
     * すべての工場マスタを取得
     * ただし、工場所属のユーザの場合は、所属工場の情報のみ取得可
     *
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function getAllFactories(): FactoryCollection
    {
        if ($this->auth->user()->belongsToFactory()) {
            return $this->auth->user()->getAffilicatedFactories();
        }

        return $this->factory_repo->all();
    }

    /**
     * 工場マスタを条件に応じて検索
     *
     * @param  array $params 検索条件
     * @param  int $page 表示ページ
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchFactories(array $params, int $page): LengthAwarePaginator
    {
        $factories = $this->factory_repo->search($params);
        if ($page > $factories->lastPage() && $factories->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $factories;
    }

    /**
     * すべての工場マスタを取得
     *
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function getAll(): FactoryCollection
    {
        return $this->factory_repo->all();
    }

    /**
     * 工場マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Factory
     */
    public function createFactory(array $params): Factory
    {
        return $this->db->transaction(function () use ($params) {
            $factory = $this->factory_repo->create($this->setFactoryParam($params));
            $this->factory_working_day_repo->saveFactoryWorkingDays($factory, $params['working_days']);

            $factory_name = last(explode(' ', $factory->factory_name));
            $warehouse = $this->warehouse_repo->create([
                'warehouse_code' => $factory->symbolic_code.'001',
                'warehouse_name' => implode(' ', [
                    $factory_name,
                    config('constant.master.factory.default_warehouse_name')
                ]),
                'warehouse_abbreviation' =>  implode(' ', [
                    str_replace(trans('view.master.factories.factory'), '', $factory_name),
                    config('constant.master.factory.default_warehouse_name')
                ]),
                'country_code' => $factory->country_code,
                'postal_code' => $factory->postal_code,
                'prefecture_code' => $factory->prefecture_code,
                'address' => $factory->address,
                'address2' => $factory->address2,
                'address3' => $factory->address3,
                'abroad_address' => $factory->abroad_address,
                'abroad_address2' => $factory->abroad_address2,
                'abroad_address3' => $factory->abroad_address3,
                'phone_number' => $factory->phone_number,
                'extension_number' => $factory->extension_number,
                'fax_number' => $factory->fax_number,
                'mail_address' => $factory->mail_address
            ]);

            $this->factory_warehouse_repo->create($factory->factory_code, $warehouse->warehouse_code, 1);
            $this->end_user_factory_repo->linkEndUsers($factory, $this->end_user_repo->getExistingEndUsers());

            return $factory;
        });
    }

    /**
     * 工場マスタの更新
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return \App\Models\Master\Factory
     */
    public function updateFactory(Factory $factory, array $params): Factory
    {
        return $this->db->transaction(function () use ($factory, $params) {
            $factory = $this->factory_repo->update($factory, $this->setFactoryParam($params));
            $this->factory_working_day_repo->saveFactoryWorkingDays($factory, $params['working_days']);

            return $factory;
        });
    }

    /**
     * 工場マスタ登録/更新情報を生成する
     *
     * @param  array $params
     * @return array
     */
    private function setFactoryParam(array $params):array
    {
        $params = array_filter($params, 'is_not_null');
        unset($params['working_days'], $params['overwrite_on_invoice']);

        if (! array_key_exists('invoice_corporation_name', $params)) {
            $params['invoice_corporation_name']  = $params['factory_name'];
        }
        if (! array_key_exists('invoice_postal_code', $params)) {
            $params['invoice_postal_code'] = $params['postal_code'];
        }
        if (! array_key_exists('invoice_address', $params)) {
            $params['invoice_address'] = $params['address'];
        }
        if (! array_key_exists('invoice_phone_number', $params)) {
            $params['invoice_phone_number'] = $params['phone_number'];
        }
        if ((! array_key_exists('invoice_fax_number', $params)) && array_key_exists('fax_number', $params)) {
            $params['invoice_fax_number'] = $params['fax_number'];
        }

        if (! array_key_exists('needs_to_slide_printing_shipping_date', $params)) {
            $params['needs_to_slide_printing_shipping_date'] = false;
        }

        return $params;
    }

    /**
     * 工場マスタの削除
     *
     * @param  \App\Models\Master\Factory $factory
     * @return void
     */
    public function deleteFactory(Factory $factory)
    {
        $this->db->transaction(function () use ($factory) {
            $factory->factory_working_days->each(function ($fwd) {
                $fwd->delete();
            });

            $factory->factory_columns->each(function ($fc) {
                $fc->delete();
            });

            $factory->factory_beds->each(function ($fb) {
                $fb->delete();
            });

            $factory->factory_warehouses->each(function ($fw) {
                $fw->delete();
            });

            $factory->factory_panels->each(function ($fp) {
                $fp->delete();
            });

            $factory->factory_cycle_patterns->each(function ($fcp) {
                $fcp->factory_cycle_pattern_items->each(function ($fcpi) {
                    $fcpi->delete();
                });

                $fcp->delete();
            });

            $factory->factory_rest->each(function ($fr) {
                $fr->delete();
            });

            $factory->end_user_factories->each(function ($euf) {
                $euf->delete();
            });

            $factory->user_factories->each(function ($uf) {
                $uf->delete();
            });

            $factory->delete();
        });
    }

    /**
     * エンドユーザに未紐づけの工場を取得
     *
     * @param  \App\Models\Master\EndUser $end_user
     * @return \App\Models\Master\Collections\FactoryCollection
     */
    public function getNotLinkedEndUserFactories(EndUser $end_user): FactoryCollection
    {
        return $this->getAllFactories()->getNotLinkedEndUserFactories($end_user);
    }

    /**
     * 栽培パネル配置図を反映した状態で工場レイアウト情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\PlannedArrangementStatusWorkCollection $planned_arrangement_status_works
     * @return array
     */
    public function getFactroyLayoutWithAllocation(
        Factory $factory,
        DisplayKubun $display_kubun,
        FactoryGrowingStageCollection $factory_growing_stages,
        PlannedArrangementStatusWorkCollection $planned_arrangement_status_works
    ): array {
        return [
            'circulations' => $factory->factory_columns->circulations(),
            'columns' => $factory->factory_columns->columns(),
            'rows' => $factory->factory_beds->rows(),
            'beds' => $factory->factory_beds->allocatePanel(
                $display_kubun,
                $factory_growing_stages,
                $planned_arrangement_status_works,
                $factory->factory_columns->toMapOfColumnAndCirculation()
            )
        ];
    }

    /**
     * 栽培パネル配置図詳細を反映した状態で工場レイアウト情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection
     *          $planned_arrangement_detail_status_works
     * @param  int $floor
     * @return array
     */
    public function getFactroyLayoutWithDetailAllocation(
        Factory $factory,
        DisplayKubun $display_kubun,
        FactoryGrowingStageCollection $factory_growing_stages,
        PlannedArrangementDetailStatusWorkCollection $planned_arrangement_detail_status_works,
        int $floor
    ): array {
        return [
            'floor' => $floor,
            'circulations' => $factory->factory_columns->circulations(),
            'columns' => $factory->factory_columns->columns(),
            'rows' => $factory->factory_beds->allocatePanelDetail(
                $display_kubun,
                $factory_growing_stages,
                $planned_arrangement_detail_status_works,
                $floor,
                $factory->factory_columns->toMapOfColumnAndCirculation()
            )
        ];
    }

    /**
     * 栽培パネル配置図詳細を反映した状態で工場レイアウト情報を取得
     * ※ 帳票出力用
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\ValueObjects\Enum\DisplayKubun $display_kubun
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\PlannedArrangementDetailStatusWorkCollection
     *          $planned_arrangement_detail_status_works
     * @param  int $floor
     * @return array
     */
    public function getFactroyLayoutWithDetailAllocationToExport(
        Factory $factory,
        DisplayKubun $display_kubun,
        FactoryGrowingStageCollection $factory_growing_stages,
        PlannedArrangementDetailStatusWorkCollection $planned_arrangement_detail_status_works,
        int $floor
    ): array {
        return [
            'floor' => $floor,
            'circulations' => $factory->factory_columns->circulations(),
            'columns' => $factory->factory_columns->columns(),
            'rows' => $factory->factory_beds->allocatePanelDetailToExport(
                $display_kubun,
                $factory_growing_stages,
                $planned_arrangement_detail_status_works,
                $floor,
                $factory->factory_columns->toMapOfColumnAndCirculation()
            )
        ];
    }

    /**
     * 配置状況データを反映した状態で工場レイアウト情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\ArrangementStateCollection $arrangement_states
     * @return array
     */
    public function getFactroyLayoutWithBedStates(
        Factory $factory,
        FactoryGrowingStageCollection $factory_growing_stages,
        ArrangementStateCollection $arrangement_states
    ): array {
        return [
            'circulations' => $factory->factory_columns->circulations(),
            'columns' => $factory->factory_columns->columns(),
            'rows' => $factory->factory_beds->rows(),
            'beds' => $factory->factory_beds->allocateBedStates(
                $factory_growing_stages,
                $arrangement_states,
                $factory->factory_columns->toMapOfColumnAndCirculation()
            )
        ];
    }

    /**
     * 配置詳細状況データを反映した状態で工場レイアウト情報を取得
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\ArrangementDetailStateCollection $arrangement_detail_states
     * @param  int $floor
     * @return array
     */
    public function getFactroyLayoutWithDetailBedStates(
        Factory $factory,
        FactoryGrowingStageCollection $factory_growing_stages,
        ArrangementDetailStateCollection $arrangement_detail_states,
        int $floor
    ): array {
        return [
            'floor' => $floor,
            'circulations' => $factory->factory_columns->circulations(),
            'columns' => $factory->factory_columns->columns(),
            'rows' => $factory->factory_beds->allocateDetailBedStates(
                $factory_growing_stages,
                $arrangement_detail_states,
                $floor,
                $factory->factory_columns->toMapOfColumnAndCirculation()
            )
        ];
    }

    /**
     * 配置詳細状況データを反映した状態で工場レイアウト情報を取得
     * ※ 帳票出力用
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\Collections\FactoryGrowingStageCollection $factory_growing_stages
     * @param  \App\Models\Plan\Collections\ArrangementDetailStateCollection $arrangement_detail_states
     * @param  int $floor
     * @return array
     */
    public function getFactroyLayoutWithDetailBedStatesToExport(
        Factory $factory,
        FactoryGrowingStageCollection $factory_growing_stages,
        ArrangementDetailStateCollection $arrangement_detail_states,
        int $floor
    ): array {
        return [
            'floor' => $floor,
            'circulations' => $factory->factory_columns->circulations(),
            'columns' => $factory->factory_columns->columns(),
            'rows' => $factory->factory_beds->allocateDetailBedStatesToExport(
                $factory_growing_stages,
                $arrangement_detail_states,
                $floor,
                $factory->factory_columns->toMapOfColumnAndCirculation()
            )
        ];
    }

    /**
     * 工場レイアウトの更新
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return void
     */
    public function updateFactoryBeds(Factory $factory, array $params): void
    {
        $this->db->transaction(function () use ($factory, $params) {
            $this->factory_repo->update($factory, [
                'number_of_floors' => $params['number_of_floors'],
                'number_of_rows' => head(array_keys(head($params['factory_beds']))),
                'number_of_columns' => $params['number_of_columns'],
                'number_of_circulation' => $params['number_of_circulation'],
                'updated_at' => $params['updated_at']
            ]);

            $circulations = [];
            foreach ($params['circulations'] as $circulation => $count) {
                $circulations = array_merge($circulations, array_fill(0, (int)$count, $circulation));
            }

            $factory_columns = [];
            foreach ($params['factory_columns'] as $column => $fc) {
                $factory_columns[] = [
                    'factory_code' => $factory->factory_code,
                    'column' => $column,
                    'column_name' => $fc['column_name'],
                    'circulation' => $circulations[$column - 1]
                ];
            }

            $this->factory_column_repo->delete($factory);
            $this->factory_column_repo->insert($factory_columns);

            $factory_beds = [];
            foreach ($params['factory_beds'] as $floor => $rows) {
                foreach ($rows as $row => $columns) {
                    foreach ($columns as $column => $fb) {
                        $factory_beds[] = [
                            'factory_code' => $factory->factory_code,
                            'row' => $row,
                            'column' => $column,
                            'floor' => $floor,
                            'x_coordinate_panel' => $fb['x_coordinate_panel'],
                            'y_coordinate_panel' => $fb['y_coordinate_panel'],
                            'irradiation' => $fb['irradiation'] ?: ''
                        ];
                    }
                }
            }

            $this->factory_bed_repo->delete($factory);
            $this->factory_bed_repo->insert($factory_beds);
        });
    }
}
