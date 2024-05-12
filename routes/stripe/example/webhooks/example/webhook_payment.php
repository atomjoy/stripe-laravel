<?php
// https://docs.stripe.com/api/events/types

\Stripe\Stripe::setApiKey(config('cashier.secret'));

// This is your Stripe CLI webhook secret for testing your endpoint locally.
$endpoint_secret = 'whsec_..';

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

// Handle the event
// https://docs.stripe.com/api/events/types
switch ($event->type) {

	case 'payment_intent.succeeded':
		$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
		//handlePaymentIntentSucceeded($paymentIntent);
		echo $paymentIntent->id;
		// echo $paymentIntent->last_charge;
		break;
	case 'payment_intent.created':
		$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
		//handlePaymentIntentCreated($paymentIntent);
		echo $paymentIntent->id;
		break;
	case 'payment_intent.canceled':
		$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
		//handlePaymentIntentCanceled($paymentIntent);
		echo $paymentIntent->id;
		break;
	case 'payment_intent.amount_capturable_updated':
		$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
		//handlePaymentIntentCapturable($paymentIntent);
		echo $paymentIntent->id;
		break;
	case 'payment_intent.payment_failed':
		$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
		//handlePaymentIntentFailed($paymentIntent);
		echo $paymentIntent->id;
		echo $paymentIntent->last_payment_error ? $paymentIntent->last_payment_error->message : "";
		break;

	case 'payment_method.attached':
		$paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
		// handlePaymentMethodAttached($paymentMethod);
		echo $paymentMethod->id;
		break;
		// ... handle other event types
	default:
		echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);

// All charges
// $stripe = new \Stripe\StripeClient(config('cashier.secret'));
// $stripe->charges->all(['payment_intent' => '{{PAYMENT_INTENT_ID}}']);

// Last
// \Stripe\Stripe::setApiKey('sk_test_7mJuPfZsBzc3JkrANrFrcDqC');
// $intent = \Stripe\PaymentIntent::retrieve('{{PAYMENT_INTENT_ID}}');
// $latest_charge = $intent->latest_charge;