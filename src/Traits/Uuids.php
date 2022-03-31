<?php

namespace Flarone\Searchable\Traits;

use Illuminate\Support\Str;

trait Uuids
{
    /**
    * Boot function from Laravel
    */
    protected static function bootUuids()
    {
        static::creating(function ($model) {
            $model->incrementing = false;
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        });
    }
}