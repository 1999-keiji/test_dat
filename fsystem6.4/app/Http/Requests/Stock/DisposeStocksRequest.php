<?php

declare(strict_types=1);

namespace App\Http\Requests\Stock;

use App\Http\Requests\FormRequest;

class DisposeStocksRequest extends FormRequest
{
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
            'stocks' => ['bail', 'required', 'array'],
            'stocks.*.disposal_quantity' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'less_than_equal_field:stocks.*.stock_quantity'
            ],
            'stocks.*.disposal_at' => [
                'bail',
                'nullable',
                'required_if_over_zero:stocks.*.disposal_quantity',
                'date_format:Y/m/d',
                'after_or_equal:stocks.*.harvesting_date'
            ],
            'stocks.*.disposal_remark' => [
                'bail',
                'nullable',
                'string',
                'max:30'
            ]
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
            'stocks.required' => '廃棄登録対象の在庫データがありません。',
            'stocks.array' => '廃棄登録対象の在庫データがありません。',
            'stocks.*.disposal_quantity.required' => '廃棄数量は、必ず指定してください。',
            'stocks.*.disposal_quantity.integer' => '廃棄数量には、正数を指定してください。',
            'stocks.*.disposal_quantity.min' => '廃棄数量には、0以上の数字を指定してください。',
            'stocks.*.disposal_quantity.less_than_equal_field' => '廃棄数量には、保管数量以下の数字を指定してください。',
            'stocks.*.disposal_at.required_if_over_zero' => '廃棄数量が設定されている場合、廃棄日も指定してください。',
            'stocks.*.disposal_at.date_format' => '廃棄日の形式が正しくありません。',
            'stocks.*.disposal_at.after_or_equal' => '廃棄日には、収穫日以降もしくは同日を指定してください。',
            'stocks.*.disposal_at.string' => '備考には、文字を指定してください。',
            'stocks.*.disposal_at.max' => '備考は、:max文字以下にしてください。',
        ];
    }
}
