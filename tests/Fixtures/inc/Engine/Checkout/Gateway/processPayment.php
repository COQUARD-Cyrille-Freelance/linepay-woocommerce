<?php

$product = [
	'name' => 'Wizard Hat',
	'slug' => 'medium-size-wizard-hat-in-new-york',
	'price' => 500.00,
	'description' => '<p>Here it is... A WIZARD HAT!</p><p>Only here and now.</p>',
	'image_id' => 90,
	'category_ids' => [19],
	'formation_type' => 'named_license',
	'id_ammon' => 10
];

$order = [
	'address' => [
		'first_name' => '111Joe',
		'last_name'  => 'Conlin',
		'company'    => 'Speed Society',
		'email'      => 'joe@testing.com',
		'phone'      => '760-555-1212',
		'address_1'  => '123 Main st.',
		'address_2'  => '104',
		'city'       => 'San Diego',
		'state'      => 'Ca',
		'postcode'   => '92121',
		'country'    => 'US'
	],
	'participant_member_ordinal' => 1010,
	'participant_civilite' => 'M',
	'participant_member_first_name' => 'test',
	'participant_member_name' => 'trainee',
	'participant_member_email' => 'trainee@gmail.com',
	'siret' => 1512512,
	'get_params' => [

	]
];

return [
    'testShouldSucceed' => [
        'config' => [
            'currency' => 'THB',
            'settings' => [
				'enabled' => 'yes',
                'testmode' => false,
                'channel_id' => '1234567890123456789012345678901234567890',
                'channel_secret' => '1234567',
                'title' => 'title',
                'description' => 'description',
            ],
            'is_enabled' => true,
            'gateway_id' => 'linepay',
            'product' => $product,
            'order' => $order,
            'order_exists' => true,
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'transactionId' => 10,
                        'paymentUrl' => [
                            'web' => 'payment_url'
                        ]
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => [
	        'is_succeed' => true,
        ]
    ],
    'testShouldDoNothingOnDisabled' => [
        'config' => [
            'currency' => 'THB',
            'settings' => [
		        'enabled' => 'yes',
		        'testmode' => false,
		        'channel_id' => '',
		        'channel_secret' => '1234567',
		        'title' => 'title',
		        'description' => 'description',
	        ],
	        'is_enabled' => true,
	        'gateway_id' => 'linepay',
            'product' => $product,
            'order' => $order,
	        'order_exists' => true,
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'transactionId' => 10,
                        'paymentUrl' => [
                            'web' => 'payment_url'
                        ]
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => [
			'is_succeed' => false,
        ]
    ],
    'testShouldDoNothingOnNotConfigured' => [
        'config' => [
            'currency' => 'THB',
            'settings' => [
                'enabled' => 'yes',
                'testmode' => false,
                'channel_id' => '',
                'channel_secret' => '1234567',
                'title' => 'title',
                'description' => 'description',
            ],
            'is_enabled' => true,
            'gateway_id' => 'linepay',
            'product' => $product,
            'order' => $order,
            'order_exists' => true,
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'transactionId' => 10,
                        'paymentUrl' => [
                            'web' => 'payment_url'
                        ]
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => [
            'is_succeed' => false,
        ]
    ],
    'testShouldDoNothingOnWrongCurrency' => [
        'config' => [
            'currency' => 'EUR',
            'settings' => [
                'enabled' => 'yes',
                'testmode' => false,
                'channel_id' => '1234567890123456789012345678901234567890',
                'channel_secret' => '1234567',
                'title' => 'title',
                'description' => 'description',
            ],
            'is_enabled' => true,
            'gateway_id' => 'linepay',
            'product' => $product,
            'order' => $order,
            'order_exists' => true,
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'transactionId' => 10,
                        'paymentUrl' => [
                            'web' => 'payment_url'
                        ]
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => [
            'is_succeed' => false,
        ]
    ],
    'testShouldDoNothingOnFailedRequest' => [
        'config' => [
            'currency' => 'THB',
            'settings' => [
                'enabled' => 'yes',
                'testmode' => false,
                'channel_id' => '1234567890123456789012345678901234567890',
                'channel_secret' => '1234567',
                'title' => 'title',
                'description' => 'description',
            ],
            'is_enabled' => true,
            'gateway_id' => 'linepay',
            'product' => $product,
            'order' => $order,
            'order_exists' => true,
            'process_generate' => [
                'is_wp_error' => true,
                'response' => json_encode([
                    'info' => [
                        'transactionId' => 10,
                        'paymentUrl' => [
                            'web' => 'payment_url'
                        ]
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => [
            'is_succeed' => false,
        ]
    ],
];