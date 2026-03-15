<?php

namespace Modules\CashOnDeliveryPayment\Tests\Unit;

use App\Contracts\AbstractPaymentMethod;
use App\Models\Currency;
use App\Models\InstalledModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

require_once dirname(__DIR__, 2) . '/Services/CashOnDeliveryPaymentService.php';

use Modules\CashOnDeliveryPayment\Services\CashOnDeliveryPaymentService;

class CashOnDeliveryPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_service_exposes_offline_payment_contract(): void
    {
        Currency::query()->create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => 'EUR',
            'symbol_left' => 'EUR ',
            'symbol_right' => null,
            'decimal_places' => 2,
            'exchange_rate' => 1,
            'is_base' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $module = new InstalledModule([
            'slug' => 'cash-on-delivery-payment',
            'name' => 'Cash on Delivery',
            'settings' => [
                'enabled' => true,
                'title' => 'Cash on Delivery',
                'description' => 'Pay when you receive the order',
                'fee_type' => 'fixed',
                'fee_amount' => 2.50,
                'order_status' => 'processing',
            ],
        ]);

        $service = new CashOnDeliveryPaymentService($module);
        $method = $service->getPaymentMethod(50);

        $this->assertSame(AbstractPaymentMethod::TYPE_OFFLINE, $service->getType());
        $this->assertSame('banknotes', $service->getIcon());
        $this->assertSame('processing', $service->getOrderStatus());
        $this->assertNotNull($method);
        $this->assertSame(2.50, $method['fee']);
        $this->assertSame(AbstractPaymentMethod::TYPE_OFFLINE, $method['type']);
        $this->assertIsString($method['formatted_fee']);
    }
}
