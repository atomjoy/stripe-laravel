<?php
// https://docs.stripe.com/api/checkout/sessions/create

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

// Checkout
Route::get('/stripe/charge', function () {
	return view('stripe.charge');
});

Route::post('/stripe/charge', function (Request $request) {
	$order_id = uniqid();
	$email = $request->input('email');

	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	// $tax = $stripe->taxRates->create([
	//     'display_name' => 'VAT',
	//     'description' => 'VAT Poland',
	//     'jurisdiction' => 'PL',
	//     'percentage' => 23,
	//     'inclusive' => true,
	// ]);

	// Calculate vat
	$vat_amount = number_format((float) $request->input('amount') * 0.23, 2, '.', '');
	$host = $request->getSchemeAndHttpHost();

	// Session
	$session = $stripe->checkout->sessions->create([
		// Enable tax calculation (paid options)
		// 'automatic_tax' => ['enabled' => true],
		// Enable payment confirmation after payment only card, paypal (manualy or with api)
		'payment_intent_data' => [
			// 'capture_method' => 'manual', // Capture payment in panel or from api
			'description' => 'Checkout payment', // Payment panel description
			// 'shipping' => [],
		],
		// 'payment_method_options' => ['card' => ['request_three_d_secure' => 'automatic'], 'paypal' => ['capture_method' => 'manual']],
		// Client order id
		'client_reference_id' => $order_id, // Will be aded in checkout.session.completed event
		'customer_email' => $email,
		'success_url' => $host . '/stripe/charge/success?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url' => $host . '/stripe/charge/cancel',
		'mode' => 'payment', // Max 100 items
		'submit_type' => 'donate',
		'line_items' => [
			[
				'quantity' => $request->input('quantity', 1),
				'price_data' => [
					'unit_amount' => $request->input('amount') * 100,
					// 'unit_amount_decimal' => $request->input('amount'),
					'currency' => config('cashier.currency'),
					'product_data' => [
						'name' => 'Send money!',
						'description' => 'Money donation.',
						'tax_code' => 'txcd_10103100', // SaaS electronic download
						'metadata' => [
							'tax_name' => 'VAT',
							'tax_percentage' => 23,
							'tax_amount' => $vat_amount,
						]
					],
					// 'product' => 'pr_23742...', // or product id
					'tax_behavior' => 'inclusive',
					// 'tax_behavior' => 'exclusive',
				],
				// 'price' => 'price_1MotwRLkdIwHu7ixYcPLm5uZ',
				// 'adjustable_quantity' => [ 'enabled' => true, 'maximum' => 10, 'minimum' => 0],
				// 'tax_rates' => [ $tax->id ], // Calculate Tax Payd option !!!
			],
			[
				'quantity' => 2,
				'price_data' => [
					'unit_amount' => 12.99 * 100,
					'currency' => config('cashier.currency'),
					'product_data' => [
						'name' => 'Send tip!',
						'description' => 'Payment tip.',
					],
					'tax_behavior' => 'inclusive',
				]
			],
		],
		'customer_email' => $request->input('email'),
		'metadata' => [
			'order_id' => $order_id,
			'email' => $email,
			'addons' => json_encode([
				'product_0' => [
					['name' => 'Ananas', 'price' => '1.00', 'size' => 'S'],
					['name' => 'Szynka', 'price' => '1.50', 'size' => 'S'],
				],
			]),
		],
		'custom_text' => [
			'submit' => [
				'message' => 'The amount includes VAT ' . $vat_amount . strtoupper(config('cashier.currency')),
			],
			'after_submit' => [
				'message' => 'Have a nice day!',
			]
		],
		// 'custom_fields' => [
		// 	[
		// 		'optional' => true,
		// 		'type' => 'text',
		// 		'key' => 'Company',
		// 		'label' => ['type' => 'custom', 'custom' => 'Company name'],
		// 	],
		// 	[
		// 		'optional' => true,
		// 		'type' => 'dropdown',
		// 		'key' => 'size',
		// 		'label' => ['type' => 'custom', 'custom' => 'Size'],
		// 		'dropdown' => [
		// 			'options' => [
		// 				['label' => 'Small', 'value' => 'S'],
		// 				['label' => 'Medium', 'value' => 'M'],
		// 				['label' => 'Big', 'value' => 'XL'],
		// 			]
		// 		]
		// 	]
		// ]
	]);

	return redirect()->away($session->url);
});

Route::post('/stripe/charge/subscribe', function (Request $request) {

	$order_id = uniqid();
	$email = $request->input('email');

	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	$tax = $stripe->taxRates->create([
		'display_name' => 'VAT',
		'description' => 'VAT Poland',
		'jurisdiction' => 'PL',
		'percentage' => 23,
		'inclusive' => true,
	]);

	// Calculate vat
	$vat_amount = number_format((float) $request->input('amount') * 0.23, 2, '.', '');
	$host = $request->getSchemeAndHttpHost();

	// Checkout does not support multiple prices with different billing intervals.
	$session = $stripe->checkout->sessions->create([
		'success_url' => $host . '/stripe/charge/success?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url' => $host . '/stripe/charge/cancel',
		'mode' => 'subscription', // Max 20 items
		// 'payment_method_types' => ['card'],
		'line_items' => [
			[
				'price' => 'price_1PFEIKRwgqLqoMqm7J55tfNy',
				'quantity' => $request->input('quantity', 1),
			],
			// [
			// 	'price_data' => [
			// 		'unit_amount' => $request->input('amount') * 100,
			// 		'currency' => config('cashier.currency'),
			// 		'product_data' => [
			// 			'name' => 'Monthly Subscription!',
			// 			'description' => 'User monthly donation.',
			// 			'tax_code' => 'txcd_10103100', // SaaS electronic download
			// 		],
			// 		'tax_behavior' => 'inclusive',
			// 		'recurring' => ['interval' => 'month', 'interval_count' => 1], // Billed every month
			// 	],
			// 	'tax_rates' => [$tax->id],
			// 	'quantity' => $request->input('quantity', 1),
			// ],
		],
		'client_reference_id' => $order_id,
		'customer_email' => $email,
		'metadata' => [
			'order_id' => $order_id,
			'email' => $email,
			'addons' => [],
		],
		'custom_text' => [
			'submit' => [
				'message' => 'The amount includes VAT ' . $vat_amount . strtoupper(config('cashier.currency')),
			],
			'after_submit' => [
				'message' => 'Have a nice day!',
			]
		],
		'custom_fields' => [[
			'optional' => true,
			'type' => 'text',
			'key' => 'Company',
			'label' => [
				'type' => 'custom',
				'custom' => 'Company name'
			],
		]]
	]);

	return redirect()->away($session->url);
});

Route::get('/stripe/charge/success', function (Request $request) {
	$sessionId = $request->get('session_id');

	if ($sessionId === null) {
		return 'Error Id';
	}

	$session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

	$orderId = $session['metadata']['order_id'] ?? null;
	$orderEmail = $session['metadata']['email'] ?? null;
	$orderAddons = $session['metadata']['addons'] ?? null;

	$collection = Cashier::stripe()->checkout->sessions->allLineItems($sessionId, []);

	// Session Status (paid, unpaid, no_payment_required)
	// if ($session->payment_status == 'unpaid') {}

	\Stripe\Stripe::setApiKey(config('cashier.secret'));

	$paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

	// Payment Status (requires_payment_method, requires_confirmation, requires_action, processing, requires_capture, canceled, or succeeded)
	if ($paymentIntent->status != 'requires_capture' && $paymentIntent->status != 'succeeded') {
		// Unpaid
		return 'Order has been unpaid (' . $paymentIntent->status . ').';
	} else {

		// Update order status
		// $order = Order::findOrFail($orderId);
		// $order->update(['status' => 'completed']);
		// return view('stripe.checkout-success', ['order' => $order]);

		// Paid
		if ($paymentIntent->status == 'requires_capture') {
			return 'Order has been paid and waiting for shop confirmation.';
		} else {
			return 'Order has been paid.';
		}
	}

	return response()->json([
		'checkout_payment_status' => $paymentIntent->status,
		'session_id' => $sessionId,
		'order_id' => $orderId,
		'email' => $orderEmail,
		'products' => $collection,
		'addons' => @json_decode($orderAddons, true),
	]);
});

Route::get('/stripe/charge/mini', function (Request $request) {
	\Stripe\Stripe::setApiKey(config('cashier.secret'));

	$session = \Stripe\Checkout\Session::create([
		'payment_method_types' => ['card'],

		// Payment donation
		'line_items' => [
			[
				'quantity' => 1,
				'price_data' => [
					'unit_amount' => 12.99 * 100,
					'currency' => config('cashier.currency'),
					'product_data' => [
						'name' => 'Send tip!',
						'description' => 'Money tip.',
					],
					'tax_behavior' => 'inclusive',
				]
			],
		],
		'mode' => 'payment',

		// Subscription
		// 'line_items' => [
		// 	[
		// 		'price' => 'price_1PFEIKRwgqLqoMqm7J55tfNy',
		// 		'quantity' => 1,
		// 	],
		// ],
		// 'mode' => 'subscription',

		// Confirmation links
		'success_url' => 'https://example.com/success?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url' => 'https://example.com/cancel',
	]);

	return redirect()->away($session->url);
});
