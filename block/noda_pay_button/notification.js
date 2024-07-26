(function($) {
	$( document ).ready(
		function() {

			let nodaSessionKey = "nodaPaymentSessionData"

			if (localStorage.getItem( nodaSessionKey ) === null) {
				return;
			}

			let msInOneDay = 1000 * 60 * 60 * 24;

			let userId            = noda_notification_php_vars.user_id;
			let paymentExpireInMs = noda_notification_php_vars.payment_expire_in_days * msInOneDay;

			let nodaSessionData = JSON.parse( localStorage.getItem( nodaSessionKey ) );

			if (nodaSessionData.hasOwnProperty( "userId" + userId ) === false) {
				return;
			}

			let userNodaSessionData = nodaSessionData["userId" + userId];

			if (
			userNodaSessionData.hasOwnProperty( "lastPaymentAt" ) === false
			|| userNodaSessionData.lastPaymentAt < Date.now() - paymentExpireInMs
			|| userNodaSessionData.hasOwnProperty( "value" ) === false
			) {
				return;
			}

			let fiveMinIntervalMs = 5 * 60 * 1000;

			// do not send requests more frequently then once in 5 min
			if (userNodaSessionData.hasOwnProperty( 'checkPaymentStatusAt' ) && userNodaSessionData.checkPaymentStatusAt > Date.now() - fiveMinIntervalMs) {
				console.log( "Left seconds till next Noda payment status check: " + Math.round( (fiveMinIntervalMs - Date.now() + userNodaSessionData.checkPaymentStatusAt) / 1000 ) )
				return;
			}

			addNotificationModal()

			let nodaSessionValue = userNodaSessionData.value;

			userNodaSessionData["checkPaymentStatusAt"] = Date.now();
			localStorage.setItem( "nodaPaymentSessionData", JSON.stringify( nodaSessionData ) );

			$.ajax(
				{
					type: 'GET',
					url: "/wp-json/noda-button/payment-notification",
					headers: {
						"Content-Type": "application/json",
						"X-WP-Nonce": noda_notification_php_vars.nonce
					},
					data: {
						user_id: userId,
						noda_session_id: nodaSessionValue
					},
					success: function (result) {
						if (result.hasOwnProperty( "payment" ) === false) {
							// no payment to notify

							return
						}

						let payment = result.payment

						if (
						! payment.hasOwnProperty( "payment_status" )
						|| ! payment.hasOwnProperty( "payment_id" )
						|| ! payment.hasOwnProperty( "description" )
						|| ! payment.hasOwnProperty( "id" )
						) {

							// missing data in response
							return;
						}

						$( '#noda-modal-close' ).attr( 'data-payment-id', payment.id )

						let notificationMessage = '';
						if (result.payment.payment_status === "2") {
							notificationMessage = "Your payment with id: <b>" + payment.payment_id + "</b> has failed"
							$( '#noda-modal-content-text' ).css( 'color', 'orange' );
							// alert("Your payment " + payment.description + " (payment Id: " + payment.payment_id + ') failed to process')
						} else if (result.payment.payment_status === "1") {
							// alert("Your payment " + payment.description + " (payment Id: " + payment.payment_id + ') was successfully processed')
							notificationMessage = "Your payment with id: " + payment.payment_id + " has successfully settled"
							$( '#noda-modal-content-text' ).css( 'color', 'green' );
						}
						$( '#noda-modal-content-text' ).text( notificationMessage )

						openModal()
					},
					error: function (result) {
						console.log( 'Failed to retrieve Noda payment notifications data' )
					}
				}
			)
		}
	);

	function updatePaymentStatus(orderId)
	{
		$.ajax(
			{
				type: 'PATCH',
				url: "/wp-json/noda-button/payment-notification",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": noda_notification_php_vars.nonce
				},
				data: JSON.stringify( {order_id: orderId} ),
				success: function (result) {
					console.log( 'Payment was marked as notified' )
				},
				error: function (result) {
					console.log( 'Failed to update payment notified status' );
				}
			}
		)
	}

	function openModal()
	{
		$( "#noda-modal-overlay" ).show()
	}

	function closeModal()
	{
		$( "#noda-modal-overlay" ).hide()
	}

	function addNotificationModal()
	{
		let modalHtml = "<div id=\"noda-modal-overlay\" style=\"z-index: 999996;background-color: rgba(0, 0, 0, 0.7);top: 0;right: 0;bottom: 0;left: 0;cursor: default;width:100%;height: 100%;position: fixed; overflow: auto; display: none\">"
		modalHtml    += "<div class=\"noda-modal-window\" style=\"z-index: 999998; margin: auto; height: 125px; auto; width: 662px; top: 10%; left: 0%; right: 0%; padding: 10px; border-width: 0px; border-style: none; border-color: rgb(255, 255, 255); position: fixed; border-radius: 5px; background-color: rgb(255, 255, 255); box-shadow: none; display: block;\">"
		modalHtml    += "<div class=\"noda-modal-content\"><div style=\"padding-bottom: 10px; padding-top: 10px; text-align: center;;\" id='noda-modal-content-text'></div>"
		modalHtml    += "<div id=\"noda-modal-close\" class=\"button noda-modal-close-button\" style=\"display: block; margin: auto; width: fit-content; padding: 6px; min-width: 70px; border-radius: 4px; background-color: #343a40; color: #ffffff; cursor: pointer; text-align: center\">OK</div></div></div></div>"

		$( 'body' ).append( modalHtml );

		$( "#noda-modal-close" ).on(
			'click',
			function(event) {

				let paymentId = event.target.getAttribute( 'data-payment-id' )

				if (paymentId !== null) {
					updatePaymentStatus( paymentId )
				}

				closeModal()
			}
		);
	}
})( jQuery );
