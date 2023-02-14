<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rss_Log extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rss__log';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
