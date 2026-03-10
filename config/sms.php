<?php
return [
    'user'       => env('SMSINDIAHUB_USER'),
    'password'   => env('SMSINDIAHUB_PASSWORD'),
    'sender_id'  => env('SMSINDIAHUB_SENDER_ID', 'RESTRO'),
    'base_url'   => env('SMSINDIAHUB_BASE_URL'),
    'route'      => env('SMSINDIAHUB_ROUTE', '4'),
    'pe_id'      => env('SMSINDIAHUB_PE_ID'),
];

