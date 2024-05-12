<?php

require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(config('cashier.secret'));

$endpoint = \Stripe\WebhookEndpoint::create([
	'url' => 'https://example.com/my/webhook/endpoint',
	'enabled_events' => [
		'payment_intent.payment_failed',
		'payment_intent.succeeded',
	],
]);

// $stripe = new \Stripe\StripeClient(config('cashier.secret'));