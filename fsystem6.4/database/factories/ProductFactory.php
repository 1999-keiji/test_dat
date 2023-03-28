<?php

use App\Models\Master\Product;
use App\ValueObjects\Enum\CreatingType;

$factory->define(Product::class, function () {
    return [
        'product_code' => 'OLT0003',
        'creating_type' => CreatingType::BASE_PLUS_LINKED,
        'species_code' => '0001-FL'
    ];
}, 'BASE+連携');

$factory->define(Product::class, function () {
    return [
        'product_code' => 'OLT0004',
        'creating_type' => CreatingType::MANUAL_CREATED,
        'species_code' => '0002-GL'
    ];
}, '手動登録');

$factory->define(Product::class, function () {
    return [
        'product_code' => 'OLT0005',
        'creating_type' => CreatingType::MANUAL_CREATED,
        'species_code' => '0003-BA'
    ];
}, '手動登録_工場商品登録済用');
