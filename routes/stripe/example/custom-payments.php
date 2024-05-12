<?php
// https://docs.stripe.com/api/checkout/sessions/create

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/stripe/custom-payment', function (Request $request) {
	return view('stripe.custom-payment');
});

Route::post('/stripe/custom-payment/create', function (Request $request) {
	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	function calculateOrderAmount(array $items): int
	{
		// Replace this constant with a calculation of the order's amount
		// Calculate the order total on the server to prevent
		// people from directly manipulating the amount on the client
		return 1001;
	}

	try {
		// retrieve JSON from POST body
		$jsonStr = file_get_contents('php://input');
		$jsonObj = json_decode($jsonStr);

		// Create a PaymentIntent with amount and currency
		$paymentIntent = $stripe->paymentIntents->create([
			// Total cost
			'amount' => calculateOrderAmount($jsonObj->items),
			// Currency
			'currency' => 'pln',
			// In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
			'automatic_payment_methods' => ['enabled' => true],
			// Enable payment confirmation after payment (manualy or with api)
			// 'capture_method' => 'manual',
			// Enable payment confirmation after payment only card, paypal (manualy or with api)
			// 'payment_method_options' => ['card' => ['capture_method' => 'manual'], 'paypal' => ['capture_method' => 'manual']],
			// Default pament methods (for errors)
			// 'payment_method_types' => ['card'],
		]);

		// {"clientSecret":"pi_3PF0qPRwgqLqoMqm0gfyzw4X_secret_iZDAFHiLUwdNckY9U8BUYlPkB"}
		return response()->json(['clientSecret' => $paymentIntent->client_secret], 200);
	} catch (Error $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}
});

Route::get('/stripe/custom-payment/retrive/{id}', function (Request $request, $id) {
	try {
		$stripe = new \Stripe\StripeClient(config('cashier.secret'));

		// Get payment
		$o = $stripe->paymentIntents->retrive($id, []); // 'pi_32AkjQ5H4Bas2eAolX13'
		return $o->id;
	} catch (Error $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}
});

Route::get('/stripe/custom-payment/cancel/{id}', function (Request $request, $id) {
	try {
		$stripe = new \Stripe\StripeClient(config('cashier.secret'));

		// Confirm payment
		$o = $stripe->paymentIntents->cancel($id, []); // 'pi_32AkjQ5H4Bas2eAolX13'
		return $o->id;
	} catch (Error $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}
});

Route::get('/stripe/custom-payment/capture/{id}', function (Request $request, $id) {
	try {
		$stripe = new \Stripe\StripeClient(config('cashier.secret'));

		// Full
		$o = $stripe->paymentIntents->capture($id, []); // 'pi_32AkjQ5H4Bas2eAolX13'
		return $o->id;

		// Part
		$o = $stripe->paymentIntents->capture($id, [], ['amount_to_capture' => 1001]);
		return $o->id;

		// Part
		// \Stripe\Stripe::setApiKey(config('cashier.secret'));
		// $intent = \Stripe\PaymentIntent::retrieve($id);
		// return $intent->capture(['amount_to_capture' => 1001]);
	} catch (Error $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}
});
