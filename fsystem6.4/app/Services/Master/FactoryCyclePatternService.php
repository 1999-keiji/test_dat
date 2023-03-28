<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use App\Models\Master\Factory;
use App\Models\Master\FactoryCyclePattern;
use App\Repositories\Master\FactoryCyclePatternRepository;
use App\Repositories\Master\FactoryCyclePatternItemRepository;

class FactoryCyclePatternService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\FactoryCyclePatternRepository
     */
    private $factory_cycle_pattern_repo;

    /**
     * @var \App\Repositories\Master\FactoryCyclePatternItemRepository
     */
    private $factory_cycle_pattern_item_repo;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\FactoryCyclePatternRepository $factory_cycle_pattern_repo
     * @return void
     */
    public function __construct(
        Connection $db,
        FactoryCyclePatternRepository $factory_cycle_pattern_repo,
        FactoryCyclePatternItemRepository $factory_cycle_pattern_item_repo
    ) {
        $this->db = $db;
        $this->factory_cycle_pattern_repo = $factory_cycle_pattern_repo;
        $this->factory_cycle_pattern_item_repo = $factory_cycle_pattern_item_repo;
    }

    /**
     * 工場サイクルパターンの更新
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return void
     */
    public function saveCyclePattern(Factory $factory, array $params): void
    {
        $this->db->transaction(function () use ($factory, $params) {
            $factory_cycle_pattern = null;
            if (! $params['sequence_number']) {
                $current_sequence_number = $factory->factory_cycle_patterns->max('sequence_number') ?: 0;
                $factory_cycle_pattern = $this->factory_cycle_pattern_repo->create([
                    'factory_code' => $factory->factory_code,
                    'sequence_number' => $current_sequence_number + 1,
                    'cycle_pattern_name' => $params['cycle_pattern_name']
                ]);
            } else {
                $factory_cycle_pattern = $this->factory_cycle_pattern_repo->find([
                    'factory_code' => $factory->factory_code,
                    'sequence_number' => $params['sequence_number'],
                ]);

                $this->factory_cycle_pattern_repo->update($factory_cycle_pattern, $params['cycle_pattern_name']);
            }

            $this->factory_cycle_pattern_item_repo->deleteFactoryCyclePatternItems($factory_cycle_pattern);
            foreach ($params['pattern'] as $idx => $pattern) {
                foreach ($params['number_of_panels'][$idx] as $day_of_the_week => $number_of_panels) {
                    $this->factory_cycle_pattern_item_repo->create([
                        'factory_code' => $factory->factory_code,
                        'cycle_pattern_sequence_number' => $factory_cycle_pattern->sequence_number,
                        'pattern' => $pattern,
                        'day_of_the_week' => $day_of_the_week,
                        'number_of_panels' => $number_of_panels
                    ]);
                }
            }
        });
    }

    /**
     * 工場サイクルパターンの更新
     *
     * @param  \App\Models\Master\FactoryCyclePattern $factory_cycle_pattern
     * @return void
     */
    public function deleteCyclePattern(FactoryCyclePattern $factory_cycle_pattern): void
    {
        $this->db->transaction(function () use ($factory_cycle_pattern) {
            $this->factory_cycle_pattern_item_repo->deleteFactoryCyclePatternItems($factory_cycle_pattern);
            $factory_cycle_pattern->delete();
        });
    }

    /**
     * 工場コードとサイクルパターン連番に紐づく工場サイクルパターンを取得
     *
     * @param  string $factory_code
     * @param  int $cycle_pattern_sequence_number
     * @return array
     */
    public function getFactoryCyclePatternItemsForApi(string $factory_code, int $cycle_pattern_sequence_number): array
    {
        $factory_cycle_pattern = $this->factory_cycle_pattern_repo->find([
            'factory_code'    => $factory_code,
            'sequence_number' => $cycle_pattern_sequence_number
        ]);

        return [
            'cycle_pattern_name' => $factory_cycle_pattern->cycle_pattern_name,
            'factory_cycle_pattern_items' => $factory_cycle_pattern
                ->factory_cycle_pattern_items
                ->toResponseForSearchingApi(),
            'is_deletable' => $factory_cycle_pattern->isDeletable()
        ];
    }
}
