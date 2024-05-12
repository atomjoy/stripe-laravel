<?php
// webhook.php
//
// Docs
// https://docs.stripe.com/api/checkout/sessions/object
// https://docs.stripe.com/api/events/types
// https://docs.stripe.com/payments/checkout/fulfill-orders?lang=php#delayed-notification
//
// Use this sample code to handle webhook events in your integration.
//
// 1) Paste this code into a new file (webhook.php)
//
// 2) Install dependencies
//   composer require stripe/stripe-php
//
// 3) Run the server on http://localhost:4242
//   php -S localhost:4242

require 'vendor/autoload.php';

// The library needs to be configured with your account's secret key.
// Ensure the key is kept out of any version control system you might be using.
// $stripe = new \Stripe\StripeClient(config('cashier.secret'));
\Stripe\Stripe::setApiKey(config('cashier.secret'));

# If you are testing your webhook locally with the Stripe CLI you
# can find the endpoint's secret by running `stripe listen`
# Otherwise, find your endpoint's secret in your webhook settings in
# the Developer Dashboard
$endpoint_secret = 'whsec_...';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
	$event = \Stripe\Webhook::constructEvent(
		$payload,
		$sig_header,
		$endpoint_secret
	);
} catch (\UnexpectedValueException $e) {
	// Invalid payload
	http_response_code(400);
	exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
	// Invalid signature
	http_response_code(400);
	exit();
}

function fulfillOrder($session)
{
	// TODO fill me in
}

function failedPaymentEmail($session)
{
	// TODO fill me in
}

// Handle the event
switch ($event->type) {
	case 'checkout.session.completed':
		$session = $event->data->object;
		// Status unpaid, paid, no_payment_required
		if ($session->payment_status == 'paid') {
			// Fulfill the purchased goods or services.
			fulfillOrder($session);
			// $session->amount_total;
			// $session->payment_intent;
		}
	case 'checkout.session.async_payment_succeeded':
		$session = $event->data->object;
		if ($session->payment_status == 'paid') {
			// Fulfill the purchased goods or services.
			fulfillOrder($session);
			// $session->amount_total;
			// $session->payment_intent;
		}
	case 'checkout.session.async_payment_failed':
		$session = $event->data->object;
		if ($session->payment_status == 'unpaid') {
			failedPaymentEmail($session);
		}

	case 'customer.subscription.deleted':
		$subscription = $event->data->object;
	case 'customer.subscription.updated':
		$subscription = $event->data->object;
		// ... handle other event types
	default:
		echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);

// Examples

// Retrieve the session. If you require line items in the response, you may include them by expanding line_items.
// \Stripe\Stripe::setApiKey(config('cashier.secret'));
// $session = \Stripe\Checkout\Session::retrieve([
// 	'id' => $event->data->object->id,
// 	'expand' => ['line_items'],
// ]);
// $line_items = $session->line_items;

// All charges
// $stripe = new \Stripe\StripeClient(config('cashier.secret'));
// $stripe->charges->all(['payment_intent' => '{{PAYMENT_INTENT_ID}}']);

// Last charge
// \Stripe\Stripe::setApiKey(config('cashier.secret'));
// $intent = \Stripe\PaymentIntent::retrieve('{{PAYMENT_INTENT_ID}}');
// $latest_charge = $intent->latest_charge;