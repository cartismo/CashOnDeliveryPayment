<?php

namespace Modules\CashOnDeliveryPayment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\HasMultiStoreModuleSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\CashOnDeliveryPayment\Services\CashOnDeliveryPaymentService;

class SettingsController extends Controller
{
    use HasMultiStoreModuleSettings;

    protected function getModuleSlug(): string
    {
        return 'cash-on-delivery-payment';
    }

    protected function getDefaultSettings(): array
    {
        return CashOnDeliveryPaymentService::defaultSettings();
    }

    public function index(): Response
    {
        $data = $this->getMultiStoreData();
        $data['translations'] = __('cashondeliverypayment::settings');

        return Inertia::render('CashOnDeliveryPayment::Admin/Settings', $data);
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