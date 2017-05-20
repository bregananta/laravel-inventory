<?php

namespace Bregananta\Inventory\Models;

use Bregananta\Inventory\Traits\InventoryStockMovementTrait;

/**
 * Class InventoryStockMovement.
 */
class InventoryStockMovement extends BaseModel
{
    use InventoryStockMovementTrait;

    protected $table = 'inventory_stock_movements';

    protected $fillable = [
        'stock_id',
        'user_id',
        'before',
        'after',
        'cost',
        'reason',
    ];

    /**
     * The belongsTo stock relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo('Bregananta\Inventory\Models\InventoryStock', 'stock_id', 'id');
    }
}
