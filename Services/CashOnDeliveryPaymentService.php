<?php

namespace Modules\CashOnDeliveryPayment\Services;

use App\Models\InstalledModule;

class CashOnDeliveryPaymentService
{
    protected ?array $settings = null;

    /**
     * Get module settings
     */
    public function getSettings(): array
    {
        if ($this->settings === null) {
            $module = InstalledModule::where('slug', 'cash-on-delivery-payment')->first();
            $this->settings = $module?->settings ?? config('cashondeliverypayment.defaults', []);
        }

        return $this->settings;
    }

    /**
     * Check if cash on delivery is enabled
     */
    public function isEnabled(): bool
    {
        return $this->getSettings()['enabled'] ?? false;
    }

    /**
     * Get the display title
     */
    public function getTitle(): string
    {
        return $this->getSettings()['title'] ?? 'Cash on Delivery';
    }

    /**
     * Get the description
     */
    public function getDescription(): string
    {
        return $this->getSettings()['description'] ?? '';
    }

    /**
     * Calculate the COD fee for a given order total
     */
    public function calculateFee(float $orderTotal): float
    {
        $settings = $this->getSettings();
        $feeType = $settings['fee_type'] ?? 'none';
        $feeAmount = (float) ($settings['fee_amount'] ?? 0);

        return match ($feeType) {
            'fixed' => $feeAmount,
            'percentage' => round($orderTotal * ($feeAmount / 100), 2),
            default => 0,
        };
    }

    /**
     * Check if COD is available for a given order total
     */
    public function isAvailableForAmount(float $orderTotal): bool
    {
        $settings = $this->getSettings();

        $minAmount = $settings['minimum_order_amount'] ?? null;
        $maxAmount = $settings['maximum_order_amount'] ?? null;

        if ($minAmount !== null && $orderTotal < $minAmount) {
            return false;
        }

        if ($maxAmount !== null && $orderTotal > $maxAmount) {
            return false;
        }

        return true;
    }

    /**
     * Get the payment instructions
     */
    public function getInstructions(): string
    {
        return $this->getSettings()['instructions'] ?? '';
    }

    /**
     * Get the order status after payment
     */
    public function getOrderStatus(): string
    {
        return $this->getSettings()['order_status'] ?? 'processing';
    }
}