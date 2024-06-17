<?php

namespace Trax\XapiStore\Relations;

use Illuminate\Database\Eloquent\Model;

class StatementActivity extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'trax_xapi_statement_activity';

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
    protected $fillable = ['activity_id', 'statement_id', 'type', 'sub'];

    /**
     * Types of relation.
     */
    const TYPE_OBJECT = 1;
    const TYPE_CONTEXT_PARENT = 2;
    const TYPE_CONTEXT_GROUPING = 3;
    const TYPE_CONTEXT_CATEGORY = 4;
    const TYPE_CONTEXT_OTHER = 5;

    /**
     * Return the context type given its name.
     *
     * @param  string  $name
     * @return int
     */
    public static function contextByName(string $name): int
    {
        switch ($name) {
            case 'parent':
                return  StatementActivity::TYPE_CONTEXT_PARENT;
            case 'grouping':
                return  StatementActivity::TYPE_CONTEXT_GROUPING;
            case 'category':
                return  StatementActivity::TYPE_CONTEXT_CATEGORY;
            case 'other':
                return  StatementActivity::TYPE_CONTEXT_OTHER;
            default:
                return null;
        }
    }
}
