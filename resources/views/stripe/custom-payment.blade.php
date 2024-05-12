<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8" />
	<title>Accept a payment</title>
	<meta name="description" content="A demo of a payment on Stripe" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="https://js.stripe.com/v3/"></script>

	<script>
		let email = ''
		let elements = null;

		const appearance = {
			theme: 'stripe', // flat, minimal
			// variables: { colorPrimaryText: '#fff', colorPrimary: '#0099ff' }
		}

		const options = {
			locale: 'pl'
		}

		const stripe = Stripe("{{ config('cashier.key') }}", options);

		// Fetches a payment intent and captures the client secret
		async function initialize() {
			// Get csrf token
			const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;

			// Cart products
			const items = [{ id: "m-tshirt" }, { id: "s-tshirt" }];

			// Send products list to server and create stripe payment and get clientSecret
			const { clientSecret } = await fetch("/stripe/custom-payment/create", {
				body: JSON.stringify({ items }),
				method: "POST", headers: {
					"Content-Type": "application/json",
					"X-CSRF-Token": csrfToken
				},
				credentials: "same-origin",
			}).then((r) => r.json());

			// Enable the skeleton loader UI for the optimal loading experience.
			const loader = 'auto';
			elements = stripe.elements({ clientSecret, appearance, loader }, options);

			// const paymentElementOptions = { layout: "accordion" };
			const paymentElementOptions = { layout: "tabs" };
			const paymentElement = elements.create("payment", paymentElementOptions);
			paymentElement.mount("#payment-element");

			// Create an instance of the Link Authentication Element.
			const linkAuthenticationElement = elements.create("linkAuthentication");
			linkAuthenticationElement.mount("#link-authentication-element");

			// const options = { mode: 'billing' };
			const shippingOptions = { mode: 'shipping' };
			const addressElement = elements.create('address', shippingOptions);
			addressElement.mount('#address-element');

			// Update email
			linkAuthenticationElement.on('change', (event) => {
  				email = event.value.email;
			});

			// Updare locale
			// elements.update({locale: options.locale});
		}

		async function handleSubmit(e) {
			e.preventDefault();
			setLoading(true);

			const { error } = await stripe.confirmPayment({
				elements,
				confirmParams: {
					// Make sure to change this to your payment completion page
					// return_url: "http://localhost:4242/stripe/custom-payment",
					// return_url: window.location.origin+"/stripe/custom-payment",
					return_url: "https://"+window.location.host+"/stripe/custom-payment",
					receipt_email: email,
					// receipt_email: document.getElementById("Field-emailInput").value,
				},
			});

			// This point will only be reached if there is an immediate error when
			// confirming the payment. Otherwise, your customer will be redirected to
			// your `return_url`. For some payment methods like iDEAL, your customer will
			// be redirected to an intermediate site first to authorize the payment, then
			// redirected to the `return_url`.
			if (error.type === "card_error" || error.type === "validation_error") {
				showMessage(error.message);
			} else {
				showMessage("An unexpected error occurred.");
			}

			setLoading(false);
		}

		// Fetches the payment intent status after payment submission
		async function checkStatus() {
			const clientSecret = new URLSearchParams(window.location.search).get("payment_intent_client_secret");

			if (!clientSecret) {
				return;
			}

			const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

			switch (paymentIntent.status) {
				case "succeeded":
					showMessage("Payment succeeded!");
					break;
				case "requires_capture":
					showMessage("Payment succeeded! Waiting for confirmation!");
					break;
				case "processing":
					showMessage("Your payment is processing.");
					break;
				case "requires_payment_method":
					showMessage("Your payment was not successful, please try again.");
					break;
				default:
					showMessage("Something went wrong. " + paymentIntent.status);
					break;
			}
		}

		// ------- UI helpers -------

		function showMessage(messageText) {
			const messageContainer = document.querySelector("#payment-message");

			messageContainer.classList.remove("hidden");
			messageContainer.textContent = messageText;

			setTimeout(function () {
				messageContainer.classList.add("hidden");
				messageContainer.textContent = "";
			}, 4000);
		}

		// Show a spinner on payment submission
		function setLoading(isLoading) {
		if (isLoading) {
			// Disable the button and show a spinner
			document.querySelector("#submit").disabled = true;
			document.querySelector("#spinner").classList.remove("hidden");
			document.querySelector("#button-text").classList.add("hidden");
		} else {
			document.querySelector("#submit").disabled = false;
			document.querySelector("#spinner").classList.add("hidden");
			document.querySelector("#button-text").classList.remove("hidden");
		}
		}

		window.onload = () => {
			console.log('Onload', window.location.host)

			initialize();
			checkStatus();

			document.querySelector("#payment-form").addEventListener("submit", handleSubmit);
		}

		document.addEventListener('DOMContentLoaded', async () => {
			let searchParams = new URLSearchParams(window.location.search);

			if (searchParams.has('payment_intent')) {
				const payment_intent = searchParams.get('payment_intent');
				document.getElementById('session-id').textContent = payment_intent
			}

			if (searchParams.has('session_id')) {
				const session_id = searchParams.get('session_id');
				document.getElementById('session-id').setAttribute('value', session_id);
			}
		});
	</script>

	<style>
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

		/* Variables */
		* {
			box-sizing: border-box;
			font-family: Poppins;
		}

		body {
			padding: 50px;
			margin: 0px;
			font-family: -apple-system, BlinkMacSystemFont, sans-serif;
			font-size: 16px;
			min-height: 100vh;
		}

		.form,
		.cart-checkout {
			margin: 50px auto;
			width: 90%;
			max-width: 1024px;
			box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.1), 0px 2px 5px 0px rgba(50, 50, 93, 0.1), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.07);
			border-radius: 7px;
			padding: 20px;
		}

		.hidden {
			display: none;
		}

		#payment-message {
			color: rgb(105, 115, 134);
			color: #0570de;
			font-size: 16px;
			font-weight: 900;
			line-height: 20px;
			padding-top: 12px;
			text-align: center;
		}

		#payment-element {
			margin-bottom: 24px;
		}

		/* Buttons and links */
		button {
			background: #0570de;
			font-family: Arial, sans-serif;
			color: #ffffff;
			border-radius: 4px;
			border: 0;
			padding: 12px 16px;
			font-size: 16px;
			font-weight: 600;
			cursor: pointer;
			display: block;
			transition: all 0.2s ease;
			box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
			width: 100%;
		}

		button:hover {
			filter: contrast(115%);
		}

		button:disabled {
			opacity: 0.5;
			cursor: default;
		}

		/* spinner/processing state, errors */
		.spinner,
		.spinner:before,
		.spinner:after {
			border-radius: 50%;
		}

		.spinner {
			color: #ffffff;
			font-size: 22px;
			text-indent: -99999px;
			margin: 0px auto;
			position: relative;
			width: 20px;
			height: 20px;
			box-shadow: inset 0 0 0 2px;
			-webkit-transform: translateZ(0);
			-ms-transform: translateZ(0);
			transform: translateZ(0);
		}

		.spinner:before,
		.spinner:after {
			position: absolute;
			content: "";
		}

		.spinner:before {
			width: 10.4px;
			height: 20.4px;
			background: #0570de;
			border-radius: 20.4px 0 0 20.4px;
			top: -0.2px;
			left: -0.2px;
			-webkit-transform-origin: 10.4px 10.2px;
			transform-origin: 10.4px 10.2px;
			-webkit-animation: loading 2s infinite ease 1.5s;
			animation: loading 2s infinite ease 1.5s;
		}

		.spinner:after {
			width: 10.4px;
			height: 10.2px;
			background: #0570de;
			border-radius: 0 10.2px 10.2px 0;
			top: -0.1px;
			left: 10.2px;
			-webkit-transform-origin: 0px 10.2px;
			transform-origin: 0px 10.2px;
			-webkit-animation: loading 2s infinite ease;
			animation: loading 2s infinite ease;
		}

		@-webkit-keyframes loading {
			0% {
				-webkit-transform: rotate(0deg);
				transform: rotate(0deg);
			}

			100% {
				-webkit-transform: rotate(360deg);
				transform: rotate(360deg);
			}
		}

		@keyframes loading {
			0% {
				-webkit-transform: rotate(0deg);
				transform: rotate(0deg);
			}

			100% {
				-webkit-transform: rotate(360deg);
				transform: rotate(360deg);
			}
		}

		@media only screen and (max-width: 600px) {
			form {
				width: 80vw;
				min-width: initial;
			}
		}

		#payment-message {
			float: left;
			width: 100%;
			padding: 10px;
			margin-bottom: 20px;
		}

		#session-id {
			float: left;
			width: 100%;
			padding: 10px;
		}

		.address-accordion,
		.email-accordion {
			float: left;
			width: 100%;
			padding: 15px;
			margin: 10px 0px;
			border: 1px solid rgb(230, 230, 230);
			border-radius: 10px;
			min-height: 50px;
			transition: all .3s;
		}

		.payment-title,
		.cart-checkout-title {
			font-weight: 700;
			font-size: 25px;
			margin-bottom: 20px;
		}

		.payment-lanel {
			float: left;
			width: 100%;
			padding-left: 10px;
			margin-bottom: 10px;
			font-weight: 700;
			font-size: 14px;
			color: #0570de;
			border-left: 2px solid #0570de;
		}
	</style>
</head>

<body>
	<!-- Dispaly cart checkout -->
	<div class="cart-checkout">
		<div class="cart-checkout-title">Koszyk</div>
		<p>Lista produktów ...</p>
	</div>
	<!-- Display a payment form -->
	<form id="payment-form" class="form">
		<div class="payment-title">Płatność Stripe</div>

		<div id="payment-message" class="hidden"></div>

		<div class="email-accordion">
			<label class="payment-lanel">Adres email</label>
			<div id="link-authentication-element">
				<!-- Elements will create authentication element here -->
			</div>
		</div>
		<div class="address-accordion">
			<label class="payment-lanel">Adres wysyłki</label>
			<div id="address-element">
				<!--Stripe.js injects the Address Element-->
			</div>
		</div>

		<div class="address-accordion">
			<label class="payment-lanel">Karta</label>
			<div id="payment-element">
				<!--Stripe.js injects the Payment Element-->
			</div>
		</div>

		<button id="submit">
			<div class="spinner hidden" id="spinner"></div>
			<span id="button-text">Dokonaj Płatności</span>
		</button>

		{{-- <div id="session-id"></div> --}}
	</form>
</body>

</html>