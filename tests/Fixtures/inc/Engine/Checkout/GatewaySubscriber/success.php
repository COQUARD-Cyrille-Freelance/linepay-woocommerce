<?php

return [
    'testNoParamsShouldDisplayNothing' => [
        'config' => [
            'transaction_id' => 'transaction_id',
            'has_get_params' => false,
            'product' => [
                'name' => 'Wizard Hat',
                'slug' => 'medium-size-wizard-hat-in-new-york',
                'price' => 500.00,
                'description' => '<p>Here it is... A WIZARD HAT!</p><p>Only here and now.</p>',
                'image_id' => 90,
                'category_ids' => [19],
                'formation_type' => 'named_license',
                'id_ammon' => 10
            ],
            'order' => [
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

                ],
                'process_generate' => [
                    'response' => json_encode([
                        'info' => [
                            'payInfo' => [
                                [
                                    'method' => 'TEST',
                                    'amount' => 500.00
                                ]
                            ],
                            'transactionId' => 'transactionId'
                        ]
                    ]),
                    'message' => 'message'
                ]
            ],
        ],
        'expected' => [

        ]
    ],
    'testDoNothingOnNoOrder' => [
        'config' => [
            'transaction_id' => 'transaction_id',
            'has_get_params' => true,
            'product' => [
                'name' => 'Wizard Hat',
                'slug' => 'medium-size-wizard-hat-in-new-york',
                'price' => 500.00,
                'description' => '<p>Here it is... A WIZARD HAT!</p><p>Only here and now.</p>',
                'image_id' => 90,
                'category_ids' => [19],
                'formation_type' => 'named_license',
                'id_ammon' => 10
            ],
            'order' => [
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
            ],
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'payInfo' => [
                            [
                                'method' => 'TEST',
                                'amount' => 500.00
                            ]
                        ],
                        'transactionId' => 'transactionId'
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => []
    ],
    'testDoNothingOnFailureRequest' => [
        'config' => [
            'transaction_id' => 'transaction_id',
            'has_get_params' => true,
            'product' => [
                'name' => 'Wizard Hat',
                'slug' => 'medium-size-wizard-hat-in-new-york',
                'price' => 500.00,
                'description' => '<p>Here it is... A WIZARD HAT!</p><p>Only here and now.</p>',
                'image_id' => 90,
                'category_ids' => [19],
                'formation_type' => 'named_license',
                'id_ammon' => 10
            ],
            'order' => [
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
            ],
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'payInfo' => [
                            [
                                'method' => 'TEST',
                                'amount' => 500.00
                            ]
                        ],
                        'transactionId' => 'transactionId'
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => []
    ],
    'testRedirect' => [
        'config' => [
            'transaction_id' => 'transaction_id',
            'has_get_params' => true,
            'product' => [
                'name' => 'Wizard Hat',
                'slug' => 'medium-size-wizard-hat-in-new-york',
                'price' => 500.00,
                'description' => '<p>Here it is... A WIZARD HAT!</p><p>Only here and now.</p>',
                'image_id' => 90,
                'category_ids' => [19],
                'formation_type' => 'named_license',
                'id_ammon' => 10
            ],
            'order' => [
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
            ],
            'process_generate' => [
                'response' => json_encode([
                    'info' => [
                        'payInfo' => [
                            [
                                'method' => 'TEST',
                                'amount' => 500.00
                            ]
                        ],
                        'transactionId' => 'transactionId'
                    ]
                ]),
                'message' => 'message'
            ]
        ],
        'expected' => []
    ]
];