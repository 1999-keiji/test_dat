<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\DeliveryDestinationCode;

class SearchDeliveryDestinationsRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\DeliveryDestinationCode
     */
    private $delivery_destination_code;

    /**
     * @param  \App\Model\Master\Species $species
     * @param  \App\ValueObjects\String\DeliveryDestinationCode $delivery_destination_code
     * @return void
     */
    public function __construct(DeliveryDestinationCode $delivery_destination_code)
    {
        $this->delivery_destination_code = $delivery_destination_code;
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
            'delivery_destination_name' => ['bail', 'nullable', 'string', 'max:40']
        ];
    }
}
