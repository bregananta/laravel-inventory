<?php

namespace Bregananta\Inventory\Tests;

use Bregananta\Inventory\Models\Location;
use Bregananta\Inventory\Models\Metric;
use Bregananta\Inventory\Models\Category;
use Bregananta\Inventory\Models\InventoryStock;
use Bregananta\Inventory\Models\Inventory;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Model as Eloquent;

class InventoryTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        Eloquent::unguard();
    }

    /**
     * @param array $attributes
     *
     * @return Inventory
     */
    protected function newInventory(array $attributes = [])
    {
        $metric = $this->newMetric();

        $category = $this->newCategory();

        if(count($attributes) > 0) {
            return Inventory::create($attributes);
        }

        return Inventory::create([
            'metric_id' => $metric->id,
            'category_id' => $category->id,
            'name' => 'Milk',
            'description' => 'Delicious Milk',
        ]);
    }

    /**
     * @return Metric
     */
    protected function newMetric()
    {
        return Metric::create([
            'name' => 'Litres',
            'symbol' => 'L',
        ]);
    }

    /**
     * @return Location
     */
    protected function newLocation()
    {
        return Location::create([
            'name' => 'Warehouse',
            'belongs_to' => '',
        ]);
    }

    /**
     * @return Category
     */
    protected function newCategory()
    {
        return Category::create([
            'name' => 'Drinks',
        ]);
    }

    public function testInventoryHasMetric()
    {
        $item = $this->newInventory();

        $this->assertTrue($item->hasMetric());
    }

    public function testInventoryDoesNotHaveMetric()
    {
        $item = $this->newInventory();

        $metric = Metric::find(1);
        $metric->delete();

        $this->assertFalse($item->hasMetric());
    }

    public function testInventoryCreateStockOnLocation()
    {
        $item = $this->newInventory();

        $location = $this->newLocation();

        Lang::shouldReceive('get')->once();

        $item->createStockOnLocation(10, $location);

        $stock = InventoryStock::find(1);

        $this->assertEquals(10, $stock->quantity);
    }

    public function testInventoryNewStockOnLocation()
    {
        $item = $this->newInventory();

        $location = $this->newLocation();

        $stock = $item->newStockOnLocation($location);

        $this->assertEquals(1, $stock->inventory_id);
        $this->assertEquals(1, $stock->location_id);
    }

    public function testInventoryNewStockOnLocationFailure()
    {
        $item = $this->newInventory();

        $location = $this->newLocation();

        $stock = $item->newStockOnLocation($location);
        $stock->save();

        $this->setExpectedException('Bregananta\Inventory\Exceptions\StockAlreadyExistsException');

        $item->newStockOnLocation($location);
    }

    public function testInventoryInvalidQuantityException()
    {
        $item = $this->newInventory();

        $location = $this->newLocation();

        Lang::shouldReceive('get')->once();

        $this->setExpectedException('Bregananta\Inventory\Exceptions\InvalidQuantityException');

        $item->createStockOnLocation('invalid quantity', $location);
    }

    public function testInventoryHasCategory()
    {
        $item = $this->newInventory();

        $this->assertTrue($item->hasCategory());
    }

    public function testInventoryDoesNotHaveCategory()
    {
        $this->newInventory();

        $item = Inventory::find(1);
        $item->category_id = null;
        $item->save();

        $this->assertFalse($item->hasCategory());
    }
}
