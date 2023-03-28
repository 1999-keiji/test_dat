<?php

namespace App\Models\Master;

use InvalidArgumentException;
use App\Models\Model;
use App\Models\Master\Collections\TaxCollection;
use App\ValueObjects\Date\ApplicationStartedOn;
use App\ValueObjects\Enum\RoundingType;

class Tax extends Model
{
    /**
     * 複数形にならないよう名前指定
     *
     * @var string
     */
    protected $table = 'tax';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['application_started_on', 'tax_rate'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\TaxCollection
     */
    public function newCollection(array $models = []): TaxCollection
    {
        return new TaxCollection($models);
    }

    /**
     * @return \App\ValueObjects\Date\ApplicationStartedOn
     */
    public function getApplicationStartedOn(): ApplicationStartedOn
    {
        return new ApplicationStartedOn($this->application_started_on);
    }

    /**
     * 税抜額から税額を計算
     *
     * @param  \App\Models\Master\Customer $customer
     * @param  float $amount
     * @return float
     * @throws InvalidArgumentException
     */
    public function calculateTaxAmount(Customer $customer, float $amount): float
    {
        if ($customer->rounding_type === RoundingType::FLOOR) {
            return floor($amount * $this->tax_rate);
        }
        if ($customer->rounding_type === RoundingType::CEIL) {
            return ceil($amount * $this->tax_rate);
        }
        if ($customer->rounding_type === RoundingType::ROUND) {
            return round($amount * $this->tax_rate);
        }

        throw new InvalidArgumentException('invalid rounding type.');
    }
}
