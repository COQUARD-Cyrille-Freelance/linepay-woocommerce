<?php

return [
    'testShouldMakeRequest' => [
        'config' => [
            'url' => 'url',
            'response' => 'response',
            'code' => 200,
            'message' => 'message',
            'body' => 'body',
            'headers' => ['headers'],
        ],
        'expected' => [
            'url' => 'url',
            'params' => [
                'timeout'     => 45,
                'headers'     => ['headers'],
                'body' => 'body'
            ],
            'code' => 200,
            'message' => 'message',
            'body' => 'body',
            'response' => 'response'
        ]
    ]
];