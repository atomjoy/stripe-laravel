<?php
https: //docs.stripe.com/api/events/types

echo "Success";


// Handle the checkout.session.completed event
// if ($event->type == 'checkout.session.completed') {
// 	// Retrieve the session. If you require line items in the response, you may include them by expanding line_items.
// 	$session = \Stripe\Checkout\Session::retrieve([
// 	  'id' => $event->data->object->id,
// 	  'expand' => ['line_items'],
// 	]);
// 	$line_items = $session->line_items;
// 	// Do something
// }