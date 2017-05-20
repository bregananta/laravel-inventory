## Installation

### Installation (Laravel 4)

Add inventory to your `composer.json` file:

    "Bregananta/inventory" : "1.6.*"

Now perform a `composer update` on your project's source.

Then insert the service provider in your `app/config/app.php` config file:

    'Bregananta\Inventory\InventoryServiceProvider'

If you want to customize the database tables, you can publish the migration and run it yourself:

    php artisan migrate:publish Bregananta/inventory

And then run the migration:

    php artisan migrate
    
Otherwise you can run the install command:

    php artisan inventory:install
    
Be sure to publish the configuration if you'd like to customize inventory:
    
    php artisan config:publish Bregananta/inventory
    
### Installation (Laravel 5)

Add inventory to your `composer.json` file:

    "Bregananta/inventory" : "1.6.*"

Now perform a `composer update` on your project's source.

Then insert the service provider in your `config/app.php` config file:

    'Bregananta\Inventory\InventoryServiceProvider'
    
Either publish the assets to customize the database tables using:

    php artisan vendor:publish
   
And then run the migrations:

    php artisan migrate
    
Or use the inventory install command:

    php artisan inventory:install

## Customize Installation

### I don't need to customize my models

If you don't need to create & customize your models, I've included pre-built models.

If you'd like to use them you'll have include them in your use statements:

    use Bregananta\Inventory\Models\Inventory;
    
    class InventoryController extends BaseController
    {
        /*
        * Holds the inventory model
        *
        * @var Inventory
        */
        protected $inventory;
    
        public function __construct(Inventory $inventory)
        {
            $this->inventory = $inventory;
        }
        
        public function create()
        {
            $item = new $this->inventory;
            
            // etc...
        }
    }

### I want to customize my models

Create the models, but keep in mind the models need:

- The shown fillable attribute arrays (needed for dynamically creating & updating relationship records)
- The shown relationship names (such as `stocks()`)
- The shown relationship type (such as hasOne, hasMany, belongsTo etc)

You are free to modify anything else (such as table names, model names, namespace etc!).

Metric:

    class Metric extends Model
    {
        protected $table = 'metrics';
    }
    
Location:
    
    use Baum\Node;
    
    class Location extends Node 
    {
        protected $table = 'locations';
    }

Category:

    use Bregananta\Inventory\Traits\CategoryTrait;
    use Baum\Node;
    
    class Category extends Node
    {
        protected $table = 'categories';
        
        protected $scoped = ['belongs_to'];
        
        public function inventories()
        {
            return $this->hasMany('Inventory', 'category_id');
        }
    }

Supplier:

    use Bregananta\Inventory\Traits\SupplierTrait;
    
    class Supplier extends BaseModel
    {
        use SupplierTrait;
    
        protected $table = 'suppliers';
        
         public function items()
        {
            return $this->belongsToMany('Inventory', 'inventory_suppliers', 'supplier_id')->withTimestamps();
        }
    }

Inventory:

    use Bregananta\Inventory\Traits\InventoryTrait;
    use Bregananta\Inventory\Traits\InventoryVariantTrait;
    
    class Inventory extends Model
    {
        use InventoryTrait;
        use InventoryVariantTrait;
    
        protected $table = 'inventory';
        
        public function category()
        {
            return $this->hasOne('Category', 'id', 'category_id');
        }
        
        public function metric()
        {
            return $this->hasOne('Metric', 'id', 'metric_id');
        }
        
        public function sku()
        {
            return $this->hasOne('InventorySku', 'inventory_id', 'id');
        }
        
        public function stocks()
        {
            return $this->hasMany('InventoryStock', 'inventory_id');
        }
        
        public function suppliers()
        {
            return $this->belongsToMany('Supplier', 'inventory_suppliers', 'inventory_id')->withTimestamps();
        }
    }
    
InventorySku:

    use Bregananta\Inventory\Traits\InventorySkuTrait;
    
    class InventorySku extends Model
    {
        use InventorySkuTrait;
    
        protected $table = 'inventory_skus';
        
        protected $fillable = array(
            'inventory_id',
            'code',
        );
        
        public function item()
        {
            return $this->belongsTo('Inventory', 'inventory_id', 'id');
        }
    }
    
InventoryStock:

    use Bregananta\Inventory\Traits\InventoryStockTrait;
    
    class InventoryStock extends Model
    {
        use InventoryStockTrait;
    
        protected $table = 'inventory_stocks';
        
        protected $fillable = array(
            'inventory_id',
            'location_id',
            'quantity',
            'aisle',
            'row',
            'bin',
        );
    
        public function item()
        {
            return $this->belongsTo('Inventory', 'inventory_id', 'id');
        }

        public function movements()
        {
            return $this->hasMany('InventoryStockMovement', 'stock_id');
        }
        
        public function transactions()
        {
            return $this->hasMany('InventoryTransaction', 'stock_id', 'id');
        }
        
        public function location()
        {
            return $this->hasOne('Location', 'id', 'location_id');
        }
    }

InventoryStockMovement:

    use Bregananta\Inventory\Traits\InventoryStockMovementTrait;
    
    class InventoryStockMovement extends Model
    {
        use InventoryStockMovementTrait;
        
        protected $table = 'inventory_stock_movements';
        
        protected $fillable = array(
            'stock_id',
            'user_id',
            'before',
            'after',
            'cost',
            'reason',
        );
        
        public function stock()
        {
            return $this->belongsTo('InventoryStock', 'stock_id', 'id');
        }
    }
    
InventoryTransaction:
    
    use Bregananta\Inventory\Traits\InventoryTransactionTrait;
    use Bregananta\Inventory\Interfaces\StateableInterface;
    
    class InventoryTransaction extends BaseModel implements StateableInterface
    {
        use InventoryTransactionTrait;
    
        protected $table = 'inventory_transactions';
    
        protected $fillable = array(
            'user_id',
            'stock_id',
            'name',
            'state',
            'quantity',
        );
    
        public function stock()
        {
            return $this->belongsTo('InventoryStock', 'stock_id', 'id');
        }

        public function histories()
        {
            return $this->hasMany('InventoryTransactionHistory', 'transaction_id', 'id');
        }
    }
    
InventoryTransactionHistory:

    use Bregananta\Inventory\Traits\InventoryTransactionHistoryTrait;
    
    class InventoryTransactionHistory extends BaseModel
    {
        use InventoryTransactionHistoryTrait;
        
        protected $table = 'inventory_transaction_histories';
    
        protected $fillable = array(
            'user_id',
            'transaction_id',
            'state_before',
            'state_after',
            'quantity_before',
            'quantity_after',
        );
    
        public function transaction()
        {
            return $this->belongsTo('InventoryTransaction', 'transaction_id', 'id');
        }
    }
