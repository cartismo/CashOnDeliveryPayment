# CashOnDeliveryPayment

Cash on Delivery (COD) payment method for Cartismo stores.

## Features

- Accept cash payments upon delivery
- Configurable COD fee (fixed amount or percentage)
- Minimum and maximum order amount restrictions
- Custom instructions shown to customers after order
- Per-store configuration (multi-store support)
- 8 languages: BG, EN, MK, SR, RO, TR, EL, CS

## Requirements

- Cartismo 1.0+
- PHP 8.2+

## Configuration

After installation, go to **Admin > Modules > CashOnDeliveryPayment > Settings** to configure:

- **Enable/Disable** per store
- **Display Title** — shown at checkout
- **Fee Type** — none, fixed amount, or percentage of order total
- **Order Restrictions** — minimum and maximum order amounts
- **Instructions** — shown on order confirmation page and email

## How It Works

1. Customer selects "Cash on Delivery" at checkout
2. Order is created with status "processing"
3. Customer sees instructions on confirmation page
4. Courier collects payment on delivery

## Technical

- Extends `AbstractPaymentMethod` (implements `PaymentMethodInterface`)
- Type: `offline` (no online payment processing)
- Icon: `banknotes` (Heroicons)
- Settings stored in `InstalledModule.settings` JSON per store