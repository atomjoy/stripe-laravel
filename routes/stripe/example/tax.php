<?php
// https://docs.stripe.com/api/checkout/sessions/create

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/stripe/tax', function (Request $request) {
	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	$list = $stripe->taxRates->all(['limit' => 100]);

	return collect($list->data);
});

Route::get('/stripe/tax/create', function (Request $request) {
	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	$tax = $stripe->taxRates->create([
		'display_name' => 'VAT 7',
		'description' => 'VAT Poland',
		'jurisdiction' => 'PL',
		'percentage' => 7,
		'inclusive' => false,
	]);

	return $tax;
});

Route::get('/stripe/tax/search', function (Request $request) {
	$stripe = new \Stripe\StripeClient(config('cashier.secret'));

	$list = $stripe->taxRates->all(['limit' => 100]);

	$search = 'VAT Poland';
	$percentage = 23;

	if ($percentage > 0) {
		$filtered = collect($list->data)->filter(function ($item) use ($search, $percentage) {
			return (stripos($item->description, $search) !== false && $item->percentage == $percentage);
		});
	} else {
		$filtered = collect($list->data)->filter(function ($item) use ($search) {
			return (stripos($item->description, $search) !== false);
		});
	}

	return collect($filtered);
});
