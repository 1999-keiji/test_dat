<?php

declare(strict_types=1);

namespace App\Extension;

use Illuminate\Support\Arr;
use Illuminate\Validation\Concerns;
use Illuminate\Validation\Validator as BaseValidator;

class Validator extends BaseValidator
{
    use Concerns\FormatsMessages,
        Concerns\ValidatesAttributes;

    /**
     * The validation rules that imply the field is required.
     *
     * @var array
     */
    protected $implicitRules = [
        'Required', 'Filled', 'RequiredWith', 'RequiredWithAll', 'RequiredWithout',
        'RequiredWithoutAll', 'RequiredIf', 'RequiredUnless', 'Accepted', 'Present',
        'RequiredIfOverZero'
    ];

    /**
     * The validation rules which depend on other fields as parameters.
     *
     * @var array
     */
    protected $dependentRules = [
        'RequiredWith', 'RequiredWithAll', 'RequiredWithout', 'RequiredWithoutAll',
        'RequiredIf', 'RequiredUnless', 'Confirmed', 'Same', 'Different', 'Unique',
        'Before', 'After', 'BeforeOrEqual', 'AfterOrEqual',
        'LessThanEqualField', 'RequiredIfOverZero'
    ];

    /**
     * Override method. (remove 'u' option from regular expression)
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateAlpha($attribute, $value)
    {
        return is_string($value) && preg_match('/\A[\pL\pM]+\z/', $value);
    }

    /**
     * Override method. (remove 'u' option from regular expression)
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateAlphaDash($attribute, $value)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/\A[\pL\pM\pN_-]+\z/', $value) > 0;
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters, periods, dashes, and underscores.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    public function validateAlphaPeriodDash($attribute, $value)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/\A[\pL\pM\pN._-]+\z/', $value) > 0;
    }

    /**
     * Override method. (remove 'u' option from regular expression)
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateAlphaNum($attribute, $value)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/\A[\pL\pM\pN]+\z/', $value) > 0;
    }

    /**
     * Override method.
     * (validate that an attribute contains not only full space)
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    public function validateRequired($attribute, $value)
    {
        $chars = "[\\x0-\x20\x7f\xc2\xa0\xe3\x80\x80]";
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_string($value) && preg_replace("/\A{$chars}++|{$chars}++\z/u", '', $value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return false;
        } elseif ($value instanceof File) {
            return (string) $value->getPath() !== '';
        }

        return true;
    }

    /**
     * Validate that an attribute is positive integer
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validatePositiveInt($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false && intval($value) >= 0;
    }

    /**
     * Validate that an attribute contains only 'Zenkaku Katakana'
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function validateZenkana($attribute, $value)
    {
        return is_string($value) && preg_match('/\A[ァ-ヾ 　－]+\z/u', $value);
    }

    /**
     * Validate that an attribute contains only 'Hankaku Katakana'
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    protected function validateHankana($attribute, $value)
    {
        return is_string($value) && preg_match("#\A[ｱ-ﾝﾞﾟｧ-ｫｯｬ-ｮｰ \\,.･\-/｢｣\(\)]+\z#u", $value);
    }

    /**
     * Validate that an attribute contains only 'Zenkaku' characters
     *
     * @param  string $attribue
     * @param  mixed  $value
     * @return bool
     */
    protected function validateZenkaku($attribute, $value)
    {
        return is_string($value) && preg_match('/\A[^ -~｡-ﾟ]*\z/u', $value);
    }

    /**
     * Validate that an attribute contains only 'Zenkaku' characters
     *
     * @param  string $attribue
     * @param  mixed  $value
     * @return bool
     */
    protected function validateHankaku($attribute, $value)
    {
        return is_string($value) && preg_match('/\A[ -~｡-ﾟ]*\z/u', $value);
    }

    /**
     * Validate that an attribute is less than or equal another attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateLessThanEqualField($attribue, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'less_than_equal_field');
        return (int)$value <= (int)(Arr::get($this->data, $parameters[0]));
    }

    /**
     * Validate that an attribute exists when another attribute is a value that is greater than zero
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  mixed   $parameters
     * @return bool
     */
    protected function validateRequiredIfOverZero($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'required_if_over_zero');

        $other = filter_var(Arr::get($this->data, $parameters[0]), FILTER_VALIDATE_INT);
        if ($other === false) {
            return true;
        }
        if ((int)$other <= 0) {
            return true;
        }

        return $this->validateRequired($attribute, $value);
    }
}
