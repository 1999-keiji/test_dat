<?php

declare(strict_types=1);

namespace App\Http\ViewComposers\Master;

use Illuminate\View\View;
use App\Services\Master\CurrencyService;
use App\Services\Master\CustomerService;
use App\ValueObjects\Enum\AbroadShipmentPriceShowClass;
use App\ValueObjects\Enum\CanDisplay;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ExportManagingClass;
use App\ValueObjects\Enum\ExportExchangeRateCode;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass;
use App\ValueObjects\Enum\StatementOfDeliveryClass;
use App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass;
use App\ValueObjects\Enum\StatementOfDeliveryRemarkClass;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\CustomerCode;
use App\ValueObjects\String\EndUserCode;
use App\ValueObjects\String\PostalCode;

class EndUsersComposer
{
    /**
     * @var \App\Services\Master\CustomerService
     */
    private $customer_service;

    /**
     * @var \App\Services\Master\CurrencyService
     */
    private $currency_service;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryClass
     */
    private $statement_of_delivery_class;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass
     */
    private $statement_of_delivery_price_show_class;

    /**
     * @var \App\ValueObjects\Enum\AbroadShipmentPriceShowClass
     */
    private $abroad_shipment_price_show_class;

    /**
     * @var \App\ValueObjects\Enum\ExportManagingClass
     */
    private $export_managing_class;

    /**
     * @var \App\ValueObjects\Enum\ExportExchangeRateCode
     */
    private $export_exchange_rate_code;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryRemarkClass
     */
    private $statement_of_delivery_remark_class;

    /**
     * @var \App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass
     */
    private $statement_of_delivery_buyer_remark_class;

    /**
     * @var \App\ValueObjects\Enum\CanDisplay
     */
    private $can_display;

    /**
     * @param  \App\Services\Master\CustomerService $customer_service
     * @param  \App\Services\Master\CurrencyService $currency_service
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryClass $statement_of_delivery_class
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass $statement_of_delivery_price_show_class
     * @param  \App\ValueObjects\Enum\AbroadShipmentPriceShowClass $abroad_shipment_price_show_class
     * @param  \App\ValueObjects\Enum\ExportManagingClass $export_managing_class
     * @param  \App\ValueObjects\Enum\ExportExchangeRateCode $export_exchange_rate_code
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryRemarkClass $statement_of_delivery_remark_class
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass $statement_of_delivery_buyer_remark_class
     * @param  \App\ValueObjects\Enum\CanDisplay $can_display
     * @return void
     */
    public function __construct(
        CustomerService $customer_service,
        CurrencyService $currency_service,
        StatementOfDeliveryClass $statement_of_delivery_class,
        StatementOfDeliveryPriceShowClass $statement_of_delivery_price_show_class,
        AbroadShipmentPriceShowClass $abroad_shipment_price_show_class,
        ExportManagingClass $export_managing_class,
        ExportExchangeRateCode $export_exchange_rate_code,
        StatementOfDeliveryRemarkClass $statement_of_delivery_remark_class,
        StatementOfDeliveryBuyerRemarkClass $statement_of_delivery_buyer_remark_class,
        CanDisplay $can_display
    ) {
        $this->customer_service = $customer_service;
        $this->currency_service = $currency_service;
        $this->statement_of_delivery_class = $statement_of_delivery_class;
        $this->statement_of_delivery_price_show_class = $statement_of_delivery_price_show_class;
        $this->abroad_shipment_price_show_class = $abroad_shipment_price_show_class;
        $this->export_managing_class = $export_managing_class;
        $this->export_exchange_rate_code = $export_exchange_rate_code;
        $this->statement_of_delivery_remark_class = $statement_of_delivery_remark_class;
        $this->statement_of_delivery_buyer_remark_class = $statement_of_delivery_buyer_remark_class;
        $this->can_display = $can_display;
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
            'end_user_code' => new EndUserCode(),
            'creating_type' => new CreatingType(CreatingType::MANUAL_CREATED),
            'customers' => $this->customer_service->getAllCustomers(),
            'country_code' => new CountryCode(),
            'postal_code' => new PostalCode(),
            'prefecture_code' => new PrefectureCode() ,
            'currencies' => $this->currency_service->getAllCurrencies(),
            'statement_of_delivery_class_list' => $this->statement_of_delivery_class->all(),
            'statement_of_delivery_price_show_class_list' => $this->statement_of_delivery_price_show_class->all(),
            'abroad_shipment_price_show_class_list' => $this->abroad_shipment_price_show_class->all(),
            'export_managing_class_list' => $this->export_managing_class->all(),
            'export_exchange_rate_code_list' => $this->export_exchange_rate_code->all(),
            'statement_of_delivery_remark_class_list' => $this->statement_of_delivery_remark_class->all(),
            'statement_of_delivery_buyer_remark_class_list' => $this->statement_of_delivery_buyer_remark_class->all(),
            'can_display_list' => $this->can_display->all(),
        ]);
    }
}
