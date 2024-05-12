<?php
// https://docs.stripe.com/api/checkout/sessions/create

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/stripe/subscription/product', function (Request $request) {
	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	// Plan 1
	$o = $stripe->products->create(['name' => 'Basic Dashboard']);

	$p1 = $stripe->prices->create([
		'product' => $o->id,
		'unit_amount' => 1000,
		'currency' => 'usd',
		'recurring' => ['interval' => 'month'],
	]);

	$p2 = $stripe->prices->create([
		'product' => $o->id,
		'unit_amount' => 10000,
		'currency' => 'usd',
		'recurring' => ['interval' => 'year'],
	]);

	// Plan 2
	$o = $stripe->products->create(['name' => 'Vip Dashboard']);

	$p3 = $stripe->prices->create([
		'product' => $o->id,
		'unit_amount' => 1500,
		'currency' => 'usd',
		'recurring' => ['interval' => 'month'],
	]);

	$p4 = $stripe->prices->create([
		'product' => $o->id,
		'unit_amount' => 15000,
		'currency' => 'usd',
		'recurring' => ['interval' => 'year'],
	]);

	$c = $stripe->customers->create([
		'email' => 'atomjoy.official@gmail.com',
		'name' => 'Atomjoy Maxio',
		'shipping' => [
			'name' => 'Atomjoy Maxio',
			'address' => [
				'city' => 'Brothers',
				'country' => 'US',
				'line1' => '27 Fredrick Ave',
				'postal_code' => '97712',
				'state' => 'CA',
			],
		],
		'address' => [
			'city' => 'Brothers',
			'country' => 'US',
			'line1' => '27 Fredrick Ave',
			'postal_code' => '97712',
			'state' => 'CA',
		],
	]);

	$s = $stripe->subscriptions->create([
		'customer' => $c->id,
		'items' => [
			['price' => $p1->id, 'quantity' => 2],
			['price' => $p3->id],
		],
		['payment_settings' => ['payment_method_types' => ['card', 'paypal']]], // 'p24' only with currency pln
		// 'collection_method' => 'send_invoice',
		// 'days_until_due' => 7,
		// 'trial_period_days' => 7,
		'trial_settings' => ['end_behavior' => ['missing_payment_method' => 'cancel']],
		'cancel_at_period_end' => true,
		'payment_behavior' => 'default_incomplete',
		// 'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
		// 'billing_cycle_anchor_config' => ['day_of_month' => 31],
		'billing_cycle_anchor' => time() + (60 * 60 * 8),
		'trial_end' => time() + (60 * 60 * 7),
		// 'expand' => ['latest_invoice.payment_intent'],
		// Update subscription biling
		// 'billing_cycle_anchor' => 'now',
		// 'proration_behavior' => 'create_prorations',
	]);

	return $s;
});
