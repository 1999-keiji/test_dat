<?php
declare(strict_types=1);

namespace App\Http\Requests\Master;

use App\Http\Requests\FormRequest;
use App\ValueObjects\String\WarehouseCode;

class SearchWarehouseRequest extends FormRequest
{
    /**
     * @var \App\ValueObjects\String\WarehouseCode
     */
    private $warehouse_code;

    /**
     * @param  \App\ValueObjects\String\WarehouseCode $warehouse_code
     * @return void
     */
    public function __construct(WarehouseCode $warehouse_code)
    {
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
            'warehouse_code' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->warehouse_code->getMinLength()}",
                "max:{$this->warehouse_code->getMaxLength()}",
                "regex:{$this->warehouse_code->getRegexPattern()}"
            ],
            'warehouse_name' => ['bail', 'nullable', 'string', 'max:50']
        ];
    }
}
