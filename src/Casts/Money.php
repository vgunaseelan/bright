<?php

declare(strict_types=1);

namespace Diviky\Bright\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @SuppressWarnings(PHPMD)
 */
class Money implements CastsAttributes
{
    /**
     * Currency Code.
     */
    protected string $currency;

    /**
     * Currenncy Decimal Places.
     */
    protected int $decimals = 2;

    /**
     * @param int    $decimals
     * @param string $currency
     */
    public function __construct($decimals = 2, $currency = 'INR')
    {
        $this->currency = $currency;
        $this->decimals = intval($decimals);
    }

    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param mixed                               $value
     * @param array                               $attributes
     *
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        return $this->from($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param mixed                               $value
     * @param array                               $attributes
     *
     * @return float|int
     */
    public function set($model, $key, $value, $attributes)
    {
        return $this->to($value);
    }

    /**
     * Convert to int from decimals.
     *
     * @param float|int $value
     *
     * @return float|int
     */
    public function to($value)
    {
        return $value * 10 ** $this->decimals;
    }

    /**
     * Convert to int from decimals.
     *
     * @param int $value
     *
     * @return string
     */
    public function from($value)
    {
        return $this->asDecimal(($value / 10 ** $this->decimals), $this->decimals);
    }

    /**
     * Convert to decimal number.
     *
     * @param float|int $value
     * @param int       $decimals
     *
     * @return string
     */
    protected function asDecimal($value, $decimals)
    {
        return \number_format($value, $decimals, '.', '');
    }
}
