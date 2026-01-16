<?php

return [
    'name' => 'CashOnDeliveryPayment',

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'enabled' => false,
        'title' => 'Cash on Delivery',
        'description' => 'Pay with cash when your order is delivered.',
        'instructions' => 'Please have the exact amount ready when the courier arrives.',
        'fee_type' => 'none', // none, fixed, percentage
        'fee_amount' => 0,
        'minimum_order_amount' => null,
        'maximum_order_amount' => null,
        'order_status' => 'processing',
        'sort_order' => 0,
    ],
];