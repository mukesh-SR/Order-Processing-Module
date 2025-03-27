<?php
namespace Vendor\CustomOrderProcessing\Api;

interface OrderStatusInterface
{
    /**
     * Update order status by increment ID
     * 
     * @param string $incrementId
     * @param string $status
     * @return string
     */
    public function updateOrderStatus($incrementId, $status);
}