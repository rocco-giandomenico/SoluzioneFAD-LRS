<?php

namespace Trax\XapiStore\Relations;

use Illuminate\Database\Eloquent\Model;

class StatementVerb extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'trax_xapi_statement_verb';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['verb_id', 'statement_id', 'sub'];
}
