<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Support\Arr;

class FormRequest extends BaseFormRequest
{
    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function all($keys = null)
    {
        $input = array_replace_recursive($this->input(), $this->allFiles());
        if (! $keys) {
            return array_except($input, ['_token', '_method']);
        }

        $results = [];
        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            Arr::set($results, $key, Arr::get($input, $key));
        }

        return $results;
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
        foreach ($this->on_off_checkboxes ?? [] as $cb) {
            $input[$cb] = isset($input[$cb]) ? (int)$input[$cb] : 0;
        }

        return data_get(
            $input + $this->query->all(),
            $key,
            $default
        );
    }

    /**
     * 予備項目用のバリデーションルールを返却
     *
     * @return  $rules array
     */
    protected function reservedRules(): array
    {
        $rules = [];
        foreach (range(1, config('settings.number_of_reserved_item')) as $idx) {
            $rules["reserved_text{$idx}"] = ['bail', 'nullable', 'string', 'max:200'];
            $rules["reserved_number{$idx}"] = [
                'bail',
                'nullable',
                "regex:/\A([-]?[1-9][0-9]{0,13}|0)(\.[0-9]{1,5})?\z/",
                'min:-999999999999.99999',
                'max:9999999999999.99999'
            ];
        }

        return $rules;
    }
}
