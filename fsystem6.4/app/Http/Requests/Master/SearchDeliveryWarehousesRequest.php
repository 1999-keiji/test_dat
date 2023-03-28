<?php
declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\DeliveryDestinationCode;
use App\ValueObjects\String\WarehouseCode;

class SearchDeliveryWarehousesRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\DeliveryDestinationCode
     */
    private $delivery_destination_code;

    /**
     * @var \App\ValueObjects\String\WarehouseCode
     */
    private $warehouse_code;

    /**
     * @param  \App\ValueObjects\String\DeliveryDestinationCode $delivery_destination_code
     * @param  \App\ValueObjects\String\WarehouseCode $warehouse_code
     * @return void
     */
    public function __construct(DeliveryDestinationCode $delivery_destination_code, WarehouseCode $warehouse_code)
    {
        $this->delivery_destination_code = $delivery_destination_code;
        $this->warehouse_code = $warehouse_code;
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
        return [
            'delivery_destination_code' => [
                'bail',
                'nullable',
                'string',
                "max:{$this->delivery_destination_code->getMaxLength()}",
                "regex:{$this->delivery_destination_code->getRegexPattern()}"
            ],
            'delivery_destination_name' => ['bail', 'nullable', 'string', 'max:40'],
            'warehouse_code' => [
                'bail',
                'nullable',
                'string',
                "max:{$this->warehouse_code->getMaxLength()}",
                "regex:{$this->warehouse_code->getRegexPattern()}"
            ],
            'warehouse_name' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }
}
