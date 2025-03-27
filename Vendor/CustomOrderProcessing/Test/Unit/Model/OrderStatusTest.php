<?php
namespace Vendor\CustomOrderProcessing\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Vendor\CustomOrderProcessing\Model\OrderStatus;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class OrderStatusTest extends TestCase
{
    private $orderStatus;
    private $orderRepositoryMock;
    private $orderMock;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->orderMock = $this->createMock(Order::class);
        $this->orderStatus = new OrderStatus($this->orderRepositoryMock);
    }

    public function testUpdateOrderStatusSuccessfully()
    {
        $this->orderMock->method('getId')->willReturn(1);
        $this->orderMock->expects($this->once())->method('setStatus')->with('processing');
        $this->orderRepositoryMock->method('get')->willReturn($this->orderMock);
        $this->orderRepositoryMock->expects($this->once())->method('save')->with($this->orderMock);

        $result = $this->orderStatus->updateOrderStatus('000000002', 'processing');
        $this->assertEquals('Order status updated successfully.', $result);
    }

    public function testUpdateOrderStatusOrderNotFound()
    {
        $this->orderRepositoryMock->method('get')->willThrowException(new NoSuchEntityException(__('Order not found.')));
        $result = $this->orderStatus->updateOrderStatus('100000002', 'complete');
        $this->assertEquals('Error: Order not found.', $result);
    }

    public function testUpdateOrderStatusError()
    {
        $this->orderMock->method('getId')->willReturn(1);
        $this->orderMock->method('setStatus')->willThrowException(new LocalizedException(__('Status update failed.')));
        $this->orderRepositoryMock->method('get')->willReturn($this->orderMock);

        $result = $this->orderStatus->updateOrderStatus('000000002', 'complete');
        $this->assertStringContainsString('Error:', $result);
    }
}
