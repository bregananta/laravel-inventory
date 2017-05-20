<?php

namespace Bregananta\Inventory\Tests;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Bregananta\Inventory\Models\Inventory;

class InventoryVariantTest extends InventoryTest
{
    public function testNewVariant()
    {
        $this->newInventory();

        $milk = Inventory::find(1);

        $chocolateMilk = $milk->newVariant();

        $chocolateMilk->name = 'Chocolate Milk';

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $chocolateMilk->save();

        $this->assertEquals($chocolateMilk->parent_id, $milk->id);
        $this->assertEquals($chocolateMilk->category_id, $milk->category_id);
        $this->assertEquals($chocolateMilk->metric_id, $milk->metric_id);
    }

    public function testCreateVariant()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $name = 'Cherry Coke';
        $description = 'Delicious Cherry Coke';

        $cherryCoke = $coke->createVariant($name, $description);

        $this->assertTrue($cherryCoke->isVariant());
        $this->assertEquals(1, $cherryCoke->parent_id);
        $this->assertEquals($name, $cherryCoke->name);
        $this->assertEquals($description, $cherryCoke->description);
        $this->assertEquals(1, $cherryCoke->category_id);
        $this->assertEquals(1, $cherryCoke->metric_id);
    }

    public function testMakeVariant()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        $cherryCoke = Inventory::create([
            'name' => 'Cherry Coke',
            'description' => 'Delicious Cherry Coke',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $cherryCoke->makeVariantOf($coke);

        $this->assertEquals($cherryCoke->parent_id, $coke->id);
    }

    public function testIsVariant()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        $cherryCoke = $coke->createVariant('Cherry Coke');

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $cherryCoke->makeVariantOf($coke);

        $this->assertFalse($coke->isVariant());
        $this->assertTrue($cherryCoke->isVariant());
    }

    public function testGetVariants()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        $cherryCoke = $coke->createVariant('Cherry Coke');

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $cherryCoke->makeVariantOf($coke);

        $variants = $coke->getVariants();

        $this->assertInstanceOf('Illuminate\Support\Collection', $variants);
        $this->assertEquals(1, $variants->count());
    }

    public function testGetVariantsRecursive()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        $cherryCoke = $coke->createVariant('Cherry Coke');

        $vanillaCherryCoke = $cherryCoke->createVariant('Vanilla Cherry Coke');

        $vanillaLimeCherryCoke = $vanillaCherryCoke->createVariant('Vanilla Lime Cherry Coke');

        $variants = $coke->getVariants(true);

        $this->assertEquals($cherryCoke->name, $variants->get(0)->name);
        $this->assertEquals($vanillaCherryCoke->name, $variants->get(0)->variants->get(0)->name);
        $this->assertEquals($vanillaLimeCherryCoke->name, $variants->get(0)->variants->get(0)->variants->get(0)->name);
    }

    public function testGetParent()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        $cherryCoke = Inventory::create([
            'name' => 'Cherry Coke',
            'description' => 'Delicious Cherry Coke',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $cherryCoke->makeVariantOf($coke);

        $parent = $cherryCoke->getParent();

        $this->assertEquals('Coke', $parent->name);
        $this->assertEquals(null, $parent->parent_id);
    }

    public function testGetTotalVariantStock()
    {
        $this->newCategory();
        $this->newMetric();

        $coke = Inventory::create([
            'name' => 'Coke',
            'description' => 'Delicious Pop',
            'metric_id' => 1,
            'category_id' => 1,
        ]);

        $cherryCoke = $coke->createVariant('Cherry Coke');

        $cherryCoke->makeVariantOf($coke);

        $vanillaCherryCoke = $cherryCoke->createVariant('Vanilla Cherry Coke');

        $vanillaCherryCoke->makeVariantOf($cherryCoke);

        DB::shouldReceive('beginTransaction')->once()->andReturn(true);
        DB::shouldReceive('commit')->once()->andReturn(true);

        Event::shouldReceive('fire')->once()->andReturn(true);

        $location = $this->newLocation();

        // Allow duplicate movements configuration option
        Config::shouldReceive('get')->twice()->andReturn(true);

        // Stock change reasons (one for create, one for put, for both items)
        Lang::shouldReceive('get')->times(4)->andReturn('Default Reason');

        $cherryCoke->createStockOnLocation(20, $location);

        $vanillaCherryCoke->createStockOnLocation(20, $location);

        $this->assertEquals(40, $coke->getTotalVariantStock());
    }
}
