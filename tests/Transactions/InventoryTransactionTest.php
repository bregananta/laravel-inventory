<?php

namespace Bregananta\Inventory\Tests\Transactions;

use Bregananta\Inventory\Models\InventoryTransactionHistory;
use Bregananta\Inventory\Models\InventoryTransaction;
use Bregananta\Inventory\Tests\InventoryStockTest;

class InventoryTransactionTest extends InventoryStockTest
{
    public function setUp()
    {
        parent::setUp();

        InventoryTransaction::flushEventListeners();
        InventoryTransaction::boot();

        InventoryTransactionHistory::flushEventListeners();
        InventoryTransactionHistory::boot();
    }

    /**
     * Returns a new stock transaction for easier testing.
     *
     * @return mixed
     */
    protected function newTransaction()
    {
        $stock = $this->newInventoryStock();

        return $stock->newTransaction();
    }

    public function testInventoryTransactionStockNotFoundException()
    {
        $transaction = $this->newTransaction();

        $transaction->stock_id = 15;

        $this->setExpectedException('Bregananta\Inventory\Exceptions\StockNotFoundException');

        $transaction->getStockRecord();
    }

    public function testInventoryTransactionSetStateFailure()
    {
        $transaction = $this->newTransaction();

        $this->setExpectedException('Bregananta\Inventory\Exceptions\InvalidTransactionStateException');

        $transaction->state = 'test';
    }

    public function testInventoryTransactionSetStateSuccess()
    {
        $transaction = $this->newTransaction();

        $transaction->state = InventoryTransaction::STATE_COMMERCE_RESERVED;
    }

    public function testInventoryTransactionGetByState()
    {
        $transaction = $this->newTransaction();

        $transaction->reserved(2);

        $results = InventoryTransaction::getByState(InventoryTransaction::STATE_COMMERCE_RESERVED);

        $this->assertEquals(1, $results->count());
    }
}
