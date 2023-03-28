<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\TransportCompany;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;
use App\ValueObjects\String\TransportCompanyCode;

class CreateTransportCompanyRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\TransportCompany
     */
    private $transport_company;

    /**
     * @var \App\Models\Master\TransportCompanyCode
     */
    private $transport_company_code;

    /**
     * @var \App\ValueObjects\String\CountryCode
     */
    private $country_code;

    /**
     * @var \App\ValueObjects\String\PostalCode
     */
    private $postal_code;

    /**
     * @var \App\ValueObjects\Enum\PrefectureCode
     */
    private $prefecture_code;

    /**
     * @param \App\Models\Master\TransportCompany $transport_company
     * @param \App\ValueObjects\String\TransportCompanyCode $transport_company_code
     * @param \App\ValueObjects\String\CountryCode $country_code
     * @param \App\ValueObjects\String\PostalCode $postal_code
     * @param \App\ValueObjects\Enum\PrefectureCode $prefecture_code
     * @return void
     */
    public function __construct(
        TransportCompany $transport_company,
        TransportCompanyCode $transport_company_code,
        CountryCode $country_code,
        PostalCode $postal_code,
        PrefectureCode $prefecture_code
    ) {
        $this->transport_company = $transport_company;
        $this->transport_company_code = $transport_company_code;
        $this->country_code = $country_code;
        $this->postal_code = $postal_code;
        $this->prefecture_code = $prefecture_code;
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
            'transport_company_code' => [
                'bail',
                'required',
                "unique:{$this->transport_company->getTable()}",
                "max:{$this->transport_company_code->getMaxLength()}",
                "regex:{$this->transport_company_code->getRegexPattern()}"
            ],
            'transport_company_name' => [
                'bail',
                'required',
                'string',
                'max:50'
            ],
            'transport_branch_name' => [
                'bail',
                'required',
                'string',
                'max:50'
            ],
            'transport_company_abbreviation' => [
                'bail',
                'required',
                'string',
                'max:20'
            ],
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
            'note' => ['bail', 'nullable', 'string', 'max:50'],
            'remark' => ['bail', 'nullable', 'string', 'max:255']
        ];
    }
}
