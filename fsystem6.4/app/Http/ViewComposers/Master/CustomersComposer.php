<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\ValueObjects\Enum\BasisForRecordingSales;
use App\ValueObjects\Enum\CanDisplay;
use App\ValueObjects\Enum\ClosingDate;
use App\ValueObjects\Enum\PaymentTimingDate;
use App\ValueObjects\Enum\PaymentTimingMonth;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\RoundingType;
use App\ValueObjects\Enum\OrderCooperation;
use App\ValueObjects\String\CustomerCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;

class CustomersComposer
{
    /**
     * @var \App\ValueObjects\Enum\ClosingDate
     */
    private $closing_date;

    /**
     * @var \App\ValueObjects\Enum\PaymentTimingMonth
     */
    private $payment_timing_month;

    /**
     * @var \App\ValueObjects\Enum\PaymentTimingDate
     */
    private $payment_timing_date;

    /**
     * @var \App\ValueObjects\Enum\BasisForRecordingSales
     */
    private $basis_for_recording_sales;

    /**
     * @var \App\ValueObjects\Enum\RoundingType
     */
    private $rounding_type;

    /**
     * @var \App\ValueObjects\Enum\CanDisplay
     */
    private $can_display;

    /**
     * @var \App\ValueObjects\Enum\OrderCooperation
     */
    private $order_cooperation;

    /**
     * @param  \App\ValueObjects\Enum\ClosingDate $closing_date
     * @param  \App\ValueObjects\Enum\PaymentTimingMonth $payment_timing_month
     * @param  \App\ValueObjects\Enum\PaymentTimingDate $payment_timing_date
     * @param  \App\ValueObjects\Enum\BasisForRecordingSales $basis_for_recording_sales
     * @param  \App\ValueObjects\Enum\RoundingType $rounding_type
     * @param  \App\ValueObjects\Enum\CanDisplay $can_display
     * @param  \App\ValueObjects\Enum\OrderCooperation $order_cooperation
     * @return void
     */
    public function __construct(
        ClosingDate $closing_date,
        PaymentTimingMonth $payment_timing_month,
        PaymentTimingDate $payment_timing_date,
        BasisForRecordingSales $basis_for_recording_sales,
        RoundingType $rounding_type,
        CanDisplay $can_display,
        OrderCooperation $order_cooperation
    ) {
        $this->closing_date              = $closing_date;
        $this->payment_timing_month      = $payment_timing_month;
        $this->payment_timing_date       = $payment_timing_date;
        $this->basis_for_recording_sales = $basis_for_recording_sales;
        $this->rounding_type             = $rounding_type;
        $this->can_display               = $can_display;
        $this->order_cooperation         = $order_cooperation;
    }
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $view->with([
            'customer_code' => new CustomerCode(),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode(),
            'closing_date_list' => $this->closing_date->all(),
            'payment_timing_month_list' => $this->payment_timing_month->all(),
            'payment_timing_date_list' => $this->payment_timing_date->all(),
            'basis_for_recording_sales_list' => $this->basis_for_recording_sales->all(),
            'rounding_type_list' => $this->rounding_type->all(),
            'can_display_list' => $this->can_display->all(),
            'order_cooperation_list' => $this->order_cooperation->all()
        ]);
    }
}
