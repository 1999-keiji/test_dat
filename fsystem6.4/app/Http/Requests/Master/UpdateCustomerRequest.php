<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\ValueObjects\Enum\BasisForRecordingSales;
use App\ValueObjects\Enum\ClosingDate;
use App\ValueObjects\Enum\PaymentTimingDate;
use App\ValueObjects\Enum\PaymentTimingMonth;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\RoundingType;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;

class UpdateCustomerRequest extends FormRequest
{
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
     * @param \App\ValueObjects\String\CountryCode $country_code
     * @param \App\ValueObjects\String\PostalCode $postal_code
     * @param \App\ValueObjects\Enum\PrefectureCode $prefecture_code
     * @param \App\ValueObjects\Enum\ClosingDate $closing_date
     * @param \App\ValueObjects\Enum\PaymentTimingMonth $payment_timing_month
     * @param \App\ValueObjects\Enum\PaymentTimingDate $payment_timing_date
     * @param \App\ValueObjects\Enum\BasisForRecordingSales $basis_for_recording_sales
     * @param \App\ValueObjects\Enum\RoundingType $rounding_type
     * @return void
     */
    public function __construct(
        CountryCode $country_code,
        PostalCode $postal_code,
        PrefectureCode $prefecture_code,
        ClosingDate $closing_date,
        PaymentTimingMonth $payment_timing_month,
        PaymentTimingDate $payment_timing_date,
        BasisForRecordingSales $basis_for_recording_sales,
        RoundingType $rounding_type
    ) {
        $this->country_code = $country_code;
        $this->postal_code = $postal_code;
        $this->prefecture_code = $prefecture_code;
        $this->closing_date = $closing_date;
        $this->payment_timing_month = $payment_timing_month;
        $this->payment_timing_date = $payment_timing_date;
        $this->basis_for_recording_sales = $basis_for_recording_sales;
        $this->rounding_type = $rounding_type;
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
            'customer_name' => ['bail', 'required', 'string', 'max:50'],
            'customer_name2' => ['bail', 'nullable', 'string', 'max:50'],
            'customer_abbreviation' => ['bail', 'required', 'string', 'max:20'],
            'customer_name_kana' => ['bail', 'required', 'string', 'max:30', 'hankana'],
            'customer_name_english' => ['bail', 'nullable', 'string', 'max:65', 'alpha_period_dash'],
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
            'closing_date' => ['bail', 'required', Rule::in($this->closing_date->all())],
            'payment_timing_month' => ['bail', 'required', Rule::in($this->payment_timing_month->all())],
            'payment_timing_date' => ['bail', 'required', Rule::in($this->payment_timing_date->all())],
            'basis_for_recording_sales' => ['bail', 'required', Rule::in($this->basis_for_recording_sales->all())],
            'rounding_type' => ['bail', 'required', Rule::in($this->rounding_type->all())],
            'can_display' => ['bail', 'required', 'boolean'],
            'order_cooperation' => ['bail', 'required', 'boolean'],
            'remark' => ['bail', 'nullable', 'string', 'max:255'],
            'updated_at' => ['required', 'date_format:Y-m-d H:i:s']
        ];
    }
}
