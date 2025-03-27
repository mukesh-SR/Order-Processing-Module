<?php
namespace Vendor\CustomOrderProcessing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderNotifier;
use Magento\Framework\Exception\LocalizedException;

class OrderStatusChange implements ObserverInterface
{
    protected $resource;
    protected $logger;
    protected $orderNotifier;

    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger,
        OrderNotifier $orderNotifier
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->orderNotifier = $orderNotifier;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $oldStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();

        if ($oldStatus !== $newStatus) {
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('order_status_log');

            // Log order status change into database
            $connection->insert($tableName, [
                'order_id'   => $order->getId(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => date('Y-m-d H:i:s'),
            ]);

            // Log to Magento system log
            $this->logger->info("Order ID {$order->getId()} status changed from {$oldStatus} to {$newStatus}");

            // If the order is marked as shipped, trigger an email using OrderNotifier
            if ($newStatus === Order::STATE_COMPLETE) {
                try {
                    $this->orderNotifier->notify($order);
                    $this->logger->info("Order complete email sent for Order ID {$order->getId()}");
                } catch (LocalizedException $e) {
                    $this->logger->error("Error sending order complete email: " . $e->getMessage());
                }
            }
        }
    }
}
