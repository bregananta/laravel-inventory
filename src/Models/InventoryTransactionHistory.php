<?php

namespace Bregananta\Inventory\Models;

use Bregananta\Inventory\Traits\InventoryTransactionHistoryTrait;

/**
 * Class InventoryTransactionPeriod.
 */
class InventoryTransactionHistory extends BaseModel
{
    use InventoryTransactionHistoryTrait;

    protected $table = 'tb_inventory_transaction_histories';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'state_before',
        'state_after',
        'quantity_before',
        'quantity_after',
    ];

    /**
     * The belongsTo transaction relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('Bregananta\Inventory\Models\InventoryTransaction', 'transaction_id', 'id');
    }
}
