<?php

namespace Bregananta\Inventory\Models;

use Bregananta\Inventory\Traits\InventoryTransactionTrait;
use Bregananta\Inventory\Interfaces\StateableInterface;

/**
 * Class InventoryTransaction.
 */
class InventoryTransaction extends BaseModel implements StateableInterface
{
    use InventoryTransactionTrait;

    protected $table = 'inventory_transactions';

    protected $fillable = [
        'user_id',
        'stock_id',
        'name',
        'state',
        'quantity',
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

    /**
     * The hasMany histories relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        return $this->hasMany('Bregananta\Inventory\Models\InventoryTransactionHistory', 'transaction_id', 'id');
    }
}
