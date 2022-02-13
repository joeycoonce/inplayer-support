<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'inplayer' => [
        'env' => env(key: 'INPLAYER_ENV', default: 'production'),
        'url' => config(key: 'services.inplayer.env') == 'production' ? 'https://services.inplayer.com' : 'https://staging-v2.inplayer.com',
        'client_id' => env(key: 'INPLAYER_CLIENT_ID'),
        'client_secret' => env(key: 'INPLAYER_CLIENT_SECRET'),
        'merchant_uuid' => env(key: 'INPLAYER_MERCHANT_UUID'),
        'merchant_password' => env(key: 'INPLAYER_MERCHANT_PASSWORD'),
        // 'access_durations' => [
        //     '6 hours' => 169, 
        //     '12 hours' => 170, 
        //     '18 hours' => 171,  
        //     '24 hours' => 5, 
        //     '36 hours' => 172, 
        //     '48 hours' => 6,
        //     '72 hours' => 173,
        //     '96 hours' => 174, 
        //     '1 week' => 7,
        //     '2 weeks' => 175, 
        //     '3 weeks' => 176, 
        //     '4 weeks' => 177,  
        //     '1 month' => 8, 
        //     '2 months' => 178, 
        //     '3 months' => 11,  
        //     '6 months' => 12,
        //     '1 year' => 9,
        // ],
        // 'default_access_duration' => env(key: 'INPLAYER_DEFAULT_ACCESS_DURATION', default: '24 hours'),
    ],
];