(function($) {
	$( document ).ready(
		function() {

			if ($( 'button.noda-pay-button' )[0]) {
				if (nodapay_button_block_onclick_php_vars.user_id === '0' && nodapay_button_block_onclick_php_vars.disable_for_anonymous === '1') {
					$( 'button.noda-pay-button' ).remove()
				}

				let logoSettings = localStorage.getItem( 'noda_pay_button_logo' );
				if (logoSettings === null) {
					handleButtonLogo(
						function (data) {
							let buttonLogo = null;
							if (data.hasOwnProperty( 'url' )) {
								buttonLogo = data.url;

								$( 'button.noda-pay-button' ).css( "background-image", "url(" + buttonLogo + ")" )
							}
						}
					);

					function handleButtonLogo(cb) {
						$.ajax(
							{
								type: 'POST',
								url: "/wp-json/noda-button/logo",
								headers: {
									"Content-Type": "application/json",
									"X-WP-Nonce": nodapay_button_block_onclick_php_vars.nonce
								},
								data: JSON.stringify( {nonce: $( 'button.noda-pay-button' ).data( 'nonce' )} ),
								success: function (result) {
									cb( result );
								},
								error: function (result) {
									alert( 'Failed to fetch button logo' );
								}
							}
						)
					}
				}

				$( 'button.noda-pay-button' ).on( 'click', clickHandler );

				// Define the clickHandler function, as before
				function clickHandler(event) {
					let bodyParams = {
						redirectUrl: window.location.href,
						userId: nodapay_button_block_onclick_php_vars.user_id,
						amount: event.target.getAttribute( 'data-amount' ),
						description: event.target.getAttribute( 'data-description' ),
					}

					if (
					localStorage.getItem( "nodaPaymentSessionData" ) !== null
					) {
						let nodaPaymentSessionData = JSON.parse( localStorage.getItem( "nodaPaymentSessionData" ) )
						if (nodaPaymentSessionData.hasOwnProperty( "userId" + nodapay_button_block_onclick_php_vars.user_id )) {
							bodyParams["session_id"] = nodaPaymentSessionData["userId" + nodapay_button_block_onclick_php_vars.user_id].value
						}
					}

					$.ajax(
						{
							type: 'POST',
							url: "/wp-json/noda-button/pay-url",
							headers: {
								"Content-Type": "application/json",
								"X-WP-Nonce": nodapay_button_block_onclick_php_vars.nonce
							},
							data: JSON.stringify( bodyParams ),
							success: function (result) {
								if (result.hasOwnProperty( "session_id" )) {
									let nodaPaymentSessionData = null;

									if (localStorage.getItem( "nodaPaymentSessionData" ) === null) {
										nodaPaymentSessionData = {};
										nodaPaymentSessionData["userId" + nodapay_button_block_onclick_php_vars.user_id] = {
											value: result.session_id,
											lastPaymentAt: Date.now(),
											userId: nodapay_button_block_onclick_php_vars.user_id
										};

									} else {
										nodaPaymentSessionData = JSON.parse( localStorage.getItem( "nodaPaymentSessionData" ) );

										nodaPaymentSessionData["userId" + nodapay_button_block_onclick_php_vars.user_id] = {
											value: result.session_id,
											lastPaymentAt: Date.now(),
											userId: nodapay_button_block_onclick_php_vars.user_id
										};
									}

									if (nodaPaymentSessionData) {
										localStorage.setItem( "nodaPaymentSessionData", JSON.stringify( nodaPaymentSessionData ) );
									}
								}

								if (result.hasOwnProperty( "url" )) {
									window.location.href = result.url;
								}
							},
							error: function (result) {
								alert( 'Failed to process payment. Please try again later' );
							}
						}
					)
				}
			}
		}
	);

	$( 'button.noda-pay-button' ).hover(
		function(){
			$( this ).css( { borderTopWidth: '0', borderLeftWidth: '6px', borderRightWidth: '0', borderBottomWidth: '6px', borderBottomLeftRadius: '12px'} );
		},
		function(){
			$( this ).css( { borderWidth: '6px', 'borderRadius': '12px' } );
		}
	);

})( jQuery );
