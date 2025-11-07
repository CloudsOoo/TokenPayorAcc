<?php
declare (strict_types=1);

return [
    'version' => '1.0.1',
    'name' => 'TokenPay',
    'author' => 'Conan',
    'website' => '#',
    'description' => 'TokenPay加密货币支付网关',
    'options' => [
        'tokenpay' => '默认接口',
    ],
    'callback' => [
        \App\Consts\Pay::IS_SIGN => true,
        \App\Consts\Pay::IS_STATUS => true,
        \App\Consts\Pay::FIELD_STATUS_KEY => 'Status', 
        \App\Consts\Pay::FIELD_STATUS_VALUE => 1, 
        \App\Consts\Pay::FIELD_ORDER_KEY => 'OutOrderId', 
        \App\Consts\Pay::FIELD_AMOUNT_KEY => 'ActualAmount', 
        \App\Consts\Pay::FIELD_RESPONSE => 'ok'
    ]
];
