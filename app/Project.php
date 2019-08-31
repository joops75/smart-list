<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ProjectDeleted;

class Project extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function tasks() {
        return $this->hasMany('App\Task');
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleted' => ProjectDeleted::class,
    ];
}
