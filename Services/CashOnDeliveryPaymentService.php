<?php

namespace Modules\CashOnDeliveryPayment\Services;

use App\Contracts\AbstractPaymentMethod;

class CashOnDeliveryPaymentService extends AbstractPaymentMethod
{
    public static function defaultSettings(): array
    {
        return [
            'enabled' => false,
            'title' => 'Cash on Delivery',
            'description' => 'Pay with cash when your order is delivered.',
            'instructions' => 'Please have the exact amount ready when the courier arrives.',
            'fee_type' => 'none',
            'fee_amount' => 0,
            'minimum_order_amount' => null,
            'maximum_order_amount' => null,
            'order_status' => 'processing',
            'sort_order' => 0,
        ];
    }

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