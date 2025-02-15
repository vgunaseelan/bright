<?php

declare(strict_types=1);

namespace Diviky\Bright\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Uuids
{
    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @SuppressWarnings(PHPMD)
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Boot function from Laravel.
     */
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Model $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
