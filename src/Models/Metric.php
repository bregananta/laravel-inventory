<?php

namespace Bregananta\Inventory\Models;

/**
 * Class Metric.
 */
class Metric extends BaseModel
{
    protected $table = 'tb_inventory_metrics';

    /**
     * The hasMany inventory items relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('Bregananta\Inventory\Models\Inventory', 'metric_id', 'id');
    }
}
