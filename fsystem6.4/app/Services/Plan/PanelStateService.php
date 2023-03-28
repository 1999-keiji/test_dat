<?php

declare(strict_types=1);

namespace App\Services\Plan;

use Cake\Chronos\Chronos;
use Maatwebsite\Excel\Excel;
use App\Models\Master\Factory;
use App\Models\Order\Collections\OrderCollection;
use App\Models\Plan\GrowthSimulation;
use App\Models\Plan\Collections\CropCollection;
use App\Models\Plan\Collections\ForecastedProductRateCollection;
use App\Models\Plan\Collections\PanelStateCollection;
use App\Repositories\Plan\PanelStateRepository;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\WorkingDate;
use App\ValueObjects\Enum\DisplayKubun;
use App\ValueObjects\Enum\GrowingStage;

class PanelStateService
{
    /**
     * @var \Maatwebsite\Excel\Excel
     */
    private $excel;

    /**
     * @var \App\Repositories\Plan\PanelStateRepository
     */
    private $panel_state_repo;

    /**
     * @param  \Maatwebsite\Excel\Excel $excel
     * @param  \App\Repositories\Plan\PanelStateRepository $panel_state_repo
     * @return void
     */
    public function __construct(Excel $excel, PanelStateRepository $panel_state_repo)
    {
        $this->excel = $excel;
        $this->panel_state_repo = $panel_state_repo;
    }

    /**
     * 指定された品種、収穫期間に応じて収穫株数を取得
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Plan\GrowthSimulation $growth_simulation
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function getHarvestingStockQuantitiesBySpeciesAndHarvestingDate(
        array $params,
        HarvestingDate $harvesting_date,
        ?GrowthSimulation $growth_simulation = null
    ): PanelStateCollection {
        $harvesting_date_term = [];
        if ($params['display_term'] === 'date') {
            $harvesting_dates = $harvesting_date->toListOfDate((int)$params['week_term']);
            $harvesting_date_term = [
                'from' => head($harvesting_dates),
                'to' => last($harvesting_dates),
            ];
        }
        if ($params['display_term'] === 'month') {
            $harvesting_months = $harvesting_date->toListOfMonth();
            $harvesting_date_term = [
                'from' => head($harvesting_months)->firstOfMonth(),
                'to' => last($harvesting_months)->lastOfMonth()
            ];
        }

        $panel_states = $this->panel_state_repo
            ->getHarvestingStockQuantitiesBySpeciesAndHarvestingDate(
                $params,
                $harvesting_date_term,
                $growth_simulation
            );

        $display_kubun = $params['display_kubun'] ?? null;
        if (is_null($growth_simulation) || (int)$params['display_kubun'] !== DisplayKubun::PROCESS) {
            return $panel_states;
        }

        $simulated_harvesting_quantities = $growth_simulation->growth_simulation_items
            ->filterHarvestingQuantities($params['display_term'], $growth_simulation->factory_species);

        return $panel_states->map(function ($ps) use ($params, $simulated_harvesting_quantities) {
            $key = $params['display_term'] === 'date' ? 'harvesting_date' : 'harvesting_month';
            if (array_key_exists($ps->{$key}, $simulated_harvesting_quantities)) {
                $ps->harvesting_quantity += $simulated_harvesting_quantities[$ps->{$key}]['quantity'];
                $ps->harvesting_weight += $simulated_harvesting_quantities[$ps->{$key}]['weight'];
            }

            return $ps;
        });
    }

    /**
     * 全工場の生販管理状況をサマライズ
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Plan\Collections\PanelStateCollection $panel_states
     * @param  \App\Models\Plan\Collections\ForecastedProductRateCollection $forecasted_product_rates
     * @param  \App\Models\Plan\Collections\CropCollection $crops
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function summarizePerFactory(
        array $params,
        HarvestingDate $harvesting_date,
        PanelStateCollection $panel_states,
        ForecastedProductRateCollection $forecasted_product_rates,
        CropCollection $crops,
        OrderCollection $orders
    ): PanelStateCollection {
        if ($params['display_term'] === 'month') {
            return $this->summarizePerFactoryAndMonth(
                $harvesting_date,
                $panel_states,
                $forecasted_product_rates,
                $crops,
                $orders
            );
        }

        $factories = $panel_states->groupByFactory()
            ->map(function ($f) use ($params, $harvesting_date, $forecasted_product_rates, $crops, $orders) {
                $summary = $this->summarizeWithFactoryAndSpecies(
                    $params,
                    $harvesting_date,
                    new Factory((array)$f),
                    $f->panel_states,
                    $forecasted_product_rates->filterByFactory($f),
                    $crops->filterByFactory($f),
                    $orders->filterByFactory($f)->first()->packaging_styles ?? new OrderCollection()
                );

                $f->summary = new PanelStateCollection($summary);

                unset($f->panel_states);
                return $f;
            })
            ->all();

            return new PanelStateCollection($factories);
    }

    /**
     * 全工場の生販管理状況を年月単位でサマライズ
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Plan\Collections\PanelStateCollection $panel_states
     * @param  \App\Models\Plan\Collections\ForecastedProductRateCollection $forecasted_product_rates
     * @param  \App\Models\Plan\Collections\CropCollection $crops
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @return \App\Models\Plan\Collections\PanelStateCollection
     */
    public function summarizePerFactoryAndMonth(
        HarvestingDate $harvesting_date,
        PanelStateCollection $panel_states,
        ForecastedProductRateCollection $forecasted_product_rates,
        CropCollection $crops,
        OrderCollection $orders
    ): PanelStateCollection {
        $factories = $panel_states->groupByFactory()
            ->map(function ($f) use ($harvesting_date, $forecasted_product_rates, $crops, $orders) {
                $summary = $this->summarizeWithFactoryAndSpeciesAndMonth(
                    $harvesting_date,
                    $f->panel_states,
                    $forecasted_product_rates->filterByFactory($f),
                    $crops->filterByFactory($f),
                    $orders->filterByFactory($f)->first()->packaging_styles ?? new OrderCollection()
                );

                $f->summary = new PanelStateCollection($summary);

                unset($f->panel_states);
                return $f;
            })
            ->all();

        return new PanelStateCollection($factories);
    }

    /**
     * 工場-品種単位の生販管理状況をサマライズ
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Plan\Collections\PanelStateCollection $panel_states
     * @param  \App\Models\Plan\Collections\ForecastedProductRateCollection $forecasted_product_rates
     * @param  \App\Models\Plan\Collections\CropCollection $crops
     * @param  \App\Models\Stock\CarryOverStock $carry_over_stock
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @return array
     */
    public function summarizeWithFactoryAndSpecies(
        array $params,
        HarvestingDate $harvesting_date,
        Factory $factory,
        PanelStateCollection $panel_states,
        ForecastedProductRateCollection $forecasted_product_rates,
        CropCollection $crops,
        OrderCollection $orders
    ): array {
        if ($params['display_term'] === 'month') {
            return $this->summarizeWithFactoryAndSpeciesAndMonth(
                $harvesting_date,
                $panel_states,
                $forecasted_product_rates,
                $crops,
                $orders
            );
        }

        $prev_carry_over_stock_weight = $carry_over_stock_weigtht = $orders->sum('weight_of_carry_over_stock');

        $harvesting_quantities = $product_rates = $product_weights = [];
        $order_weights = $not_forecasted_order_flags = $only_fixed_order_flags = [];
        $gaps = $disposal_weights = $disposal_gap = $stocks = [];

        $harvesting_dates_list = $harvesting_date->toListOfDatePerWeek((int)$params['week_term']);
        foreach ($harvesting_dates_list as $week => $harvesting_dates) {
            $harvesting_quantities[$week] = $product_rates[$week] = $product_weights[$week] = [];
            $order_weights[$week] = $not_forecasted_order_flags[$week] = $only_fixed_order_flags[$week] = [];
            $gaps[$week] = $disposal_weights[$week] = $disposal_gap[$week] = $stocks[$week] = [];

            $latest_average_product_rate = $this->getLatestAverageRate(
                head($harvesting_dates),
                $factory,
                $forecasted_product_rates,
                $crops,
                $panel_states
            );

            foreach ($harvesting_dates as $idx => $hd) {
                $panel_state = $panel_states->filterByHarvestingDate($hd);
                $harvesting_quantity = (int)($panel_state->harvesting_quantity ?? 0);

                $fpr = $forecasted_product_rates->filterByHarvestingDate($hd);
                $has_productized = false;

                if (! is_null($fpr)) {
                    $has_productized = (bool)$fpr->has_productized;
                    $harvesting_quantity += $has_productized ?
                        ((int)$fpr->actual_crop_failure + (int)$fpr->actual_advanced_harvest) :
                        ((int)$fpr->crop_failure + (int)$fpr->advanced_harvest);
                }

                $crop = $crops->filterByHarvestingDate($hd);

                $product_rate = 0.00;
                if ($has_productized && $harvesting_quantity !== 0) {
                    $stock_quantity = $crop->product_quantity ?? $crop->crop_quantity ?? 0;
                    $product_rate = round(($stock_quantity / $harvesting_quantity * 100), 2);
                }
                if (! $has_productized) {
                    $product_rate = $fpr->product_rate ?? 0.00;
                }
                if ($product_rate === 0.00) {
                    $product_rate = $latest_average_product_rate;
                }

                $product_weight = 0;
                if (! is_null($crop)) {
                    $product_weight = (int)($has_productized ? $crop->product_weight : $crop->crop_weight);
                }
                if ($product_weight === 0 && ! is_null($panel_state)) {
                    $product_weight = $panel_state->harvesting_weight * ($product_rate / 100);
                }

                $date = $hd->format('Ymd');
                $harvesting_quantities[$week][$date] = $harvesting_quantity;
                $product_rates[$week][$date] = $product_rate;
                $product_weights[$week][$date] = convert_to_kilogram($product_weight);

                $order_weights[$week][$date] = $orders
                    ->map(function ($ps) use ($week, $hd) {
                        return $ps->factory_products->toSumOfWholePerDate($week, $hd->format('Ymd'), 'weight');
                    })
                    ->sum();

                $not_forecasted_order_flags[$week][$date] = $orders
                    ->reject(function ($ps) use ($week, $hd) {
                        return $ps->factory_products->notIncludeForecastedOrder($week, $hd->format('Ymd'));
                    })
                    ->isEmpty();

                $only_fixed_order_flags[$week][$date] = $orders
                    ->reject(function ($ps) use ($week, $hd) {
                        return $ps->factory_products->isOnlyFixedOrder($week, $hd->format('Ymd'));
                    })
                    ->isEmpty();

                $disposal_weights[$week][$date] = convert_to_kilogram((int)($fpr->disposal_weight ?? 0));

                $gaps[$week][$date] = $product_weights[$week][$date] - $order_weights[$week][$date];
                $disposal_gap[$week][$date] = $gaps[$week][$date] -
                    $disposal_weights[$week][$date] +
                    ($idx === 0 ? $carry_over_stock_weigtht : 0);

                $stocks[$week][$date] = array_sum($disposal_gap[$week]);
            }

            $harvesting_quantities[$week]['total'] = array_sum($harvesting_quantities[$week]);
            $product_weights[$week]['total'] = array_sum($product_weights[$week]);
            $order_weights[$week]['total'] = array_sum($order_weights[$week]);
            $disposal_weights[$week]['total'] = array_sum($disposal_weights[$week]);

            $carry_over_stock_weigtht = last($stocks[$week]);
        }

        return compact(
            'prev_carry_over_stock_weight',
            'harvesting_quantities',
            'product_rates',
            'product_weights',
            'order_weights',
            'not_forecasted_order_flags',
            'only_fixed_order_flags',
            'gaps',
            'disposal_weights',
            'stocks'
        );
    }

    /**
     * 工場-品種単位の生販管理状況を年月単位でサマライズ
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Plan\Collections\PanelStateCollection $panel_states
     * @param  \App\Models\Plan\Collections\ForecastedProductRateCollection $forecasted_product_rates
     * @param  \App\Models\Plan\Collections\CropCollection $crops
     * @param  \App\Models\Order\Collections\OrderCollection $orders
     * @return array
     */
    public function summarizeWithFactoryAndSpeciesAndMonth(
        HarvestingDate $harvesting_date,
        PanelStateCollection $panel_states,
        ForecastedProductRateCollection $forecasted_product_rates,
        CropCollection $crops,
        OrderCollection $orders
    ): array {
        $harvesting_quantities = $product_weights = $order_weights = [];
        $gaps = $stocks = $disposal_gap = $disposal_weights = [];

        foreach ($harvesting_date->toListOfMonth() as $hm) {
            $month = $hm->format('Ym');

            $product_weight = 0;
            if ($hm->willDisplayOrderOnTheMonth()) {
                $product_weight = $crops->filterByHarvestingMonth($month)->product_weight ?? 0;
            }
            if (! $hm->willDisplayOrderOnTheMonth()) {
                $product_weight = $crops->filterByHarvestingMonth($month)->crop_weight ?? 0;
            }

            $harvesting_quantities[$month] = $panel_states->filterByHarvestingMonth($month)->harvesting_quantity ?? 0;
            $product_weights[$month] = convert_to_kilogram($product_weight);

            $order_weights[$month] = $orders
                ->map(function ($ps) use ($month) {
                    return $ps->factory_products->toSumOfWholePerMonth($month, 'weight');
                })
                ->sum();

            $disposal_weights[$month] = convert_to_kilogram(
                (int)($forecasted_product_rates->filterByHarvestingMonth($month)->disposal_weight ?? 0)
            );

            $gaps[$month] = $product_weights[$month] - $order_weights[$month];
            $disposal_gap[$month] = $gaps[$month] - $disposal_weights[$month];
            $stocks[$month] = array_sum($disposal_gap);
        }

        return compact(
            'harvesting_quantities',
            'product_weights',
            'order_weights',
            'gaps',
            'disposal_weights',
            'stocks'
        );
    }

    /**
     * データが登録されている直近1週間の平均予想製品化率を取得
     *
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Plan\Collections\ForecastedProductRateCollection $forecasted_product_rates
     * @param  \App\Models\Plan\Collections\CropCollection $crops
     * @param  \App\Models\Plan\Collections\PanelStateCollection $panel_states
     * @return float
     */
    public function getLatestAverageRate(
        HarvestingDate $harvesting_date,
        Factory $factory,
        ForecastedProductRateCollection $forecasted_product_rates,
        CropCollection $crops,
        PanelStateCollection $panel_states
    ): float {
        $grouped_per_week = $forecasted_product_rates->getLatestGroupedForecastedProducts($harvesting_date);
        if (is_null($grouped_per_week)) {
            return 0.00;
        }

        $days_per_week = $factory->factory_working_days->count();
        $harvesting_dates = HarvestingDate::parse($grouped_per_week->first()->harvesting_date)->toListOfDate(1);

        $product_rates = [];
        foreach ($harvesting_dates as $hd) {
            $product_rate = null;

            $fpr = $grouped_per_week->filterByHarvestingDate($hd);
            if (! is_null($fpr)) {
                $harvesting_quantity = $panel_states->filterByHarvestingDate($hd)->harvesting_quantity ?? 0;
                if ((int)$harvesting_quantity !== 0) {
                    $has_productized = (bool)$fpr->has_productized;
                    $harvesting_quantity += $has_productized ?
                        ((int)$fpr->actual_crop_failure + (int)$fpr->actual_advanced_harvest) :
                        ((int)$fpr->crop_failure + (int)$fpr->advanced_harvest);

                    $stock_quantity = 0;
                    if ($crop = $crops->filterByHarvestingDate($hd)) {
                        $stock_quantity = (int)($has_productized ? $crop->product_quantity : $crop->crop_quantity);
                    }
                    if ((int)$stock_quantity !== 0 && (int)$harvesting_quantity !== 0) {
                        $product_rate = round(($stock_quantity / $harvesting_quantity * 100), 2);
                    }
                    if (is_null($product_rate)) {
                        $product_rate = $fpr->product_rate;
                    }
                }
            }

            if (! is_null($product_rate)) {
                $product_rates[] = (float)$product_rate;
            }
        }

        if (count($product_rates) !== $days_per_week) {
            return $this->getLatestAverageRate(
                $harvesting_date,
                $factory,
                $forecasted_product_rates->filterOldByHarvestingDate(head($harvesting_dates)),
                $crops,
                $panel_states
            );
        }

        return round(array_sum($product_rates) / $days_per_week, 2);
    }

    /**
     * 生産計画表の出力
     *
     * @param  \App\Models\Master\Factory $factory
     * @param  array $params
     * @return void
     */
    public function exportGrowthPlannedTable(Factory $factory, array $params)
    {
        $params['date_from'] = WorkingDate::parse($params['date_from']);
        $params['date_to'] = $params['date_from']->addWeeks($params['date_range']);
        $working_dates = $params['date_from']->toListOfDate((int)$params['date_range']);

        $growing_panel_count_list = $this->panel_state_repo->getGrowingPanelCountList($params);
        $harvesting_panel_count_list = $this->panel_state_repo->getHravestingPanelCountList($params);

        $factory_species_list = $factory->factory_species
            ->filter(function ($fs) use ($params, $growing_panel_count_list, $harvesting_panel_count_list) {
                if ($growing_panel_count_list->filterByFactorySpecies($fs)->isEmpty() &&
                    $harvesting_panel_count_list->filterByFactorySpecies($fs)) {
                    return false;
                }

                $factory_species_code = $params['factory_species_code'] ?? null;
                if (is_null($factory_species_code)) {
                    return true;
                }

                return $fs->factory_species_code === $factory_species_code;
            })
            ->map(function ($fs) use ($factory, $growing_panel_count_list, $harvesting_panel_count_list) {
                $filtered = $growing_panel_count_list->filterByFactorySpecies($fs);

                $seeding_stage = $fs->factory_growing_stages->filterSeeding();
                $seeding_tray_count_list = new PanelStateCollection();

                foreach ($filtered->filterJustAfterSeeding() as $ps) {
                    $seeding_date = $ps
                        ->getGrowingStage()
                        ->getStageChangedDate($factory, $ps->date, $seeding_stage->growing_term);

                    $tray_count = (int)(ceil(
                        ceil(
                            ($ps->panel_count * $ps->number_of_holes) / $seeding_stage->yield_rate
                        ) / $seeding_stage->number_of_holes
                    ));

                    $seeding_tray_count_list->push((object)[
                        'date' => $seeding_date,
                        'tray_count' => $tray_count
                    ]);
                }

                $fs->seeding_stage = (object)[
                    'growing_stage' => $seeding_stage->growing_stage,
                    'growing_stage_name' => $seeding_stage->growing_stage_name,
                    'number_of_holes' => $seeding_stage->number_of_holes,
                    'tray_count_list' => $seeding_tray_count_list
                ];

                $growing_stages = [];
                foreach ($fs->factory_growing_stages->exceptSeeding() as $fgs) {
                    $growing_stages[] = (object)[
                        'sequence_number' => $fgs->sequence_number,
                        'growing_stage' => $fgs->growing_stage,
                        'growing_stage_name' => $fgs->growing_stage_name,
                        'number_of_holes' => $fgs->number_of_holes,
                        'panel_count_list' => $filtered->filterByFactoryGrowingStage($fgs)
                    ];
                }

                $fs->growing_stages = $growing_stages;

                $fs->harvesting_stage = (object)[
                    'number_of_holes' => $fgs->number_of_holes,
                    'panel_count_list' => $harvesting_panel_count_list->filterByFactorySpecies($fs)
                ];

                return $fs;
            });

        $file_name = generate_file_name(trans('view.plan.growth_planned_table.index'), [
            $factory->factory_abbreviation,
            $params['date_from']->format('Ymd')
        ]);

        $this->excel->create($file_name, function ($excel) use ($factory, $factory_species_list, $working_dates) {
            $sheet_name = trans('view.plan.growth_planned_table.index');
            $excel->sheet($sheet_name, function ($sheet) use ($factory, $factory_species_list, $working_dates) {
                $sheet->loadView('plan.growth_planned_table.export')
                    ->with('factory', $factory)
                    ->with('factory_species_list', $factory_species_list)
                    ->with('working_dates', $working_dates)
                    ->with('growing_stage', new GrowingStage());

                $sheet->setFreeze('E4');
                $sheet->mergeCells('A2:C3');
                $sheet->setBorder('A2:C3', 'medium');
            });
        })
            ->export();
    }

    /**
     * パネル状況データの複製
     *
     * @param  array $options
     * @param  bool $only_show
     * @return \App\Models\Plan\Collections\PanelStateCollection $dates
     */
    public function replicatePanelStates(array $options, bool $only_show): PanelStateCollection
    {
        if (is_null($options['date'])) {
            $options['date'] = Chronos::today()->addYear()->format('Y-m-d');
        }

        $factory_beds = $this->panel_state_repo->getLateseDatePerBed($options);
        if ($only_show) {
            return $factory_beds;
        }

        foreach ($factory_beds as $bed) {
            $this->panel_state_repo->replicatePanelStates(
                $bed->factory_code,
                $bed->bed_row,
                $bed->bed_column,
                $options['date']
            );
        }

        return $factory_beds;
    }
}
