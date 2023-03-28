<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\Models\Master\Collections\FactorySpeciesCollection;
use App\Repositories\Master\FactorySpeciesRepository;
use App\Repositories\Master\FactoryGrowingStageRepository;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\WorkingDate;
use App\ValueObjects\Enum\GrowingStage;
use App\ValueObjects\Enum\InputChange;

class FactorySpeciesService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactorySpeciesRepository
     */
    private $factory_species_repo;

    /**
     * @var \App\Repositories\Master\FactoryGrowingStageRepository
     */
    private $factory_growing_stage_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\FactorySpeciesRepository $factory_product_repo
     * @param  \App\Repositories\Master\FactoryGrowingStageRepository $factory_growing_stage_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        FactorySpeciesRepository $factory_species_repo,
        FactoryGrowingStageRepository $factory_growing_stage_repo
    ) {
        $this->db = $db;
        $this->factory_species_repo = $factory_species_repo;
        $this->factory_growing_stage_repo = $factory_growing_stage_repo;
    }

    /**
     * 工場に紐づく工場取扱品種マスタを取得
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\FactorySpeciesCollection
     */
    public function getFactorySpecies(array $params): FactorySpeciesCollection
    {
        $params = ['factory_code' => $params['factory_code'] ?? null];
        return $this->factory_species_repo->getFactorySpecies($params);
    }

    /**
     * 工場品種マスタの登録
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return \App\Models\Master\FactorySpecies
     */
    public function createFactorySpecies(Factory $factory, array $params): FactorySpecies
    {
        return $this->db->transaction(function () use ($factory, $params) {
            $factory_species = $this->factory_species_repo->create([
                'factory_code' => $factory->factory_code,
                'factory_species_code' => $params['factory_species_code'],
                'species_code' => $params['species_code'],
                'factory_species_name' => $params['factory_species_name'],
                'weight' => $params['weight'],
                'remark' => $params['remark'] ?: ''
            ]);

            foreach ($params['growing_stage'] as $sequence_number => $growing_stage) {
                $this->factory_growing_stage_repo->create([
                    'factory_code' => $factory->factory_code,
                    'factory_species_code' => $factory_species->factory_species_code,
                    'sequence_number' => $sequence_number,
                    'growing_stage' => $growing_stage,
                    'growing_stage_name' => $params['growing_stage_name'][$sequence_number],
                    'label_color' => str_replace('#', '', ($params['label_color'][$sequence_number] ?? '')),
                    'growing_term' => $params['growing_term'][$sequence_number],
                    'number_of_holes' => $params['number_of_holes'][$sequence_number],
                    'yield_rate' => ($params['yield_rate'][$sequence_number] ?? 100) / 100,
                    'cycle_pattern_sequence_number'
                        => $params['cycle_pattern_sequence_number'][$sequence_number] ?? null
                ]);
            }

            return $factory_species;
        });
    }

    /**
     * 工場品種マスタの更新
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @param  array $params
     * @return \App\Models\Master\FactorySpecies
     */
    public function updateFactorySpecies(
        Factory $factory,
        FactorySpecies $factory_species,
        array $params
    ): FactorySpecies {
        return $this->db->transaction(function () use ($factory, $factory_species, $params) {
            $factory_species = $this->factory_species_repo->update($factory_species, [
                'factory_species_name' => $params['factory_species_name'],
                'weight' => $params['weight'],
                'can_select_on_simulation' => $params['can_select_on_simulation'],
                'remark' => $params['remark'] ?: ''
            ]);

            $this->factory_growing_stage_repo->delete($factory_species);
            foreach ($params['growing_stage'] as $sequence_number => $growing_stage) {
                $this->factory_growing_stage_repo->create([
                    'factory_code' => $factory->factory_code,
                    'factory_species_code' => $factory_species->factory_species_code,
                    'sequence_number' => $sequence_number,
                    'growing_stage' => $growing_stage,
                    'growing_stage_name' => $params['growing_stage_name'][$sequence_number],
                    'label_color' => str_replace('#', '', ($params['label_color'][$sequence_number] ?? '')),
                    'growing_term' => $params['growing_term'][$sequence_number],
                    'number_of_holes' => $params['number_of_holes'][$sequence_number],
                    'yield_rate' => ($params['yield_rate'][$sequence_number] ?? 100) / 100,
                    'cycle_pattern_sequence_number'
                        => $params['cycle_pattern_sequence_number'][$sequence_number] ?? null
                ]);
            }

            return $factory_species;
        });
    }

    /**
     * 工場品種マスタの削除
     *
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return void
     */
    public function deleteFactorySpecies(FactorySpecies $factory_species): void
    {
        $this->db->transaction(function () use ($factory_species) {
            $this->factory_growing_stage_repo->delete($factory_species);
            $factory_species->delete();
        });
    }

    /**
     * API用に工場取扱品種マスタを検索
     *
     * @param  array $params
     * @return array
     */
    public function getFactorySpeciesForSearchingApi(array $params): array
    {
        return $this->factory_species_repo->getFactorySpecies([
            'factory_code' => $params['factory_code'] ?? null
        ])
            ->toResponseForSearchingApi(
                (bool)($params['can_select_on_simulation'] ?? false)
            );
    }

    /**
     * 指定された工場コードに紐づく品種を取得
     *
     * @param  array
     * @return array
     */
    public function getSpeciesWithFactoryCodeForSearchingApi(array $params): array
    {
        $params = [
            'factory_code' => $params['factory_code'] ?? null,
        ];

        return $this->factory_species_repo
            ->getSpeciesWithFactoryCode($params)
            ->toResponseForSearchingApi(false);
    }

    /**
     * 日付と株数を基に生育用の数値をシミュレーション
     *
     * @param  array $params
     * @return array
     */
    public function simulateGrowing(array $params): array
    {
        $factory_species = $this->factory_species_repo->findFactorySpecies($params);

        $factory = $factory_species->factory;
        $factory_growing_stages = $factory_species->factory_growing_stages;
        $panels_average_list = $factory_growing_stages->getAverageOfMovingPanelsOnStartOfWeek($factory);

        $floor = function ($number) {
            return (int)($number >= 0 ? floor($number) : ceil($number));
        };
        $ceil = function ($number) {
            return (int)($number >= 0 ? ceil($number) : floor($number));
        };
        $to_even = function ($number) {
            if ($number % 2 === 0) {
                return (int)$number;
            }

            return (int)($number >= 0 ? ($number + 1) : ($number - 1));
        };

        $growth_simulation_items = [];
        if ((int)$params['input_change'] === InputChange::HARVESTING) {
            $date = $params['date'];
            $stock_number = (int)$params['stock_number'];

            foreach ($factory_growing_stages->reverse() as $idx => $fgs) {
                $date = $fgs->getGrowingStage()->getStageChangedDate($factory, $date, $fgs->growing_term);
                $stock_number = $floor($stock_number / $fgs->yield_rate);

                $panel_number = $ceil($stock_number / $fgs->number_of_holes);
                if ($fgs->growing_stage !== GrowingStage::SEEDING) {
                    $panel_number = $to_even($panel_number);
                }

                $bed_number = null;
                if ($fgs->cycle_pattern_sequence_number) {
                    $bed_number = 0;
                    if ($panels_average_list[$fgs->cycle_pattern_sequence_number] !== 0) {
                        $bed_number = $ceil($panel_number / $panels_average_list[$fgs->cycle_pattern_sequence_number]);
                    }
                }

                $growth_simulation_items[$idx] = [
                    'date' => $date,
                    'bed_number' => $bed_number,
                    'panel_number' => $panel_number,
                    'stock_number' => $panel_number * $fgs->number_of_holes
                ];
            }

            $growth_simulation_items[$factory_growing_stages->count()] = [
                'date' => $params['date'],
                'bed_number' => null,
                'panel_number' => null,
                'stock_number' => (int)$params['stock_number']
            ];
        }

        if ((int)$params['input_change'] === InputChange::SEEDING) {
            $date = $params['date'];
            $stock_number = (int)$params['stock_number'];

            $stage_changing_dates = [];
            foreach ($factory_growing_stages as $idx => $fgs) {
                $date = $fgs->getGrowingStage()->getNextGrowthStageDate($factory, $date, $fgs->growing_term);
                if ($fgs->growing_stage !== GrowingStage::SEEDING) {
                    $stock_number = $floor($stock_number * $factory_growing_stages[$idx - 1]->yield_rate);
                }

                $panel_number = $ceil($stock_number / $fgs->number_of_holes);
                if ($fgs->growing_stage !== GrowingStage::SEEDING) {
                    $panel_number = $to_even($panel_number);
                }

                $bed_number = null;
                if ($fgs->cycle_pattern_sequence_number) {
                    $bed_number = 0;
                    if ($panels_average_list[$fgs->cycle_pattern_sequence_number] !== 0) {
                        $bed_number = $ceil($panel_number / $panels_average_list[$fgs->cycle_pattern_sequence_number]);
                    }
                }

                $growth_simulation_items[$idx] = [
                    'date' => last($stage_changing_dates) ?: $params['date'],
                    'bed_number' => $bed_number,
                    'panel_number' => $panel_number,
                    'stock_number' => $panel_number * $fgs->number_of_holes
                ];

                $stage_changing_dates[] = $date;
            }

            $growth_simulation_items[$factory_growing_stages->count()] = [
                'date' => last($stage_changing_dates),
                'bed_number' => null,
                'panel_number' => null,
                'stock_number' => (int)($growth_simulation_items[$idx]['stock_number'] * $fgs->yield_rate)
            ];
        }

        ksort($growth_simulation_items);
        return $growth_simulation_items;
    }
}
