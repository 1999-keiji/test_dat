<?php

declare(strict_types=1);

namespace App\Services\Plan;

use App\Models\Plan\Collections\CropCollection;
use App\Models\Plan\Collections\ForecastedProductRateCollection;
use App\Repositories\Plan\CropRepository;
use App\Repositories\Plan\ForecastedProductRateRepository;
use App\ValueObjects\Date\HarvestingDate;

class CropService
{
    /**
     * @var \App\Repositories\Plan\CropRepository
     */
    private $crop_repo;

    /**
     * @var \App\Repositories\Plan\ForecastedProductRateRepository
     */
    private $forecasted_product_rate_repo;

    /**
     * @param  \App\Repositories\Plan\CropRepository $crop_repo
     * @param  \App\Repositories\Plan\ForecastedProductRateRepository $forecasted_product_rate_repo
     * @return void
     */
    public function __construct(
        CropRepository $crop_repo,
        ForecastedProductRateRepository $forecasted_product_rate_repo
    ) {
        $this->crop_repo = $crop_repo;
        $this->forecasted_product_rate_repo = $forecasted_product_rate_repo;
    }

    /**
     * 指定された品種、収穫期間に応じて出来高データを取得
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\Collections\CropCollection
     */
    public function getCropsBySpeciesAndHarvestingDate(
        array $params,
        HarvestingDate $harvesting_date
    ): CropCollection {
        $harvesting_date_term = [];
        if ($params['display_term'] === 'date') {
            $harvesting_dates = $harvesting_date->toListOfDate((int)$params['week_term']);
            $harvesting_date_term = [
                'from' => head($harvesting_dates),
                'to' => last($harvesting_dates)
            ];
        }
        if ($params['display_term'] === 'month') {
            $harvesting_months = $harvesting_date->toListOfMonth();
            $harvesting_date_term = [
                'from' => head($harvesting_months)->firstOfMonth(),
                'to' => last($harvesting_months)->lastOfMonth()
            ];
        }

        return $this->crop_repo->getCropsBySpeciesAndHarvestingDate($params, $harvesting_date_term);
    }

    /**
     * 指定された品種、収穫期間に応じて予想製品化率データを取得
     *
     * @param  array $params
     * @param  \App\ValueObjects\Date\HarvestingDate $harvesting_date
     * @return \App\Models\Plan\Collections\ForecastedProductRateCollection
     */
    public function getForecastedProductRatesBySpeciesAndHarvestingDate(
        array $params,
        HarvestingDate $harvesting_date
    ): ForecastedProductRateCollection {
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

        return $this->forecasted_product_rate_repo
            ->getForecastedProductRatesBySpeciesAndHarvestingDate($params, $harvesting_date_term);
    }
}
