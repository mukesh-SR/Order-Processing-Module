<?php
namespace Vendor\CustomOrderProcessing\Test\Integration\Observer;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Sales\Model\Order;
use Magento\Framework\App\ResourceConnection;
use PHPUnit\Framework\TestCase;

class OrderStatusChangeTest extends TestCase
{
    private $order;
    private $resource;

    protected function setUp(): void
    {
        $this->resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $this->order = Bootstrap::getObjectManager()->create(Order::class);
    }

    public function testOrderStatusChangeLog()
    {
        $this->order->loadByIncrementId('000000001');
        $this->order->setStatus(Order::STATE_PROCESSING);
        $this->order->save();

        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('order_status_log');
        $logEntry = $connection->fetchRow("SELECT * FROM {$tableName} ORDER BY changed_at DESC LIMIT 1");

        $this->assertNotEmpty($logEntry);
        $this->assertEquals($this->order->getId(), $logEntry['order_id']);
        $this->assertEquals(Order::STATE_PROCESSING, $logEntry['new_status']);
    }
}
