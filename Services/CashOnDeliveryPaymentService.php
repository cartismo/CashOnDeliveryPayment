<?php

namespace Modules\CashOnDeliveryPayment\Services;

use App\Contracts\AbstractPaymentMethod;

class CashOnDeliveryPaymentService extends AbstractPaymentMethod
{
    /**
     * Get icon identifier for this payment method
     */
    public function getIcon(): string
    {
        return $this->settings['icon'] ?? 'banknotes';
    }

    /**
     * Get payment type: always offline for COD
     */
    public function getType(): string
    {
        return self::TYPE_OFFLINE;
    }

    /**
     * Get default order status for COD orders
     */
    public function getOrderStatus(): string
    {
        return $this->settings['order_status'] ?? 'processing';
    }
}