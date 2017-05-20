## Variants

In update `1.6`, variants were added. This allows you to create multiple variations of an item, and track their stock
individually.

#### Make Variant Of

To make an item a variant of another, use the `makeVariantOf($item)` method:

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
    
    $cherryCoke->makeVariantOf($coke);

#### Get Variants

To retrieve all variants of an item, use the `getVariants()` method:

> **Note**: This method is non-recursive, meaning if you have variants of variants, only the single level
> is returned.

    $variants = $item->getVariants();
    
    foreach($variants as $variant)
    {
        echo $variant->name;
    }

#### New Variant

To instantiate a new variant of another item, use the `newVariant($name = '')` method:

    $coke = Inventory::find(1);
    
    $cherryCoke = $coke->newVariant('Cherry Coke');
    
    $cherryCoke->save();
    
Or just assign the name of the variant at a later time:
    
    $coke = Inventory::find(1);
        
    $cherryCoke = $coke->newVariant();
    
    $cherryCoke->name = 'Cherry Coke';
    
    $cherryCoke->save();

Don't worry about assigning the metric ID and category ID for the variant, this is done automatically.
However, feel free to override these if you wish:

    $coke = Inventory::find(1);
            
    $cherryCoke = $coke->newVariant();
    
    $cherryCoke->name = 'Cherry Coke';
    $cherryCoke->metric_id = 2;
    $cherryCoke->category_id = 2;
    
    $cherryCoke->save();

#### Create Variant

To create a variant, use the `createVariant($name = '', $description = '', $categoryId = null, $metricId = null)` method:

    $coke = Inventory::find(1);
    
    $cherryCoke = $coke->createVariant('Cherry Coke', 'Delicious Cherry Coke');

#### Is Variant

To ask an item if it's a variant, use the `isVariant()` method:

    if($item->isVariant())
    {
        echo "I'm a variant of another item!";    
    }

#### Get Parent

To retrieve the parent item of a variant, use the `getParent()` method:

    if($item->isVariant())
    {
        $parent = $item->getParent();
        
        echo $parent->name;
    }

#### Get Total Variant Stock (Added in `1.7.1`)

To retrieve the total stock of all an items variants, use the `getTotalVariantStock($recursive = true)` method:
    
    // The current items stock
    $itemStock = $item->getTotalStock();
    
    // The current items variant stock
    $itemVariantStock = $item->getTotalVariantStock();
    
    // The complete amount of stock
    $completeStock = $itemStock + $itemVariantStock;

> **Note**: By default this method is recursive, and retrieves variants of variants. Pass in `false` into the first
> parameter if you only want immediate children stock.

