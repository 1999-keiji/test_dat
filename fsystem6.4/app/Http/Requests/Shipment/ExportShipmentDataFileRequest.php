<?php

declare(strict_types=1);

namespace App\Http\Requests\Shipment;

use Exception;
use Illuminate\Validation\Rule;
use Cake\Chronos\Chronos;
use App\Http\Requests\FormRequest;
use App\Models\Master\Customer;
use App\Models\Master\EndUser;
use App\Models\Master\Factory;
use App\ValueObjects\Enum\ShipmentDataExportFile;

class ExportShipmentDataFileRequest extends FormRequest
{
    /**
     * @var int
     */
    private const VALID_DATE_TERM = 30;

    /**
     * @var \App\Models\Master\Factory
     */
    private $factory;

    /**
     *  @var \App\Models\Master\Customer
     */
    private $customer;

    /**
     * @var \App\Models\Master\EndUser
     */
    private $end_user;

    /**
     * @var \App\ValueObjects\Enum\ShipmentDataExportFile
     */
    private $shipment_data_export_file_class;

    /**
     * @param \App\Models\Master\Factory $factory
     * @param \App\Models\Master\Customer $customer
     * @param \App\Models\Master\EndUser $end_user
     * @param \App\ValueObjects\Enum\ShipmentDataExportFile $shipment_data_export_file
     * @return void
     */
    public function __construct(
        Factory $factory,
        Customer $customer,
        EndUser $end_user,
        ShipmentDataExportFile $shipment_data_export_file
    ) {
        $this->factory = $factory;
        $this->customer = $customer;
        $this->end_user = $end_user;
        $this->shipment_data_export_file_class = $shipment_data_export_file;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'factory_code' => ['bail', 'required', "exists:{$this->factory->getTable()}"],
            'shipment_data_export_file' => ['bail', 'required', Rule::in($this->shipment_data_export_file_class->all())]
        ];

        if ($this->shipment_data_export_file == ShipmentDataExportFile::BY_DAY) {
            $rules['harvesting_date.from'] = ['bail', 'required', 'date_format:Y/m/d'];
            $rules['harvesting_date.to']   = ['bail', 'required', 'date_format:Y/m/d'];

            try {
                if ($this->harvesting_date['from']) {
                    $harvesting_date_to = Chronos::parse($this->harvesting_date['from'])
                        ->addDays(self::VALID_DATE_TERM)
                        ->format('Y-m-d');

                    $rules['harvesting_date.to'][] = "before_or_equal:${harvesting_date_to}";
                    $rules['harvesting_date.to'][] = 'after_or_equal:harvesting_date.from';
                }
            } catch (Exception $e) {
                //
            }
        }

        if ($this->shipment_data_export_file == ShipmentDataExportFile::BY_CUSTOMER) {
            $rules['shipping_date.from'] = ['bail', 'nullable', 'date_format:Y/m/d'];
            $rules['shipping_date.to']   = ['bail', 'nullable', 'date_format:Y/m/d'];
            $rules['delivery_date.from'] = ['bail', 'required', 'date_format:Y/m/d'];
            $rules['delivery_date.to']   = ['bail', 'required', 'date_format:Y/m/d'];
            $rules['customer_code'] = ['bail', 'nullable', "exists:{$this->customer->getTable()}"];
            $rules['end_user_code'] = ['bail', 'nullable', "exists:{$this->end_user->getTable()}"];

            try {
                if ($this->shipping_date['from']) {
                    $shipping_date_to = Chronos::parse($this->shipping_date['from'])
                        ->addDays(self::VALID_DATE_TERM)
                        ->format('Y-m-d');

                    $rules['shipping_date.to'][] = "before_or_equal:${shipping_date_to}";
                    $rules['shipping_date.to'][] = 'after_or_equal:shipping_date.from';
                }
            } catch (Exception $e) {
                //
            }

            try {
                if ($this->delivery_date['from']) {
                    $delivery_date_to = Chronos::parse($this->delivery_date['from'])
                        ->addDays(self::VALID_DATE_TERM)
                        ->format('Y-m-d');

                    $rules['delivery_date.to'][] = "before_or_equal:${delivery_date_to}";
                    $rules['delivery_date.to'][] = 'after_or_equal:delivery_date.from';
                }
            } catch (Exception $e) {
                //
            }
        }

        return $rules;
    }
}
