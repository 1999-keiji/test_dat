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

class UpdateProductRequest extends FormRequest
{
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
     * @param  \App\Models\Master\Prodcut $product
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
        $this->species = $species;
        $this->product_code = $product_code;
        $this->category_code = $category_code;
        $this->product_class = $product_class;
        $this->sales_order_unit_quantity = $sales_order_unit_quantity;
        $this->product_weight = $product_weight;
        $this->product_size = $product_size;
        $this->country_code = $country_code;

        $this->on_off_checkboxes = $product->getWillCastAsBoolean();
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
        $updatable = $this->route('product')->creating_type->getUpdatableCreatingTypes();
        $rules = [
            'species_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                "exists:{$this->species->getTable()}"
            ],
            'product_name' =>[
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:40'
            ],
            'result_addup_code' => ['bail', 'nullable', 'string', 'max:10', 'alpha'],
            'result_addup_name' => ['bail', 'nullable', 'string', 'max:30'],
            'result_addup_abbreviation' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:10'
            ],
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
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->product_class->all())
            ],
            'custom_product_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'sales_order_unit' => ['bail', 'nullable', 'string', 'max:3'],
            'sales_order_unit_quantity' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'integer',
                "min:{$this->sales_order_unit_quantity->getMinimumNum()}",
                "max:{$this->sales_order_unit_quantity->getMaximumNum()}"
            ],
            'minimum_sales_order_unit_quantity' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'integer',
                "min:{$this->sales_order_unit_quantity->getMinimumNum()}",
                "max:{$this->sales_order_unit_quantity->getMaximumNum()}"
            ],
            'statement_of_delivery_name' => ['bail', 'nullable', 'string', 'max:50'],
            'pickup_slip_message' => ['bail', 'nullable', 'string', 'max:40'],
            'lot_target_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'species_name' => ['bail', 'nullable', 'string', 'max:25'],
            'export_target_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'net_weight' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'numeric',
                "min:{$this->product_weight->getMinimumNum()}",
                "max:{$this->product_weight->getMaximumNum()}",
                "regex:{$this->product_weight->getRegexPattern()}"
            ],
            'gross_weight' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'numeric',
                "min:{$this->product_weight->getMinimumNum()}",
                "max:{$this->product_weight->getMaximumNum()}",
                "regex:{$this->product_weight->getRegexPattern()}"
            ],
            'depth' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'numeric',
                "min:{$this->product_size->getMinimumNum()}",
                "max:{$this->product_size->getMaximumNum()}",
                "regex:{$this->product_size->getRegexPattern()}"
            ],
            'width' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'numeric',
                "min:{$this->product_size->getMinimumNum()}",
                "max:{$this->product_size->getMaximumNum()}",
                "regex:{$this->product_size->getRegexPattern()}"
            ],
            'height' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'numeric',
                "min:{$this->product_size->getMinimumNum()}",
                "max:{$this->product_size->getMaximumNum()}",
                "regex:{$this->product_size->getRegexPattern()}"
            ],
            'country_of_origin' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                "min:{$this->country_code->getMinLength()}",
                "max:{$this->country_code->getMaxLength()}",
                "regex:{$this->country_code->getRegexPattern()}"
            ],
            'remark' => ['bail', 'nullable', 'string', 'max:255'],
            'updated_at' => ['required', 'date_format:Y-m-d H:i:s']
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

    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all();
        $target = ($this->route('product')->creating_type->isUpdatableCreatingType())
            ? $this->on_off_checkboxes
            : [];

        foreach ($target as $cb) {
            $input[$cb] = isset($input[$cb]) ? (int)$input[$cb] : 0;
        }

        $input['creating_type'] = $this->route('product')->creating_type->value();
        return data_get(
            $input + $this->query->all(),
            $key,
            $default
        );
    }
}
