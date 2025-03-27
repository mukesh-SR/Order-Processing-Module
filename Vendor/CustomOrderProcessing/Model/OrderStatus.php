<?php
namespace Vendor\CustomOrderProcessing\Model;

use Vendor\CustomOrderProcessing\Api\OrderStatusInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class OrderStatus implements OrderStatusInterface
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;

    }

    /**
     * Update order status by increment ID
     *
     * @param string $incrementId
     * @param string $status
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function updateOrderStatus($incrementId, $status): string
    {
        try {

            $order = $this->orderRepository->get($incrementId);

            if (!$order->getId()) {
                throw new NoSuchEntityException(__('Order not found.'));
            }

             $order->setStatus($status);
             $order->setState(Order::STATE_PROCESSING);
             $this->orderRepository->save($order);
            return __('Order status updated successfully.');
        } catch (NoSuchEntityException $e) {
            return __('Error: ') . $e->getMessage();
        } catch (LocalizedException $e) {
            return __('Error: ') . $e->getMessage();
        } catch (\Exception $e) {
            return __('Error: Something went wrong while updating order status.');
        }
    }
}
