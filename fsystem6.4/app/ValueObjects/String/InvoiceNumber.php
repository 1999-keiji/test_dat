<?php

declare(strict_types=1);

namespace App\ValueObjects\String;

use App\ValueObjects\ValueObjectInterface;
use App\ValueObjects\Date\DeliveryDate;

final class InvoiceNumber implements ValueObjectInterface
{
    /**
     * @var int
     */
    private const MIN_LENGTH = 24;

    /**
     * @var int
     */
    private const MAX_LENGTH = 24;

    /**
     * @var string
     */
    private const REGEX_PATTERN = "/\A[a-zA-Z0-9-]{1,15}\_[0-9]{6}\_[0-9]{3}+\z/";

    /**
     * @var string
     */
    private $invoice_number;

    /**
     * 請求書番号を発行する
     *
     * @param  string $factory_code
     * @param  \App\ValueObjects\Date\DeliveryDate $delivery_month
     * @param  int $count
     * @return string $invoice_number
     */
    public static function generateInvoiceNumber(string $factory_code, DeliveryDate $delivery_month, int $count): string
    {
        return implode('_', [
            $factory_code,
            $delivery_month->format('Ym'),
            sprintf('%03d', $count)
        ]);
    }

    /**
     * @param  mixed $value
     * @return bool
     */
    public static function isValidValue($invoice_number): bool
    {
        if (mb_strlen($invoice_number) < self::MIN_LENGTH) {
            return false;
        }
        if (mb_strlen($invoice_number) > self::MAX_LENGTH) {
            return false;
        }
        if (! preg_match(self::REGEX_PATTERN, $invoice_number)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->invoice_number;
    }

    /**
     * @return string
     */
    public function __toString(): stinrg
    {
        return $this->invoice_number;
    }
}
