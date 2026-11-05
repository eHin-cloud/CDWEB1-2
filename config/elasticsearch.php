<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'http://127.0.0.1:9200'),
    'user' => env('ELASTICSEARCH_USER'),
    'password' => env('ELASTICSEARCH_PASSWORD', env('ELASTICSEARCH_PASS')),
    'cloud_id' => env('ELASTICSEARCH_CLOUD_ID', env('ELASTICSEARCH_API_ID')),
    'api_key' => env('ELASTICSEARCH_API_KEY'),
    'ssl_verification' => env('ELASTICSEARCH_SSL_VERIFICATION', false),
    'queue' => [
        'timeout' => env('SCOUT_QUEUE_TIMEOUT'),
    ],
    'indices' => [
        'mappings' => [
            'default' => [
                'properties' => [
                    'id' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
        ],
        'settings' => [
            'default' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
    ],
];

