<?php

namespace Flarone\Searchable\Traits;

use Flarone\Searchable\Models\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait Indexable
{
//    private $buffer = 10;

    protected static function bootSearchable()
    {
        static::created(function (Model $model) {
            $class = get_class($model);
        });

        static::updated(function (Model $model) {
            //self::getParent($model);
            // Delete all related records in the search index

            // Re-index from the model up (incl relations)

            // Onderzoeken how to get parent from many-to-many relation
        });

        static::deleting(function (Model $model) {
            //$model->removeSearchIndexOnDelete();
        });
    }

    protected function generateSearchableOnCreate()
    {
        Log::info('Creating search index');
    }

    protected function generateSearchableOnUpdate()
    {
        Log::info('Update search index');
    }

    protected function removeSearchableOnDelete()
    {
        Log::info('Remove search index');
    }

    protected function getIndexEntry($model) {
        return Search::where(['model' => get_class($model), 'model_id' => $model->id])->first();
    }

    protected function getParent($model) {
        $result = self::getIndexEntry($model);
        if ($result) {
            // Return the parent model
        }
        return null;
    }
}