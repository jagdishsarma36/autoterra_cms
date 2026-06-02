<?php

return [

    'key_id' => env('RAZORPAY_KEY_ID', ''),
    'key_secret' => env('RAZORPAY_KEY_SECRET', ''),
    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),

    'api_base' => 'https://api.razorpay.com/v1',

    // GST percentage for India
    'gst_percentage' => 18,

    // Default currency
    'default_currency' => 'INR',

];
