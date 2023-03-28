<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\Corporation;
use App\Models\Master\Supplier;
use App\ValueObjects\Date\WorkingDate;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;
use App\ValueObjects\String\SymbolicCode;

class UpdateFactoryRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\Corporation
     */
    private $corporation;

    /**
     * @var \App\Models\Master\Supplier
     */
    private $supplier;

    /**
     * @var \App\ValueObjects\Enum\PrefectureCode
     */
    private $prefecture_code;

    /**
     * @var \App\ValueObjects\String\CountryCode
     */
    private $country_code;

    /**
     * @var \App\ValueObjects\String\PostalCode
     */
    private $postal_code;

    /**
     * @var \App\ValueObjects\String\SymbolicCode
     */
    private $symbolic_code;

    /**
     * @var \App\ValueObjects\Date\WorkingDate
     */
    private $working_date;

    /**
     * @param \App\Models\Master\Corporation $corporation
     * @param \App\Models\Master\Supplier $supplier
     * @param \App\ValueObjects\Enum\PrefectureCode $prefecture_code
     * @param \App\ValueObjects\String\CountryCode $country_code
     * @param \App\ValueObjects\String\PostalCode $postal_code
     * @param \App\ValueObjects\String\SymbolicCode $symbolic_code
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return void
     */
    public function __construct(
        Corporation $corporation,
        Supplier $supplier,
        PrefectureCode $prefecture_code,
        CountryCode $country_code,
        PostalCode $postal_code,
        SymbolicCode $symbolic_code,
        WorkingDate $working_date
    ) {
        $this->corporation  = $corporation;
        $this->supplier  = $supplier;

        $this->prefecture_code = $prefecture_code;
        $this->country_code = $country_code;
        $this->postal_code = $postal_code;
        $this->symbolic_code = $symbolic_code;
        $this->working_date = $working_date;
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
            'factory_name' => ['bail', 'required', 'string', 'max:50'],
            'factory_abbreviation' => ['bail', 'required', 'string', 'max:20'],
            'country_code' => [
                'bail',
                'required',
                'string',
                "min:{$this->country_code->getMinLength()}",
                "max:{$this->country_code->getMaxLength()}",
                "regex:{$this->country_code->getRegexPattern()}"
            ],
            'postal_code' => [
                'bail',
                'required',
                'string',
                "min:{$this->postal_code->getMinLength()}",
                "max:{$this->postal_code->getMaxLength()}",
                "regex:{$this->postal_code->getRegexPattern()}"
            ],
            'prefecture_code' => [
                'bail',
                'nullable',
                "required_if:country_code,{$this->prefecture_code->getJoinedRequirePrefectureCodeList()}",
                Rule::in($this->prefecture_code->all())
            ],
            'address' => ['bail', 'required', 'string', 'max:50'],
            'address2' => ['bail', 'nullable', 'string', 'max:50'],
            'address3' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address2' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address3' => ['bail', 'nullable', 'string', 'max:50'],
            'phone_number' => ['bail', 'required', 'string', 'max:20', 'regex:/\A[0-9-]+\z/'],
            'extension_number' => ['bail', 'nullable', 'string', 'max:15', 'regex:/\A[0-9-]+\z/'],
            'fax_number' => ['bail', 'nullable', 'string', 'max:15', 'regex:/\A[0-9-]+\z/'],
            'mail_address' => ['bail', 'nullable', 'string', 'max:250', 'email'],
            'working_days' => ['bail', 'required', 'array', 'min:1', 'max:'.$this->working_date::DAYS_PER_WEEK],
            'corporation_code' => ['bail', 'required', "exists:{$this->corporation->getTable()}"],
            'supplier_code' => ['bail', 'required', "exists:{$this->supplier->getTable()}"],
            'symbolic_code' => [
                'bail',
                'required',
                'string',
                "min:{$this->symbolic_code->getMinLength()}",
                "max:{$this->symbolic_code->getMaxLength()}",
                "regex:{$this->symbolic_code->getRegexPattern()}"
            ],
            'global_gap_number' => ['bail', 'required', 'string', 'max:15'],
            'invoice_bank_name' => ['bail', 'required', 'string', 'max:40'],
            'invoice_bank_branch_name' => ['bail', 'required', 'string', 'max:40'],
            'invoice_bank_account_number' => ['bail', 'required', 'string', 'max:8'],
            'invoice_bank_account_holder' => ['bail', 'required', 'string', 'max:40'],
            'remark' => ['bail', 'nullable', 'string', 'max:255'],
            'invoice_corporation_name' => ['bail', 'nullable', 'string', 'max:50'],
            'invoice_postal_code' => [
                'bail',
                'nullable',
                'string',
                "min:{$this->postal_code->getMinLength()}",
                "max:{$this->postal_code->getMaxLength()}",
                "regex:{$this->postal_code->getRegexPattern()}"
            ],
            'invoice_address' => ['bail', 'nullable', 'string', 'max:50'],
            'invoice_phone_number' => ['bail', 'nullable', 'string', 'max:20', 'regex:/\A[0-9-]+\z/'],
            'invoice_fax_number' => ['bail', 'nullable', 'string', 'max:15', 'regex:/\A[0-9-]+\z/']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'working_days.required' => '営業日は少なくとも１日指定してください。'
        ];
    }
}
