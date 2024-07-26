wp.blocks.registerBlockType('noda/test-block', {
    title: 'Noda pay button',
    icon: 'smiley',
    category: 'widgets',
    attributes: {
        content: {type: 'string'},
        colorBackground: {type: 'string'},
        colorBorder: {type: 'string'},
        imageUrl: {type: 'string'},
        buttonWidthPercent: {type: 'integer', default: 50},
        amount: {type: 'float'},
		description: {type: 'string', default: 'Pay for services'}
    },

    edit: function(props) {
        function updateColorBackground(value) {
            props.setAttributes({colorBackground: value !== '' ? value.hex : value})
        }

        function updateColorBorder(value) {

            props.setAttributes({colorBorder: value !== '' ? value.hex : value})
        }

        function updateButtonWidthPercent(event) {

            let value = event.target.value

            if (value < 0) {
                value = 0;
            }

            if (value > 100) {
                value = 100;
            }

            props.setAttributes({buttonWidthPercent: value})
        }

        function updateAmount(event)
        {
            let value = event.target.value

            if (value < 0.01) {
                value = 0.01;
            }

            if (value === undefined) {
            	value = 0.01
			}

            props.setAttributes({amount: value})
        }

        function updateDescription(event)
		{
			props.setAttributes({description: event.target.value})
		}

        return React.createElement(
            "div",
            {
                style: {
                }
            },
            React.createElement(
                "div",
                null,
                React.createElement(
                    "div",
                    {
                        style: {
                            float: "left",
                            width: "100%",
                            minWidth: "200px",
                            marginTop: "5px",
                        }
                    },
                    React.createElement(
                        "label",
                        {
                            style: {
                                fontWeight: 600,
                                width: "35%"
                            }
                        },
                        "Price (in currency, specified in noda settings):"
                    ),
                    React.createElement(
                        "input",
                        {
                            type: "number",
                            step: "0.01",
                            min: "0.01",
                            required: "required",
                            value: props.attributes.amount,
                            style: {
                                minWidth: "100px",
                                width:"30%"
                            },
                            onChange: updateAmount
                        },
                    )
                )
            ),
			React.createElement(
				"div",
				{
					style: {
						float: "left",
						width: "100%",
						minWidth: "200px",
						marginTop: "5px",
					}
				},
				React.createElement(
					"label",
					{
						style: {
							fontWeight: 600,
							width: "35%"
						}
					},
					"Description for payment (destination of payment):"
				),
				React.createElement(
					"input",
					{
						type: "text",
						required: "required",
						value: props.attributes.description,
						style: {
							minWidth: "100px",
							width:"30%"
						},
						onChange: updateDescription
					},
				)
			),
            React.createElement(
                "div",
                {
                    style: {
                        marginTop: "5px"
                    }
                },
                React.createElement(
                    "div",
                    {
                        style: {
                            float: "left",
                            width: "35%",
                            minWidth: "217px"
                        }
                    },
                    React.createElement(
                        "label",
                        {
                            style: {
                                minWidth: "217px",
                                fontWeight: 600
                            }
                        },
                        "Background color:"
                    ),
                    React.createElement(wp.components.ColorPicker, {
                        color: props.attributes.colorBackground, onChangeComplete: updateColorBackground
                    }),
                ),

                React.createElement(
                    "div",
                    {
                        style: {
                            float: "left",
                            width: "28%",
                            minWidth: "217px"
                        }
                    },
                    React.createElement(
                        "label",
                        {
                            style: {
                                minWidth: "217px",
                                fontWeight: 600
                            }
                        },
                        "Border color:"
                    ),
                    React.createElement(
                        wp.components.ColorPicker,
                        { color: props.attributes.colorBorder, onChangeComplete: updateColorBorder })
                ),
                React.createElement("div", {style: {clear: "both"}})
            )
        );
    },
    save: function(props) {

		return React.createElement(
			"div",
			{
				className: "noda-pay-button-container",
				style: {
					textAlign: "center"
				}
			},
			React.createElement(
				"button",
				{
					style: {
						border: "6px solid " + props.attributes.colorBorder,
						background: "no-repeat center center " + props.attributes.colorBackground,
						backgroundPosition: "center",
						backgroundRepeat: "no-repeat",
						width: props.attributes.buttonWidthPercent + "%",
						minWidth: "260px",
						borderRadius: "12px",
						padding: "0.6180469716em 1.41575em",
						textDecoration: "none",
						color: "transparent !important",
						minHeight: "76px",
						cursor: "pointer"
					},
					className: "noda-pay-button",
					'data-amount': props.attributes.amount !== undefined ? props.attributes.amount : 0.01,
					'data-description': props.attributes.description,
				},
				null
			)
		);
    }
})
