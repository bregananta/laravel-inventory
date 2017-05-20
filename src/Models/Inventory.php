<?php

namespace Bregananta\Inventory\Models;

use Bregananta\Inventory\Traits\AssemblyTrait;
use Bregananta\Inventory\Traits\InventoryVariantTrait;
use Bregananta\Inventory\Traits\InventoryTrait;

/**
 * Class Inventory.
 */
class Inventory extends BaseModel
{
    use InventoryTrait;
    use InventoryVariantTrait;
    use AssemblyTrait;

    protected $table = 'inventories';

    protected $fillable = [
        'user_id',
        'category_id',
        'metric_id',
        'name',
        'description',
    ];

    /**
     * The hasOne category relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne('Bregananta\Inventory\Models\Category', 'id', 'category_id');
    }

    /**
     * The hasOne metric relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function metric()
    {
        return $this->hasOne('Bregananta\Inventory\Models\Metric', 'id', 'metric_id');
    }

    /**
     * The hasOne sku relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sku()
    {
        return $this->hasOne('Bregananta\Inventory\Models\InventorySku', 'inventory_id', 'id');
    }

    /**
     * The hasMany stocks relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany('Bregananta\Inventory\Models\InventoryStock', 'inventory_id', 'id');
    }

    /**
     * The belongsToMany suppliers relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function suppliers()
    {
        return $this->belongsToMany('Bregananta\Inventory\Models\Supplier', 'inventory_suppliers', 'inventory_id')->withTimestamps();
    }

    /**
     * The belongsToMany assemblies relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assemblies()
    {
        return $this->belongsToMany($this, 'inventory_assemblies', 'inventory_id', 'part_id')->withPivot(['quantity'])->withTimestamps();
    }
}
