<?php

namespace Bregananta\Inventory\Models;

use Bregananta\Inventory\Traits\CategoryTrait;
use Baum\Node;

/**
 * Class Category.
 */
class Category extends Node
{
    use CategoryTrait;

    protected $table = 'categories';

    protected $fillable = [
        'name',
    ];

    protected $scoped = ['belongs_to'];

    /**
     * The hasMany inventories relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany('Bregananta\Inventory\Models\Inventory', 'category_id', 'id');
    }
}
