<?php

use App\Models\Master\FactoryProduct;

$factory->define(FactoryProduct::class, function () {
    return [
        'factory_code' => '0001-ODT',
        'product_code' => 'OLT0005',
        'factory_product_code' => 'ODT-OLT0005'
    ];
});
