<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" lang="pl" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title>Payment</title>
</head>

<body>
	<main>

		<form id="payment-form" method="POST" action="/stripe/charge/subscribe">
			<h1>Subscribe</h1>

			{{ csrf_field() }}

			<label for="amount">
				Amount (in cents):
				<input type="text" name="amount" id="amount">
			</label>

			<label for="email">
				Email:
				<input type="text" name="email" id="email" value="">
			</label>

			<button id="submit">Subscribe now</button>
		</form>

		<form id="payment-form" method="POST" action="/stripe/charge">
			<h1>Payment</h1>
			{{ csrf_field() }}

			<label for="amount">
				Amount (in cents):
				<input type="text" name="amount" id="amount">
			</label>

			<label for="email">
				Email:
				<input type="text" name="email" id="email" value="">
			</label>

			<button id="submit">Pay now</button>

			{{--
			<h3>Contact info</h3>
			<div id="link-authentication-element">
				<!-- Elements will create authentication element here -->
			</div>

			<h3>Payment</h3>
			<div id="payment-element">
				<!-- Elements will create form elements here -->
			</div>


			<div id="error-message">
				<!-- Display error message to your customers here -->
			</div> --}}
		</form>

		<div id="messages" role="alert" style="display: none;"></div>
	</main>
</body>

</html>