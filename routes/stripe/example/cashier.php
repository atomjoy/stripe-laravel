<?php

use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

// Checkout cashier example
Route::get('/checkout', function (Request $request) {
	$stripePriceId = 'price_deluxe_album';
	$quantity = 1;

	return $request->user()->checkout([$stripePriceId => $quantity], [
		'success_url' => route('checkout.success'),
		'cancel_url' => route('checkout.cancel'),
	]);
})->name('checkout');

Route::get('/cart/{cart}/checkout', function (Request $request, Cart $cart) {
	$order = Order::create([
		'cart_id' => $cart->id,
		'price_ids' => $cart->price_ids,
		'status' => 'incomplete',
	]);

	return $request->user()->checkout($order->price_ids, [
		'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url' => route('checkout.cancel'),
		'metadata' => [
			'order_id' => $order->id
		],
	]);
})->name('cart.checkout');

Route::get('/checkout/success', function (Request $request) {
	$sessionId = $request->get('session_id');

	if ($sessionId === null) {
		return;
	}

	$session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

	if ($session->payment_status !== 'paid') {
		return;
	}

	$orderId = $session['metadata']['order_id'] ?? null;

	$order = Order::findOrFail($orderId);

	$order->update(['status' => 'completed']);

	return view('checkout-success', ['order' => $order]);
})->name('checkout.success');

Route::view('checkout/cancel', 'checkout.cancel', [])->name('checkout.cancel');
