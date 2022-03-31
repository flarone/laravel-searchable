<?php

namespace Flarone\Searchable\Models;

use Flarone\Searchable\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Search extends Model
{
    use SoftDeletes, Uuids;

    protected $table = 'search_index';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'id',   // maybe remove this one
        'searchcontent',
        'field',
        'model',
        'model_id',
        'parent_model',
        'parent_id'
    ];

    /**
     * The attributes that should be casted as a type
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];
}
