# Stripe Laravel Cashier

Install laravel cashier first, and run migrations.

## Stripe weebhook secret

```sh
stripe listen
```

## Keys

.env

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

CASHIER_CURRENCY=pln
CASHIER_CURRENCY_LOCALE=pl_PL
```

## Strip routes

/routes/stripe/routes.php

## Stripe custom checkout (stripe.js)

### Shop page custom checkout example with Web Elements

/stripe/custom-payment

### Capture checkout payment

/stripe/custom-payment/capture/{pi_3PFM...}

## Strip payment

### Donation example

/stripe/charge

### Subscription example

/stripe/charge

## Cashier payment

### Cashier checkout example

/checkout

## Docs

- <https://docs.stripe.com/payments/payment-element>
- <https://docs.stripe.com/payments/quickstart>
- <https://docs.stripe.com/elements/address-element>
- <https://docs.stripe.com/payments/elements/link-authentication-element>

- <https://docs.stripe.com/api>
- <https://docs.stripe.com/stripe-cli>
- <https://docs.stripe.com/payments/elements>

- <https://docs.stripe.com/api/payment_intents/create>
- <https://docs.stripe.com/api/checkout/sessions/create>

- <https://docs.stripe.com/api/events/types>
- <https://docs.stripe.com/api/payment_intents/object>
- <https://docs.stripe.com/api/checkout/sessions/object>

## Screen

<img src="https://raw.githubusercontent.com/atomjoy/stripe-laravel/main/shop-page-custom-checkout.png" width="100%">
