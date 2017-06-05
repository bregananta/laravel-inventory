<?php

namespace Bregananta\Inventory\Models;

use Bregananta\Inventory\Traits\InventorySkuTrait;

/**
 * Class InventorySku.
 */
class InventorySku extends BaseModel
{
    use InventorySkuTrait;

    protected $table = 'tb_inventory_skus';

    protected $fillable = [
        'inventory_id',
        'code',
    ];

    /**
     * The belongsTo item trait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('Bregananta\Inventory\Models\Inventory', 'inventory_id', 'id');
    }
}
