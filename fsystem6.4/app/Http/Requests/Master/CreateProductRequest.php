<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Product;
use App\Models\Master\Species;
use App\ValueObjects\Decimal\ProductSize;
use App\ValueObjects\Decimal\ProductWeight;
use App\ValueObjects\Enum\ProductClass;
use App\ValueObjects\Integer\SalesOrderUnitQuantity;
use App\ValueObjects\String\CategoryCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\ProductCode;

class CreateProductRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Product
     */
    private $product;

    /**
     * @var \App\Models\Master\Species
     */
    private $species;

    /**
     * @var \App\ValueObjects\String\ProductCode
     */
    private $product_code;

    /**
     * @var \App\ValueObjects\String\CategoryCode
     */
    private $category_code;

    /**
     * @var \App\ValueObjects\Enum\ProductClass
     */
    private $product_class;

    /**
     * @var \App\ValueObjects\Integer\SalesOrderUnitQuantity
     */
    private $sales_order_unit_quantity;

    /**
     * @var \App\ValueObjects\Decimal\ProductWeight
     */
    private $product_weight;

    /**
     * @var \App\ValueObjects\Decimal\ProductSize
     */
    private $product_size;

    /**
     * @var \App\ValueObjects\String\CountryCode
     */
    private $country_code;

    /**
     * @var array
     */
    protected $on_off_checkboxes;

    /**
     * @param  \App\Models\Master\Product $product
     * @param  \App\Models\Master\Species $species
     * @param  \App\ValueObjects\String\ProductCode $product_code
     * @param  \App\ValueObjects\String\CategoryCode $category_code
     * @param  \App\ValueObjects\Enum\ProductClass $product_class
     * @param  \App\ValueObjects\Integer\SalesOrderUnitQuantity $sales_order_unit_quantity
     * @param  \App\ValueObjects\Decimal\ProductWeight $product_weight
     * @param  \App\ValueObjects\Decimal\ProductSize $product_size
     * @param  \App\ValueObjects\String\CountryCode $country_code
     * @return void
     */
    public function __construct(
        Product $product,
        Species $species,
        ProductCode $product_code,
        CategoryCode $category_code,
        ProductClass $product_class,
        SalesOrderUnitQuantity $sales_order_unit_quantity,
        ProductWeight $product_weight,
        ProductSize $product_size,
        CountryCode $country_code
    ) {
        $this->product = $product;
        $this->species = $species;
        $this->product_code = $product_code;
        $this->category_code = $category_code;
        $this->product_class = $product_class;
        $this->sales_order_unit_quantity = $sales_order_unit_quantity;
        $this->product_weight = $product_weight;
        $this->product_size = $product_size;
        $this->country_code = $country_code;

        $this->on_off_checkboxes = $this->product->getWillCastAsBoolean();
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
            'product_code' => [
                'bail',
                'required',
                "unique:{$this->product->getTable()}",
                "min:{$this->product_code->getMinLength()}",
                "max:{$this->product_code->getMaxLength()}",
                "regex:{$this->product_code->getRegexPattern()}"
            ],
            'species_code' => [
                'bail',
                'required',
                "exists:{$this->species->getTable()}"
            ],
            'product_name' => ['bail', 'required', 'string', 'max:40'],
            'result_addup_code' => ['bail', 'nullable', 'string', 'max:10', 'alpha'],
            'result_addup_name' => ['bail', 'nullable', 'string', 'max:30'],
            'result_addup_abbreviation' => ['bail', 'required', 'string', 'max:10'],
            'product_large_category' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->category_code->getMinLength()}",
                "max:{$this->category_code->getMaxLength()}",
                "regex:{$this->category_code->getRegexPattern()}"
            ],
            'product_middle_category' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->category_code->getMinLength()}",
                "max:{$this->category_code->getMaxLength()}",
                "regex:{$this->category_code->getRegexPattern()}"
            ],
            'product_class' => [
                'bail',
                'required',
                Rule::in($this->product_class->all())
            ],
            'custom_product_flag' => ['bail', 'required', 'boolean'],
            'sales_order_unit' => ['bail', 'nullable', 'string', 'max:3'],
            'sales_order_unit_quantity' => [
                'bail',
                'required',
                'integer',
                "min:{$this->sales_order_unit_quantity->getMinimumNum()}",
                "max:{$this->sales_order_unit_quantity->getMaximumNum()}"
            ],
            'minimum_sales_order_unit_quantity' => [
                'bail',
                'required',
                'integer',
                "min:{$this->sales_order_unit_quantity->getMinimumNum()}",
                "max:{$this->sales_order_unit_quantity->getMaximumNum()}"
            ],
            'statement_of_delivery_name' => ['bail', 'nullable', 'string', 'max:50'],
            'pickup_slip_message' => ['bail', 'nullable', 'string', 'max:40'],
            'lot_target_flag' => ['bail', 'required', 'boolean'],
            'species_name' => ['bail', 'nullable', 'string', 'max:25'],
            'export_target_flag' => ['bail', 'required', 'boolean'],
            'net_weight' => [
                'bail',
                'required',
                'numeric',
                "min:{$this->product_weight->getMinimumNum()}",
                "max:{$this->product_weight->getMaximumNum()}",
                "regex:{$this->product_weight->getRegexPattern()}"
            ],
            'gross_weight' => [
                'bail',
                'required',
                'numeric',
                "min:{$this->product_weight->getMinimumNum()}",
                "max:{$this->product_weight->getMaximumNum()}",
                "regex:{$this->product_weight->getRegexPattern()}"
            ],
            'depth' => [
                'bail',
                'required',
                'numeric',
                "min:{$this->product_size->getMinimumNum()}",
                "max:{$this->product_size->getMaximumNum()}",
                "regex:{$this->product_size->getRegexPattern()}"
            ],
            'width' => [
                'bail',
                'required',
                'numeric',
                "min:{$this->product_size->getMinimumNum()}",
                "max:{$this->product_size->getMaximumNum()}",
                "regex:{$this->product_size->getRegexPattern()}"
            ],
            'height' => [
                'bail',
                'required',
                'numeric',
                "min:{$this->product_size->getMinimumNum()}",
                "max:{$this->product_size->getMaximumNum()}",
                "regex:{$this->product_size->getRegexPattern()}"
            ],
            'country_of_origin' => [
                'bail',
                'required',
                'string',
                "min:{$this->country_code->getMinLength()}",
                "max:{$this->country_code->getMaxLength()}",
                "regex:{$this->country_code->getRegexPattern()}"
            ],
            'remark' => ['bail', 'nullable', 'string', 'max:255']
        ];

        return $rules + $this->reservedRules();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'species_code' => trans('view.master.species.species')
        ];
    }
}
