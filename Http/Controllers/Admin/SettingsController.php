<?php

namespace Modules\CashOnDeliveryPayment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\HasMultiStoreModuleSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    use HasMultiStoreModuleSettings;

    protected function getModuleSlug(): string
    {
        return 'cash-on-delivery-payment';
    }

    protected function getDefaultSettings(): array
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

    public function index(): Response
    {
        return Inertia::render('CashOnDeliveryPayment::Admin/Settings', $this->getMultiStoreData());
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'is_enabled' => 'boolean',
            'settings.enabled' => 'boolean',
            'settings.title' => 'required|string|max:255',
            'settings.description' => 'nullable|string|max:1000',
            'settings.instructions' => 'nullable|string|max:2000',
            'settings.fee_type' => 'required|in:none,fixed,percentage',
            'settings.fee_amount' => 'nullable|numeric|min:0',
            'settings.minimum_order_amount' => 'nullable|numeric|min:0',
            'settings.maximum_order_amount' => 'nullable|numeric|min:0',
            'settings.order_status' => 'string|max:50',
            'settings.sort_order' => 'integer|min:0',
        ]);

        return $this->saveStoreSettings($request);
    }
}