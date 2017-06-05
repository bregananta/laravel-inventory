<?php

namespace Bregananta\Inventory\Commands;

use Bregananta\Inventory\Exceptions\Commands\DatabaseTableReservedException;
use Bregananta\Inventory\Exceptions\Commands\DependencyNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Command;

/**
 * Class SchemaCheckCommand.
 */
class SchemaCheckCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inventory:check-schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the current database to make sure the required tables are present, and the reserved tables are not';

    /**
     * Holds the database tables that
     * must be present before install.
     *
     * @var array
     */
    protected $dependencies = [
        'users' => 'Sentry, Sentinel or Laravel',
    ];

    /**
     * Holds the reserved database tables that
     * cannot exist before installation.
     *
     * @var array
     */
    protected $reserved = [
        'tb_inventory_metrics',
        'tb_inventory_locations',
        'tb_inventory_categories',
        'tb_inventory_suppliers',
        'tb_inventory_inventory',
        'tb_inventory_skus',
        'tb_inventory_stocks',
        'tb_inventory_stock_movements',
        'tb_inventory_inventory_suppliers',
        'tb_inventory_transactions',
        'tb_inventory_transaction_histories',
        'tb_inventory_assemblies',
    ];

    /**
     * Executes the console command.
     *
     * @throws DatabaseTableReservedException
     * @throws DependencyNotFoundException
     */
    public function fire()
    {
        if ($this->checkDependencies()) {
            $this->info('Schema dependencies are all good!');
        }

        if ($this->checkReserved()) {
            $this->info('Schema reserved tables are all good!');
        }
    }

    /**
     * Checks the current database for dependencies.
     *
     * @throws DependencyNotFoundException
     *
     * @return bool
     */
    private function checkDependencies()
    {
        foreach ($this->dependencies as $table => $suppliedBy) {
            if (!$this->tableExists($table)) {
                $message = sprintf('Table: %s does not exist, it is supplied by %s', $table, $suppliedBy);

                throw new DependencyNotFoundException($message);
            }
        }

        return true;
    }

    /**
     * Checks the current database for reserved tables.
     *
     * @throws DatabaseTableReservedException
     *
     * @return bool
     */
    private function checkReserved()
    {
        foreach ($this->reserved as $table) {
            if ($this->tableExists($table)) {
                $message = sprintf('Table: %s already exists. This table is reserved. Please remove the database table to continue', $table);

                throw new DatabaseTableReservedException($message);
            }
        }

        return true;
    }

    /**
     * Returns true / false if the current
     * database table exists.
     *
     * @param string $table
     *
     * @return bool
     */
    private function tableExists($table)
    {
        return Schema::hasTable($table);
    }
}
